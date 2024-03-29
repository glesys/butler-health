<?php

namespace Butler\Health;

use Closure;
use Composer\InstalledVersions;
use Illuminate\Support\Composer;
use ReflectionFunction;

class Repository
{
    protected static array $customAboutResolvers = [];

    public function __invoke()
    {
        return [
            'about' => $this->gatherAbout(),
            'checks' => $this->gatherChecks(),
        ];
    }

    private function gatherAbout(): array
    {
        $laravel = app();

        $data = [
            'environment' => [
                'applicationName' => config('app.name'),
                'laravelVersion' => $laravel->version(),
                'phpVersion' => phpversion(),
                'composerVersion' => $laravel->make(Composer::class)->getVersion(),
                'environment' => $laravel->environment(),
                'debugMode' => config('app.debug') ? true : false,
                'url' => str(config('app.url'))->replace(['http://', 'https://'], '')->toString(),
                'timezone' => config('app.timezone'),
            ],
            'cache' => [
                'config' => $laravel->configurationIsCached(),
                'events' => $laravel->eventsAreCached(),
                'routes' => $laravel->routesAreCached(),
            ],
            'drivers' => [
                'broadcasting' => config('broadcasting.default'),
                'cache' => config('cache.default'),
                'database' => config('database.default'),
                'logs' => config('logging.default'),
                'mail' => config('mail.default'),
                'octane' => config('octane.server'),
                'queue' => config('queue.default'),
                'session' => config('session.driver'),
            ],
            'butlerHealth' => [
                'version' => ltrim(InstalledVersions::getPrettyVersion('glesys/butler-health'), 'v'),
            ],
            'request' => [
                'ip' => request()->ip(),
                'userAgent' => request()->userAgent(),
            ],
        ];

        $data = array_merge_recursive(
            $data,
            ...collect(static::$customAboutResolvers)->values()->map->__invoke()
        );

        return $data;
    }

    private function gatherChecks(): array
    {
        return collect(config('butler.health.checks', []))
            ->map(fn ($class) => $this->checkToArray(app($class)))
            ->sortByDesc(fn ($check) => $check['result']['order'])
            ->values()
            ->toArray();
    }

    private function checkToArray(Check $check): array
    {
        $name = str($check->name ?? class_basename($check))
            ->kebab()
            ->replace('-', ' ')
            ->title();

        $start = now();

        return [
            'name' => (string) $name,
            'slug' => $check->slug ?? (string) $name->slug(),
            'group' => $check->group ?? 'other',
            'description' => $check->description,
            'result' => $check->run()->toArray(),
            'runtimeInMilliseconds' => (int) $start->diffInMilliseconds(),
        ];
    }

    public static function add(string $section, mixed $data): void
    {
        $id = $data instanceof Closure
            ? md5(new ReflectionFunction($data))
            : md5(serialize($data));

        static::$customAboutResolvers[$section . $id] = fn () => [$section => value($data)];
    }

    public static function clear(): void
    {
        static::$customAboutResolvers = [];
    }
}

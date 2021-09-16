<?php

namespace Butler\Health;

use Butler\Health\Check;
use Closure;
use Composer\InstalledVersions;
use Illuminate\Foundation\Application;
use Illuminate\Support\Str;

class Repository
{
    protected static $customApplicationData;

    public function __invoke()
    {
        return [
            'application' => $this->applicationData(),
            'checks' => $this->checks(),
        ];
    }

    public static function customApplicationData(Closure $callback): void
    {
        static::$customApplicationData = $callback;
    }

    private function applicationData(): array
    {
        $data = [
            'name' => config('app.name'),
            'timezone' => config('app.timezone'),
            'php' => PHP_VERSION,
            'laravel' => Application::VERSION,
            'butlerHealth' => ltrim(InstalledVersions::getPrettyVersion('glesys/butler-health'), 'v'),
        ];

        if (static::$customApplicationData) {
            return array_merge($data, (static::$customApplicationData)());
        }

        return $data;
    }

    private function checks(): array
    {
        return collect(config('butler.health.checks', []))
            ->map(fn ($class) => $this->checkToArray(app($class)))
            ->sortByDesc(fn ($check) => $check['result']['order'])
            ->toArray();
    }

    private function checkToArray(Check $check): array
    {
        $name = Str::of($check->name ?? class_basename($check))
            ->kebab()
            ->replace('-', ' ')
            ->title();

        return [
            'name' => (string) $name,
            'slug' => $check->slug ?? (string) $name->slug(),
            'group' => $check->group ?? 'other',
            'description' => $check->description,
            'result' => $check->run()->toArray(),
        ];
    }
}

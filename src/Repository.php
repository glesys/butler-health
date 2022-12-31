<?php

namespace Butler\Health;

use Illuminate\Support\Facades\Artisan;

class Repository
{
    public function __invoke()
    {
        return [
            'about' => $this->about(),
            'checks' => $this->checks(),
        ];
    }

    private function about(): array
    {
        Artisan::call('about --json');

        $output = Artisan::output();

        return json_decode(trim($output), true);
    }

    private function checks(): array
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
            'runtimeInMilliseconds' => $start->diffInMilliseconds(),
        ];
    }
}

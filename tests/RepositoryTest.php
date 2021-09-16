<?php

namespace Butler\Health\Tests;

use Butler\Health\Repository;
use Illuminate\Testing\Fluent\AssertableJson;

class RepositoryTest extends AbstractTestCase
{
    public function test_returns_correct_data()
    {
        $result = (new Repository())();

        AssertableJson::fromArray($result)
            ->has('application', fn (AssertableJson $json) => $json
                ->where('name', config('app.name'))
                ->where('timezone', config('app.timezone'))
                ->hasAll('php', 'laravel', 'butlerHealth'))
            ->where('checks', [
                [
                    'name' => 'Test Check',
                    'slug' => 'test-check',
                    'group' => 'other',
                    'description' => 'A test check',
                    'result' => [
                        'value' => null,
                        'message' => 'Looking good.',
                        'state' => 'ok',
                        'order' => 1,
                    ],
                ]
            ]);
    }

    public function test_customApplicationData()
    {
        Repository::customApplicationData(fn () => [
            'name' => 'custom name',
            'foo' => 'bar',
        ]);

        $result = (new Repository())();

        AssertableJson::fromArray($result['application'])
            ->where('name', 'custom name')
            ->where('foo', 'bar')
            ->hasAll('timezone', 'php', 'laravel', 'butlerHealth');
    }
}

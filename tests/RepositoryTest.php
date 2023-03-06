<?php

namespace Butler\Health\Tests;

use Butler\Health\Repository;
use Illuminate\Testing\Fluent\AssertableJson;

class RepositoryTest extends AbstractTestCase
{
    public function test_returns_correct_data()
    {
        Repository::add('environment', ['foo' => 'bar']);
        Repository::add('environment', fn () => ['foz' => 'baz']);

        $result = $this->travelTo(now(), function () {
            return (new Repository())();
        });

        AssertableJson::fromArray($result)
            ->has('about', fn (AssertableJson $json) => $json
                ->has('environment', fn (AssertableJson $json) => $json
                    ->hasAll(
                        'application_name',
                        'laravel_version',
                        'php_version',
                        'composer_version',
                        'environment',
                        'debug_mode',
                        'url',
                    )
                    ->where('timezone', config('app.timezone'))
                    ->where('foo', 'bar')
                    ->where('foz', 'baz')
                )
                ->has('cache', fn (AssertableJson $json) => $json
                    ->hasAll('config', 'events', 'routes')
                )
                ->has('drivers', fn (AssertableJson $json) => $json
                    ->hasAll(
                        'broadcasting',
                        'cache',
                        'database',
                        'logs',
                        'mail',
                        'octane',
                        'queue',
                        'session',
                    )
                )
                ->has('butler_health.version')
            )
            ->where('checks', [
                [
                    'name' => 'Test Check',
                    'slug' => 'test-check',
                    'group' => 'other',
                    'description' => 'A test check',
                    'runtimeInMilliseconds' => 100,
                    'result' => [
                        'value' => null,
                        'message' => 'Looking good.',
                        'state' => 'ok',
                        'order' => 1,
                    ],
                ],
            ]);
    }
}

<?php

namespace Butler\Health\Tests;

use Butler\Health\Repository;
use Illuminate\Testing\Fluent\AssertableJson;

class RepositoryTest extends AbstractTestCase
{
    public function test_invoke_returns_correct_default_information()
    {
        $result = $this->travelTo(now(), fn () => (new Repository())());

        AssertableJson::fromArray($result)
            ->has('about', fn (AssertableJson $json) => $json
                ->has('environment', fn (AssertableJson $json) => $json
                    ->hasAll(
                        'applicationName',
                        'laravelVersion',
                        'phpVersion',
                        'composerVersion',
                        'environment',
                        'debugMode',
                        'url',
                    )
                    ->where('timezone', config('app.timezone'))
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
                ->has('butlerHealth.version')
                ->has('request', fn (AssertableJson $json) => $json
                    ->hasAll('ip', 'userAgent')
                )
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

    public function test_add()
    {
        $repository = new Repository();

        $repository::add('environment', ['foo' => 'bar']);
        $repository::add('environment', ['foo' => 'bar']);

        $repository::add('custom', fn () => ['foo' => 'bar']);
        $repository::add('custom', fn () => [
            'foz' => 'bar',
            'baz' => 'foo',
        ]);

        AssertableJson::fromArray($repository())
            ->where('about.environment.foo', 'bar')
            ->where('about.custom', [
                'foo' => 'bar',
                'foz' => 'bar',
                'baz' => 'foo',
            ]);
    }

    public function test_clear()
    {
        $repository = new Repository();

        $repository::add('foo', ['bar' => 'baz']);

        $repository::clear();

        AssertableJson::fromArray($repository())->missing('about.foo');
    }
}

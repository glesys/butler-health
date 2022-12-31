<?php

namespace Butler\Health\Tests;

use Butler\Health\Repository;
use Illuminate\Testing\Fluent\AssertableJson;

class RepositoryTest extends AbstractTestCase
{
    public function test_returns_correct_data()
    {
        $result = $this->travelTo(now(), function () {
            return (new Repository())();
        });

        AssertableJson::fromArray($result)
            ->has('about', fn (AssertableJson $json) => $json
                ->where('environment.timezone', config('app.timezone'))
                ->has('butler_health.version')
                ->etc())
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

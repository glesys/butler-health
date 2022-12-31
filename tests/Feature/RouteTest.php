<?php

namespace Butler\Health\Tests\Feature;

use Butler\Health\Repository;
use Butler\Health\Tests\AbstractTestCase;

class RouteTest extends AbstractTestCase
{
    public function test_happy_path()
    {
        $this->mock(Repository::class, function ($mock) {
            $mock->expects('__invoke')->andReturns(['data']);
        });

        $this->getJson(route('butler-health'))
            ->assertOk()
            ->assertExactJson(['data']);
    }
}

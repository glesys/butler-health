<?php

namespace Butler\Health\Tests;

use GrahamCampbell\TestBenchCore\ServiceProviderTrait;

class ServiceProviderTest extends AbstractTestCase
{
    use ServiceProviderTrait;

    public function test_route_config()
    {
        $this->assertEquals('/health', config('butler.health.route'));
    }

    public function test_checks_config()
    {
        $this->assertEquals([TestCheck::class], config('butler.health.checks'));
    }

    public function test_default_route_is_registered()
    {
        $this->assertTrue(app('router')->has('butler-health'));
    }
}

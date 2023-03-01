<?php

namespace Butler\Health\Tests;

use Butler\Health\ServiceProvider;
use GrahamCampbell\TestBench\AbstractPackageTestCase;

abstract class AbstractTestCase extends AbstractPackageTestCase
{
    protected static function getServiceProviderClass(): string
    {
        return ServiceProvider::class;
    }

    protected function getEnvironmentSetUp($app): void
    {
        parent::getEnvironmentSetUp($app);

        $app['config']->set('butler.health', [
            'route' => '/health',
            'checks' => [
                TestCheck::class,
            ],
            'heartbeat' => [
                'url' => 'http://localhost',
                'token' => 'secret',
            ],
        ]);
    }
}

<?php

namespace Butler\Health\Tests;

use Butler\Health\ServiceProvider;
use GrahamCampbell\TestBench\AbstractPackageTestCase;

abstract class AbstractTestCase extends AbstractPackageTestCase
{
    protected function getServiceProviderClass()
    {
        return ServiceProvider::class;
    }

    protected function getEnvironmentSetUp($app)
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

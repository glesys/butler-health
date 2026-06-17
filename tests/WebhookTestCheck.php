<?php

namespace Butler\Health\Tests;

use Butler\Health\Check;
use Butler\Health\Result;

class WebhookTestCheck extends Check
{
    public string $description = 'A test check with webhooks';

    public array $webhooks = [
        'alerts' => 'https://hooks.example.test/extra',
    ];

    public function run(): Result
    {
        return Result::critical('Something is broken.');
    }
}

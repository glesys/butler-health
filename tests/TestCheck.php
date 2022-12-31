<?php

namespace Butler\Health\Tests;

use Butler\Health\Check;
use Butler\Health\Result;
use Illuminate\Support\Carbon;

class TestCheck extends Check
{
    public string $description = 'A test check';

    public function run(): Result
    {
        Carbon::setTestNow(now()->addMilliseconds(100));

        return Result::ok('Looking good.');
    }
}

<?php

namespace Butler\Health\Tests;

use Butler\Health\Facades\Heartbeat;

class HelpersTest extends AbstractTestCase
{
    public function test_heartbeat()
    {
        Heartbeat::fake();

        heartbeat('foobar');
        heartbeat('foobaz', 5);

        Heartbeat::assertSent('foobar');
        Heartbeat::assertSent('foobaz', 5);
    }
}

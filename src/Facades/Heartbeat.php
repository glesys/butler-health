<?php

namespace Butler\Health\Facades;

use Butler\Health\Heartbeat as HeartbeatClass;
use Illuminate\Support\Facades\Facade;

/**
 * @method static static fake()
 * @method static void send(string $slug, int $minutes = 1)
 * @method static void assertSent(string $slug, int $minutes = 1)
 * @method static void assertSentCount(int $count)
 * @method static void assertNotSent(string $slug, int $minutes = 1)
 * @method static void assertNothingSent()
 * @method static void assertSentCount(int $count)
 *
 * @see \Butler\Health\Heartbeat
 */
class Heartbeat extends Facade
{
    protected static function getFacadeAccessor()
    {
        return HeartbeatClass::class;
    }
}

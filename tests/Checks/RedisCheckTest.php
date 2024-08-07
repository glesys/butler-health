<?php

namespace Butler\Tests\Health;

use Butler\Health\Checks\Redis;
use Butler\Health\Enums\ResultState;
use Butler\Health\Tests\AbstractTestCase;
use Illuminate\Support\Facades\Redis as RedisClient;

class RedisCheckTest extends AbstractTestCase
{
    public function test_unknown_when_redis_extension_is_not_loaded()
    {
        if (extension_loaded('redis')) {
            $this->markTestSkipped();
        }

        $result = (new Redis())->run();

        $this->assertEquals('Redis extension not enabled.', $result->message);
        $this->assertEquals(ResultState::UNKNOWN, $result->state);
        $this->assertNull($result->value());
    }

    public function test_unknown_when_redis_host_is_undefined()
    {
        if (! extension_loaded('redis')) {
            $this->markTestSkipped();
        }

        config(['database.redis.default.host' => null]);

        $result = (new Redis())->run();

        $this->assertEquals('Redis host undefined.', $result->message);
        $this->assertEquals(ResultState::UNKNOWN, $result->state);
        $this->assertNull($result->value());
    }

    public function test_ok()
    {
        if (! extension_loaded('redis')) {
            $this->markTestSkipped();
        }

        config(['database.redis.default.host' => 'localhost']);

        RedisClient::shouldReceive('ping')->once()->andReturnTrue();

        $result = (new Redis())->run();

        $this->assertEquals('Connected to redis on localhost.', $result->message);
        $this->assertEquals(ResultState::OK, $result->state);
        $this->assertNull($result->value());
    }
}

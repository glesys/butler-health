<?php

namespace Butler\Health\Checks;

use Butler\Health\Check;
use Butler\Health\Result;
use Illuminate\Support\Facades\Redis as RedisClient;

class Redis extends Check
{
    public string $group = 'core';
    public string $description = 'Check redis connection.';

    public function run(): Result
    {
        if (! extension_loaded('redis')) {
            return Result::unknown('Redis extension not enabled.');
        }

        if (! $host = config('database.redis.default.host')) {
            return Result::unknown('Redis host undefined.');
        }

        try {
            if (RedisClient::ping()) {
                return Result::ok("Connected to redis on {$host}.");
            }
        } catch (\Exception) {
            //
        }

        return Result::critical("Could not connect to redis on {$host}.");
    }
}

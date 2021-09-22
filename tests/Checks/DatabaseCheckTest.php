<?php

namespace Butler\Tests\Health;

use Butler\Health\Checks\Database;
use Butler\Health\Result;
use Butler\Health\Tests\AbstractTestCase;

class DatabaseCheckTest extends AbstractTestCase
{
    public function test_unknown_when_no_database_connections_exist()
    {
        config(['database.connections' => []]);

        $result = (new Database())->run();

        $this->assertEquals('No database connections found.', $result->message);
        $this->assertEquals(Result::UNKNOWN, $result->state);
        $this->assertNull($result->value());
    }

    public function test_ok_when_single_database_connection_succeeds()
    {
        config([
            'database.connections' => [
                'testing' => [
                    'driver' => 'sqlite',
                    'database' => ':memory:',
                ]
            ]
        ]);

        $result = (new Database())->run();

        $this->assertEquals('Connected to the database.', $result->message);
        $this->assertEquals(Result::OK, $result->state);
        $this->assertEquals(1, $result->value());
    }

    public function test_ok_when_multiple_database_connections_succeeds()
    {
        config([
            'database.connections' => [
                'testing1' => [
                    'driver' => 'sqlite',
                    'database' => ':memory:',
                ],
                'testing2' => [
                    'driver' => 'sqlite',
                    'database' => ':memory:',
                ],
            ]
        ]);

        $result = (new Database())->run();

        $this->assertEquals('Connected to all 2 databases.', $result->message);
        $this->assertEquals(Result::OK, $result->state);
        $this->assertEquals(1, $result->value());
    }

    public function test_critical_when_one_database_connection_fails()
    {
        config(['database.connections' => ['foobar']]);

        $result = (new Database())->run();

        $this->assertEquals('Not connected to the database.', $result->message);
        $this->assertEquals(Result::CRITICAL, $result->state);
        $this->assertEquals(0, $result->value());
    }

    public function test_critical_when_one_of_multiple_database_connections_fails()
    {
        config([
            'database.connections' => [
                'foobar',
                'testing' => [
                    'driver' => 'sqlite',
                    'database' => ':memory:',
                ]
            ]
        ]);

        $result = (new Database())->run();

        $this->assertEquals('Connected to 1 of 2 databases.', $result->message);
        $this->assertEquals(Result::CRITICAL, $result->state);
        $this->assertEquals(0.5, $result->value());
    }
}

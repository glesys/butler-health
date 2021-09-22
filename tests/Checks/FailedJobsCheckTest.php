<?php

namespace Butler\Health\Tests;

use Butler\Health\Checks\FailedJobs;
use Butler\Health\Result;
use Butler\Health\Tests\AbstractTestCase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class FailedJobsCheckTest extends AbstractTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config(['queue.failed.database' => 'sqlite']);

        Schema::create(config('queue.failed.table'), fn ($table) => $table->id());
    }

    public function test_ok_when_no_failed_jobs_exist()
    {
        $result = (new FailedJobs())->run();

        $this->assertEquals('No failed jobs.', $result->message);
        $this->assertEquals(Result::OK, $result->state);
        $this->assertEquals(0, $result->value());
    }

    public function test_critical_when_multiple_failed_jobs_exist()
    {
        DB::table(config('queue.failed.table'))->insert([
            ['id' => 1],
            ['id' => 2],
        ]);

        $result = (new FailedJobs())->run();

        $this->assertEquals('2 failed jobs.', $result->message);
        $this->assertEquals(Result::CRITICAL, $result->state);
        $this->assertEquals(2, $result->value());
    }

    public function test_critical_when_one_failed_job_exist()
    {
        DB::table(config('queue.failed.table'))->insert(['id' => 1]);

        $result = (new FailedJobs())->run();

        $this->assertEquals('One failed job.', $result->message);
        $this->assertEquals(Result::CRITICAL, $result->state);
        $this->assertEquals(1, $result->value());
    }

    public function test_unknown_when_table_dont_exist()
    {
        config(['queue.failed.table' => 'foobar']);

        $result = (new FailedJobs())->run();

        $this->assertEquals('Table foobar not found.', $result->message);
        $this->assertEquals(Result::UNKNOWN, $result->state);
        $this->assertNull($result->value());
    }
}

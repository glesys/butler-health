<?php

namespace Butler\Health\Checks;

use Butler\Health\Check;
use Butler\Health\Result;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class FailedJobs extends Check
{
    public string $group = 'core';
    public string $name = 'Failed jobs';
    public string $description = 'Check if there are failed jobs.';

    public function run(): Result
    {
        $connection = config('queue.failed.database');
        $tableName = config('queue.failed.table');

        try {
            throw_unless(Schema::connection($connection)->hasTable($tableName));
        } catch (Exception) {
            return Result::unknown("Table {$tableName} not found.");
        }

        if ($count = DB::connection($connection)->table($tableName)->count()) {
            $message = $count > 1 ? "$count failed jobs." : 'One failed job.';

            return tap(Result::critical($message))->value($count);
        }

        return tap(Result::ok('No failed jobs.'))->value(0);
    }
}

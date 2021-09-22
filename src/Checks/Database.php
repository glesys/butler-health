<?php

namespace Butler\Health\Checks;

use Butler\Health\Check;
use Butler\Health\Result;
use Illuminate\Support\Facades\DB;

class Database extends Check
{
    public string $group = 'core';
    public string $description = 'Check all database connections.';

    public function run(): Result
    {
        $connectedDatabases = 0;
        $checkedDatabases = 0;
        $connectionKeys = collect(config('database.connections'))->keys();

        if ($connectionKeys->isEmpty()) {
            return Result::unknown('No database connections found.');
        }

        foreach ($connectionKeys->all() as $connection) {
            ++$checkedDatabases;

            try {
                if (DB::connection($connection)->getPdo()) {
                    ++$connectedDatabases;
                }
            } catch (\Exception) {
                //
            }
        }

        if ($connectedDatabases === $checkedDatabases) {
            $message = $checkedDatabases > 1
                ? "Connected to all $checkedDatabases databases."
                : 'Connected to the database.';

            $result = Result::ok($message);
        } else {
            $message = $checkedDatabases > 1
                ? "Connected to $connectedDatabases of $checkedDatabases databases."
                : 'Not connected to the database.';

            $result = Result::critical($message);
        }

        return tap($result)->value($connectedDatabases / $checkedDatabases);
    }
}

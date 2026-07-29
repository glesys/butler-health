<?php

use Butler\Health\Facades\Heartbeat;

if (! function_exists('heartbeat')) {
    function heartbeat(string $slug, int $minutes = 1): void
    {
        Heartbeat::send($slug, $minutes);
    }
}

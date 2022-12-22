<?php

if (! function_exists('heartbeat')) {
    function heartbeat(string $slug, int $minutes = 1): void
    {
        Butler\Health\Facades\Heartbeat::send($slug, $minutes);
    }
}

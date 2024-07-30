<?php

return [

    'health' => [

        'route' => '/health',

        'checks' => [],

        'heartbeat' => [
            'driver' => env('BUTLER_HEALTH_HEARTBEAT_DRIVER', 'http'),
            'url' => env('BUTLER_HEALTH_HEARTBEAT_URL'),
            'token' => env('BUTLER_HEALTH_HEARTBEAT_TOKEN'),
            'report' => env('BUTLER_HEALTH_HEARTBEAT_REPORT'),
            'timeout' => env('BUTLER_HEALTH_HEARTBEAT_TIMEOUT'),
        ],

    ],

];

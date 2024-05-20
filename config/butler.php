<?php

return [

    'health' => [

        'route' => '/health',

        'checks' => [],

        'heartbeat' => [
            'url' => env('BUTLER_HEALTH_HEARTBEAT_URL'),
            'token' => env('BUTLER_HEALTH_HEARTBEAT_TOKEN'),
            'report' => env('BUTLER_HEALTH_HEARTBEAT_REPORT'),
        ],

    ],

];

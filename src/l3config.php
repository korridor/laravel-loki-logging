<?php
return [
    'context' => [
        'application' => env('LOG_APP', env('APP_NAME')),
        'type' => '{level_name}'
    ],
    'format' => env('LOG_FORMAT', '[{level_name}] {message}'),
    'loki' => [
        'server' => env('LOG_SERVER', 'https://logging.app'),
        'username' => env('LOG_USERNAME', null),
        'password' => env('LOG_PASSWORD', null),
        'tenant_id' => env('LOG_LOKI_TENANT_ID', null),
    ],
];

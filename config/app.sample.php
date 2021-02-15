<?php

return [
    'useRestrictIpAddress' => true,
    'allowedIpAddresses' => [
        '::1',
        '127.0.0.1'
    ],
    'instances' => [
        [
            'host' => 'host',
            'port' => 3306,
            'user' => 'user',
            'password' => 'password'
        ],
    ]
];
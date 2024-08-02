<?php

return [
    'default' => 'fcm',

    'channels' => [
        'fcm' => [
            'project' => env('GOOGLE_PROJECT'),
            'service_account' => env('GOOGLE_SERVICE_ACCOUNT'),
        ],
    ],
];

<?php

return [
    'disks' => [
        'minio' => [
            'driver' => 'minio',
            'key' => env('MINIO_ACCESS_KEY_ID', env('AWS_ACCESS_KEY_ID')),  // required
            'secret' => env('MINIO_SECRET_ACCESS_KEY', env('AWS_SECRET_ACCESS_KEY')),   // required
            'region' => env('MINIO_DEFAULT_REGION', env('AWS_DEFAULT_REGION', 'ap-south-1')), // required
            'bucket' => env('MINIO_BUCKET', env('AWS_BUCKET')), // required
            'url' => env('MINIO_URL', env('AWS_URL')),
            'endpoint' => env('MINIO_ENDPOINT', env('AWS_ENDPOINT')),   // required
            'use_path_style_endpoint' => env('MINIO_USE_PATH_STYLE_ENDPOINT', env('AWS_USE_PATH_STYLE_ENDPOINT', true)),
            'visibility' => env('MINIO_VISIBILITY', env('AWS_VISIBILITY', 'private')),
            'throw' => env('MINIO_THROW', env('AWS_THROW', false)),
        ],
    ],
];

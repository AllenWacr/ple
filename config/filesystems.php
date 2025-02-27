<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Filesystem Disk
    |--------------------------------------------------------------------------
    |
    | Here you may specify the default filesystem disk that should be used
    | by the framework. The "local" disk, as well as a variety of cloud
    | based disks are available to your application. Just store away!
    |
    */

    'default' => env('FILESYSTEM_DISK', 'local'),

    /*
    |--------------------------------------------------------------------------
    | Filesystem Disks
    |--------------------------------------------------------------------------
    |
    | Here you may configure as many filesystem "disks" as you wish, and you
    | may even configure multiple disks of the same driver. Defaults have
    | been set up for each driver as an example of the required values.
    |
    | Supported Drivers: "local", "ftp", "sftp", "s3"
    |
    */

    'disks' => [

        'local' => [
            'driver' => 'local',
            'root' => storage_path('app'),
            'throw' => false,
        ],

        'public' => [
            'driver' => 'local',
            'root' => storage_path('app/public'),
            'url' => env('APP_URL').'/storage',
            'visibility' => 'public',
            'throw' => false,
        ],

        's3' => [
            'driver' => 's3',
            'key' => env('AWS_ACCESS_KEY_ID'),
            'secret' => env('AWS_SECRET_ACCESS_KEY'),
            'region' => env('AWS_DEFAULT_REGION'),
            'bucket' => env('AWS_BUCKET'),
            'url' => env('AWS_URL'),
            'endpoint' => env('AWS_ENDPOINT'),
            'use_path_style_endpoint' => env('AWS_USE_PATH_STYLE_ENDPOINT', false),
            'throw' => false,
        ],
        'media' => [
            'driver' => 'local',
            'root'   => public_path('/media'),
            'url'    => env('APP_URL').'/media',
        ],
        'form' => [
            'driver' => 'local',
            'root'   => public_path('/media/form'),
            'url'    => env('APP_URL').'/media/form',
            // 'permissions' =>[
            //     'file'=>[
            //         'public'=> 0774,
            //         'private'=>0600,
            //     ],
            //     'dir'=>[
            //         'public'=>0775,
            //         'private'=>0775,
            //     ]
            // ]
        ],
        'competition' => [
            'driver' => 'local',
            'root'   => public_path('/media/competition'),
            'url'    => env('APP_URL').'/media/competition',
        ],

        'certificate' => [
            'driver' => 'local',
            'root'   => public_path('/media/certificate'),
            'url'    => env('APP_URL').'/media/certificate',
        ],
        'article' => [
            'driver' => 'local',
            'root'   => public_path('/media/article'),
            'url'    => env('APP_URL').'/media/article',
        ],
        'file' => [
            'driver' => 'local',
            'root'   => public_path('media/file'),
            'url'    => env('APP_URL').'/media/file',
        ],
        'image' => [
            'driver' => 'local',
            'root'   => public_path('/media/image'),
            'url'    => env('APP_URL').'/media/image',
        ]


    ],

    /*
    |--------------------------------------------------------------------------
    | Symbolic Links
    |--------------------------------------------------------------------------
    |
    | Here you may configure the symbolic links that will be created when the
    | `storage:link` Artisan command is executed. The array keys should be
    | the locations of the links and the values should be their targets.
    |
    */

    'links' => [
        public_path('storage') => storage_path('app/public'),
    ],

];

<?php

return [
    'auth' => [
        'guard' => env('FILAMENT_AUTH_GUARD', 'web'),
        'pages' => [
            'login' => \App\Filament\Auth\CustomLogin::class,
        ],
    ],
    'default_filesystem_disk' => env('FILAMENT_FILESYSTEM_DISK', 'public'),
    'default_locale' => 'lo',
    'locales' => [
        'lo' => 'ລາວ',
        'en' => 'English',
    ],
    'path' => env('FILAMENT_PATH', 'admin'),
    'domain' => env('FILAMENT_DOMAIN'),
    'home_url' => '/',
    'brand' => env('APP_NAME'),
    'favicon' => null,
    'theme' => [
        'preload_css' => true,
    ],
];
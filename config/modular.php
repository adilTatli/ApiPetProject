<?php

/**
 * Конфигурационный файл для модульной структуры приложения.
 *
 * Этот файл содержит настройки для модульной структуры Laravel-приложения.
 * Здесь определены пути, пространства имен, middleware и списки модулей для разных групп.
 *
 * @return array
 */
return [
    'path' => base_path() . '/app/Modules',
    'base_namespace' => 'App\Modules',
    'groupWithoutPrefix' => 'Pub',

    'groupMiddleware' => [
        'Admin' => [
            'web' => ['auth'],
            'api' => ['auth:api'],
        ]
    ],

    'modules' => [
        'Admin' => [
            'Role',
            'Menu',
            'Dashboard',
            'User'
        ],

        'Pub' => [
            'Auth'
        ],
    ]
];

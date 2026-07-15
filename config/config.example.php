<?php

return [
    'db' => [
        'host'    => 'localhost',
        'name'    => 'chefmate_db',
        'user'    => 'root',
        'pass'    => '',
        'charset' => 'utf8mb4',
    ],

    'app' => [
        
        'root_path' => dirname(__DIR__),
        
        'session_lifetime' => 0,

        'env' => 'development',
    ],

    'flask' => [
        'base_url' => 'http://127.0.0.1:5000',
        'timeout'  => 15,
    ],
];

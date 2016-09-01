<?php

return [
    'my_database' => [
        'dsn' => 'pgsql:host=localhost;port=5432;dbname=testdb',
        'user' => 'my-database_user',
        'password' => 'my_database_password',
        'options' => [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ],
        'migrations' => '/path/to/migrations'
    ],
    'another-database' => [
        'dsn' => 'pgsql:host=localhost;port=5432;dbname=testdb',
        'user' => 'another_database_user',
        'password' => 'another_database_password',
        'options' => [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ],
        'migrations' => '/path/to/migrations'
    ],
];

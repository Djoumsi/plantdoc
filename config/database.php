<?php
return [
    'host'    => Env::get('DB_HOST', 'localhost'),
    'port'    => Env::get('DB_PORT', '3306'),
    'name'    => Env::get('DB_NAME', 'plantdoc'),
    'user'    => Env::get('DB_USER', 'root'),
    'pass'    => Env::get('DB_PASS', ''),
    'charset' => 'utf8mb4',
    'options' => [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ],
];

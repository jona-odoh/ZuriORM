<?php

return [
    'base_url' => 'http://localhost/screening',
    'default' => 'mysql', // Default database connection
    'connections' => [
        'mysql' => [
            'driver'    => 'mysql', // Database driver
            'host'      => 'localhost', // Database host
            'database'  => 'testing', // Database name
            'username'  => 'root', // Database username
            'password'  => '', // Database password
            'charset'   => 'utf8', // Database charset
            'collation' => 'utf8_unicode_ci', // Database collation
            'prefix'    => '', // Table prefix (if any)
        ],
        // Additional database connections can be added here
    ],
];

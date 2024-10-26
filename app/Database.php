<?php
namespace App;

use PDO;
use PDOException; // Make sure this is correctly imported

class Database
{
    protected static $connection;

    // Establishes a database connection using the provided configuration
    public static function connect($config)
{
    if (self::$connection === null) {
        if (!isset($config['default'], $config['connections'][$config['default']])) {
            throw new PDOException('Invalid database configuration.');
        }

        $dbConfig = $config['connections'][$config['default']];

        // Validate required fields
        if (!isset($dbConfig['driver'], $dbConfig['host'], $dbConfig['database'], $dbConfig['username'], $dbConfig['password'])) {
            throw new PDOException('Database configuration is incomplete.');
        }

        $dsn = "{$dbConfig['driver']}:host={$dbConfig['host']};dbname={$dbConfig['database']};charset={$dbConfig['charset']}";

        try {
            self::$connection = new PDO($dsn, $dbConfig['username'], $dbConfig['password']);
            self::$connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            error_log('Database connection failed: ' . $e->getMessage());
            throw new PDOException('Database connection failed: ' . $e->getMessage());
        }
    }

    return self::$connection;
}

}

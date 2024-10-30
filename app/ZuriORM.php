<?php

namespace App;

use PDO;
use PDOException;

class ZuriORM
{
    private static $connection;
    private $statement;


    public function __construct($host, $dbname, $username, $password)
    {
        $this->connect($host, $dbname, $username, $password);
    }

    public function connect($host, $dbname, $username, $password)
    {
        try {
            self::$connection = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
            self::$connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("Connection failed: " . $e->getMessage());
        }
    }

    public function closeConnection()
    {
        self::$connection = null;
    }

    public function getConnectionStatus()
    {
        return self::$connection ? "Connected" : "Not connected";
    }
}

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

   
    public function create($table, array $data)
    {
        $columns = implode(',', array_keys($data));
        $placeholders = implode(',', array_map(fn($v) => ":$v", array_keys($data)));
        $sql = "INSERT INTO $table ($columns) VALUES ($placeholders)";
        $statement = self::$connection->prepare($sql);
        $statement->execute($data);
        return self::$connection->lastInsertId();
    }
    public function read($table, $conditions = [])
    {
        $where = $this->buildWhereClause($conditions);
        $sql = "SELECT * FROM $table $where";
        $statement = self::$connection->prepare($sql);
        $statement->execute(array_values($conditions));
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    // On finish work here (Clause)

    private function buildWhereClause($conditions)
    {
    }
}

<?php

namespace App;

use PDO;
use PDOException;
use PDOStatement;

class ZuriORM
{
    private static ?PDO $connection = null;
    private ?PDOStatement $statement = null;


    public function __construct($host, $dbname, $username, $password)
    {
        $this->connect($host, $dbname, $username, $password);
    }

    public function connect(
        string $host,
        string $dbname,
        string $username,
        string $password
    ): void

    {
        try {
            self::$connection = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
            self::$connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("Connection failed: " . $e->getMessage());
        }
    }

    public function closeConnection(): void
    {
        self::$connection = null;
    }

    public function getConnectionStatus(): string
    {
        return self::$connection ? "Connected" : "Not connected";
    }


    public function create(string $table, array $data): int
    {
        $columns = implode(',', array_keys($data));
        $placeholders = implode(',', array_map(fn($v) => ":$v", array_keys($data)));
        $sql = "INSERT INTO $table ($columns) VALUES ($placeholders)";
        $statement = self::$connection->prepare($sql);
        $statement->execute($data);
        return self::$connection->lastInsertId();
    }
    public function read(string $table, array $conditions = []): array
    {
        $where = $this->buildWhereClause($conditions);
        $sql = "SELECT * FROM $table $where";
        $statement = self::$connection->prepare($sql);
        $statement->execute(array_values($conditions));
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }
    public function update($table, array $data, $conditions)
    {
        $set = implode(', ', array_map(fn($k) => "$k = :$k", array_keys($data)));
        $where = $this->buildWhereClause($conditions);
        $sql = "UPDATE $table SET $set $where";
        $statement = self::$connection->prepare($sql);
        $statement->execute(array_merge($data, $conditions));
        return $statement->rowCount();
    }

    // On finish work here (Clause)

    private function buildWhereClause(array $conditions): string
    {
        if (empty($conditions)) {
            return '';
        }

        $clauses = [];
        foreach ($conditions as $key => $value) {
            $clauses[] = "$key = :$key";
        }
        return 'WHERE ' . implode(' AND ', $clauses);
    }
}

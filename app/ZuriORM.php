<?php

namespace App;

use PDO;
use PDOException;

class ZuriORM
{
    protected static $connection;
    protected $table;
    protected $selects = '*';
    protected $joins = [];
    protected $wheres = [];
    protected $bindings = [];
    protected $order;
    protected $groupBy;
    protected $having;
    protected $limit;
    protected $offset;
    protected $softDelete = false;
    protected $relationships = [];
    protected $cache = [];

    public function __construct()
    {
        $this->table = strtolower((new \ReflectionClass($this))->getShortName()) . 's';
    }

    public static function connect(array $config)
    {
        try {
            self::$connection = new PDO($config['dsn'], $config['user'], $config['password']);
            self::$connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("Connection failed: " . $e->getMessage());
        }
    }

    public static function closeConnection()
    {
        self::$connection = null;
    }

    public static function getConnectionStatus()
    {
        return self::$connection ? 'Connected' : 'Disconnected';
    }

    public function insert(array $data)
    {
        $fields = implode(', ', array_keys($data));
        $placeholders = ':' . implode(', :', array_keys($data));
        $sql = "INSERT INTO {$this->table} ({$fields}) VALUES ({$placeholders})";

        $statement = self::$connection->prepare($sql);
        foreach ($data as $key => $value) {
            $statement->bindValue(":$key", $value);
        }

        return $statement->execute();
    }
    public function select(...$columns)
    {
        $this->selects = implode(', ', $columns);
        return $this;
    }
    public function update(array $data)
    {
        $fields = '';
        foreach ($data as $key => $value) {
            $fields .= "{$key} = :{$key}, ";
            $this->bindings[$key] = $value;
        }
        $fields = rtrim($fields, ', ');

        $sql = "UPDATE {$this->table} SET {$fields} {$this->getWhereClause()}";
        $statement = self::$connection->prepare($sql);

        foreach ($this->bindings as $key => $value) {
            $statement->bindValue(":$key", $value);
        }

        return $statement->execute();
    }
    public function delete()
    {
        $sql = "DELETE FROM {$this->table} {$this->getWhereClause()}";
        $statement = self::$connection->prepare($sql);

        foreach ($this->bindings as $key => $value) {
            $statement->bindValue(":$key", $value);
        }

        return $statement->execute();
    }
    public function where(string $column, string $operator, $value)
    {
        $this->wheres[] = "{$column} {$operator} :{$column}";
        $this->bindings[$column] = $value;
        return $this;
    }

    public function orWhere(string $column, string $operator, $value)
    {
        $this->wheres[] = "OR {$column} {$operator} :{$column}";
        $this->bindings[$column] = $value;
        return $this;
    }

    public function orderBy(string $column, string $direction = 'ASC')
    {
        $this->order = "ORDER BY {$column} {$direction}";
        return $this;
    }

    public function groupBy(string $column)
    {
        $this->groupBy = "GROUP BY {$column}";
        return $this;
    }

    public function having(string $column, string $operator, $value)
    {
        $this->having = "HAVING {$column} {$operator} :havingValue";
        $this->bindings['havingValue'] = $value;
        return $this;
    }






}

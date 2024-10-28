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

        $stmt = self::$connection->prepare($sql);
        foreach ($data as $key => $value) {
            $stmt->bindValue(":$key", $value);
        }

        return $stmt->execute();
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
        $stmt = self::$connection->prepare($sql);

        foreach ($this->bindings as $key => $value) {
            $stmt->bindValue(":$key", $value);
        }

        return $stmt->execute();
    }

    public function delete()
    {
        $sql = "DELETE FROM {$this->table} {$this->getWhereClause()}";
        $stmt = self::$connection->prepare($sql);

        foreach ($this->bindings as $key => $value) {
            $stmt->bindValue(":$key", $value);
        }

        return $stmt->execute();
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

    public function limit(int $limit)
    {
        $this->limit = "LIMIT {$limit}";
        return $this;
    }

    public function offset(int $offset)
    {
        $this->offset = "OFFSET {$offset}";
        return $this;
    }

    public function join(string $type, string $table, string $first, string $operator, string $second)
    {
        $this->joins[] = strtoupper($type) . " JOIN {$table} ON {$first} {$operator} {$second}";
        return $this;
    }

    public function count($column = '*')
    {
        $this->selects = "COUNT({$column})";
        return $this->executeSelect();
    }

    public function sum(string $column)
    {
        $this->selects = "SUM({$column})";
        return $this->executeSelect();
    }

    public function avg(string $column)
    {
        $this->selects = "AVG({$column})";
        return $this->executeSelect();
    }

    public function max(string $column)
    {
        $this->selects = "MAX({$column})";
        return $this->executeSelect();
    }

    public function min(string $column)
    {
        $this->selects = "MIN({$column})";
        return $this->executeSelect();
    }

    public static function beginTransaction()
    {
        self::$connection->beginTransaction();
    }

    public static function commitTransaction()
    {
        self::$connection->commit();
    }

    public static function rollbackTransaction()
    {
        self::$connection->rollBack();
    }

    public function rawQuery(string $query, array $params = [])
    {
        $stmt = self::$connection->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    protected function executeSelect()
    {
        $sql = "SELECT {$this->selects} FROM {$this->table} {$this->getJoinClause()} {$this->getWhereClause()} {$this->groupBy} {$this->having} {$this->order} {$this->limit} {$this->offset}";
        $stmt = self::$connection->prepare($sql);

        foreach ($this->bindings as $key => $value) {
            $stmt->bindValue(":$key", $value);
        }

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    protected function getWhereClause()
    {
        return $this->wheres ? 'WHERE ' . implode(' ', $this->wheres) : '';
    }

    protected function getJoinClause()
    {
        return implode(' ', $this->joins);
    }
}

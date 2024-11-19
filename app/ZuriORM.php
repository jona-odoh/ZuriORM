<?php

namespace App;

use Exception;
use PDO;
use PDOException;
use PDOStatement;

class ZuriORM
{
    private static ?PDO $connection = null;
    private ?PDOStatement $statement = null;
    protected array $bindings = [];
    private $selects = '*';
    private $table;
    private $wheres = [];
    private $limit = '';
    private $offset = '';
    private $orderBy = '';
    private $groupBy = '';
    private $joins = '';


    public function __construct()
    {
        $config = require 'config/database.php';
        $this->connect($config['host'], $config['dbname'], $config['username'], $config['password']);
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
    public function setTable($table): self
    {
        $this->table = $table;
        return $this;
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
    public function delete($table, $conditions)
    {
        $where = $this->buildWhereClause($conditions);
        $sql = "DELETE FROM $table $where";
        $statement = self::$connection->prepare($sql);
        $statement->execute(array_values($conditions));
        return $statement->rowCount();
    }
    public function select($columns)
    {
        $this->selects = is_array($columns) ? implode(',', $columns) : $columns;
        return $this;
    }
    public function where($column, $operator, $value)
    {
        $this->wheres[] = "$column $operator ?";
        $this->bindings[] = $value;
        return $this;
    }
    public function andWhere($column, $operator, $value)
    {
        $this->wheres[] = "AND $column $operator ?";
        $this->bindings[] = $value;
        return $this;
    }
    public function orWhere($column, $operator, $value)
    {
        $this->wheres[] = "OR $column $operator ?";
        $this->bindings[] = $value;
        return $this;
    }
    public function orderBy($column, $direction = 'ASC')
    {
        $this->orderBy = "ORDER BY $column $direction";
        return $this;
    }
    public function groupBy($column)
    {
        $this->groupBy = "GROUP BY $column";
        return $this;
    }
    public function limit($limit)
    {
        $this->limit = "LIMIT $limit";
        return $this;
    }
    public function offset($offset)
    {
        $this->offset = "OFFSET $offset";
        return $this;
    }
    public function join($type, $table, $on)
    {
        $this->joins .= "$type JOIN $table ON $on ";
        return $this;
    }
    private function buildWhereClause($conditions)
    {
        return $conditions ? "WHERE " . implode(' AND ', array_map(fn($k) => "$k = ?", array_keys($conditions))) : '';
    }
    public function count($column = '*')
    {
        return $this->aggregate("COUNT($column)");
    }
    public function sum($column)
    {
        return $this->aggregate("SUM($column)");
    }
    public function avg($column)
    {
        return $this->aggregate("AVG($column)");
    }
    public function max($column)
    {
        return $this->aggregate("MAX($column)");
    }
    public function min($column)
    {
        return $this->aggregate("MIN($column)");
    }
    private function aggregate($function)
    {
        $sql = "SELECT $function FROM {$this->table} {$this->joins} ";
        if ($this->wheres) {
            $sql .= 'WHERE ' . implode(' ', $this->wheres);
        }
        $statement = self::$connection->prepare($sql);
        $statement->execute($this->bindings);
        return $statement->fetchColumn();
    }
    public function beginTransaction()
    {
        self::$connection->beginTransaction();
    }
    public function commit()
    {
        self::$connection->commit();
    }
    public function rollback()
    {
        self::$connection->rollBack();
    }
    public function hasOne(string $relatedClass, string $foreignKey = null, string $localKey = 'id')
    {
        $relatedTable = strtolower($relatedClass) . "s";
        $foreignKey = $foreignKey ?? strtolower($this->table) . "_id";
        if (!isset($this->$localKey)) {
            throw new Exception("Local key '$localKey' not found in the current object.");
        }
        $query = "SELECT * FROM {$relatedTable} WHERE $foreignKey = :localKey LIMIT 1";
        $statement = self::$connection->prepare($query);
        $statement->execute([':localKey' => $this->$localKey]);
        return $statement->fetch(PDO::FETCH_OBJ);
    }
    public function hasMany(string $relatedClass, string $foreignKey = null, string $localKey = 'id'): array
    {
        $relatedTable = strtolower($relatedClass) . "s";
        $foreignKey = $foreignKey ?? strtolower($this->table) . "_id";
        $query = "SELECT * FROM {$relatedTable} WHERE $foreignKey = :localKey";
        $statement = self::$connection->prepare($query);
        $statement->execute([':localKey' => $this->$localKey]);
        return $statement->fetchAll(PDO::FETCH_OBJ);
    }
    /**
     * @throws Exception
     */
    public function execute(): array
    {
        $sql = "SELECT {$this->selects} FROM {$this->table} {$this->joins}";
        if ($this->wheres) {
            $sql .= ' WHERE ' . implode(' AND ', $this->wheres);
        }
        if ($this->orderBy) {
            $sql .= " {$this->orderBy}";
        }
        if ($this->groupBy) {
            $sql .= " {$this->groupBy}";
        }
        if ($this->limit) {
            $sql .= " {$this->limit}";
        }
        if ($this->offset) {
            $sql .= " {$this->offset}";
        }
        try {
            $statement = self::$connection->prepare($sql);
            $statement->execute($this->bindings);
            return $statement->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Database query failed: " . $e->getMessage());
        }
    }
    public function softDelete($table, $conditions)
    {
        $data = ['deleted_at' => date('Y-m-d H:i:s')];
        return $this->update($table, $data, $conditions);
    }
    public function paginate($perPage, $currentPage)
    {
        $offset = ($currentPage - 1) * $perPage;
        $this->limit($perPage)->offset($offset);
        return $this->execute();
    }
    public function scopeActive()
    {
        return $this->where('status', '=', 'active');
    }



}

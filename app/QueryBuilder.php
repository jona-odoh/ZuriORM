<?php
namespace App;

use PDO;

class QueryBuilder
{
    protected $connection;
    protected $table;
    protected $selects = '*';
    protected $joins = [];
    protected $wheres = [];
    protected $bindings = [];
    protected $order;
    protected $limit;
    protected $offset;
    protected $softDelete = false;
    protected $eagerLoad = [];

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }

    // Sets the table for the query
    public function table(string $table)
    {
        $this->table = $table;
        return $this;
    }

    // Specifies the fields to select in the query
    public function select(...$fields)
    {
        $this->selects = implode(', ', $fields);
        return $this;
    }

    // Adds a WHERE clause to the query
    public function where(string $field, string $operator, $value)
    {
        $this->wheres[] = "{$field} {$operator} :{$field}";
        $this->bindings[$field] = $value;
        return $this;
    }

    // Adds an ORDER BY clause to the query
    public function orderBy(string $field, string $direction = 'ASC')
    {
        $this->order = "ORDER BY {$field} {$direction}";
        return $this;
    }

    // Adds a LIMIT clause to the query
    public function limit(int $limit)
    {
        $this->limit = "LIMIT {$limit}";
        return $this;
    }

    // Adds an OFFSET clause to the query
    public function offset(int $offset)
    {
        $this->offset = "OFFSET {$offset}";
        return $this;
    }

    // Adds a JOIN clause to the query
    public function join(string $table, string $localKey, string $operator, string $foreignKey, string $type = 'INNER')
    {
        $this->joins[] = "{$type} JOIN {$table} ON {$localKey} {$operator} {$foreignKey}";
        return $this;
    }

    // Adds a LEFT JOIN clause to the query
    public function leftJoin(string $table, string $localKey, string $operator, string $foreignKey)
    {
        return $this->join($table, $localKey, $operator, $foreignKey, 'LEFT');
    }

    // Adds a RIGHT JOIN clause to the query
    public function rightJoin(string $table, string $localKey, string $operator, string $foreignKey)
    {
        return $this->join($table, $localKey, $operator, $foreignKey, 'RIGHT');
    }

    // Specifies relationships to eager load
    public function with(array $relations)
    {
        $this->eagerLoad = $relations;
        return $this;
    }

    // Executes the query and retrieves results
    public function get()
    {
        $sql = "SELECT {$this->selects} FROM {$this->table}";

        if ($this->softDelete) {
            $this->whereNull('deleted_at');
        }

        if ($this->joins) {
            $sql .= ' ' . implode(' ', $this->joins);
        }

        if ($this->wheres) {
            $sql .= " WHERE " . implode(' AND ', $this->wheres);
        }

        if ($this->order) {
            $sql .= " {$this->order}";
        }

        if ($this->limit) {
            $sql .= " {$this->limit}";
        }

        if ($this->offset) {
            $sql .= " {$this->offset}";
        }

        $stmt = $this->connection->prepare($sql);
        $stmt->execute($this->bindings);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ($this->eagerLoad) {
            $results = $this->loadRelations($results);
        }

        return $results;
    }

    // Loads related models for eager loading
    protected function loadRelations(array $results)
    {
        foreach ($this->eagerLoad as $relation) {
            $relatedData = [];
            foreach ($results as $index => $result) {
                $relatedModel = $relation['model'];
                $foreignKey = $relation['foreignKey'];
                $localKey = $relation['localKey'];

                $relatedData[$index] = (new $relatedModel($this->connection))
                    ->where($foreignKey, '=', $result[$localKey])
                    ->get();
            }

            foreach ($results as $index => $result) {
                $results[$index][$relation['name']] = $relatedData[$index];
            }
        }

        return $results;
    }

    // Deletes records with optional soft delete
    public function delete()
    {
        if ($this->softDelete) {
            $sql = "UPDATE {$this->table} SET deleted_at = NOW()";
        } else {
            $sql = "DELETE FROM {$this->table}";
        }

        if ($this->wheres) {
            $sql .= " WHERE " . implode(' AND ', $this->wheres);
        }

        $stmt = $this->connection->prepare($sql);
        return $stmt->execute($this->bindings);
    }

    // Adds a WHERE IS NULL clause
    public function whereNull(string $field)
    {
        $this->wheres[] = "{$field} IS NULL";
        return $this;
    }

    // Adds a WHERE IS NOT NULL clause
    public function whereNotNull(string $field)
    {
        $this->wheres[] = "{$field} IS NOT NULL";
        return $this;
    }
}

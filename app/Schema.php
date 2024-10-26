<?php
namespace App;

class Schema
{
    protected $connection;

    public function __construct($connection)
    {
        $this->connection = $connection;
    }

    // Creates a new table using the given callback
    public function create($table, callable $callback)
    {
        $blueprint = new Blueprint($table);
        $callback($blueprint);
        $sql = $blueprint->toSql();
        return $this->connection->exec($sql);
    }

    // Drops an existing table
    public function drop($table)
    {
        $sql = "DROP TABLE IF EXISTS {$table}";
        return $this->connection->exec($sql);
    }
}

class Blueprint
{
    protected $table;
    protected $columns = [];

    public function __construct($table)
    {
        $this->table = $table;
    }

    // Adds an AUTO_INCREMENT primary key column
    public function increments($column)
    {
        $this->columns[] = "{$column} INT AUTO_INCREMENT PRIMARY KEY";
    }

    // Adds a VARCHAR column
    public function string($column, $length = 255)
    {
        $this->columns[] = "{$column} VARCHAR({$length})";
    }

    // Adds created_at and updated_at timestamp columns
    public function timestamps()
    {
        $this->columns[] = "created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP";
        $this->columns[] = "updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP";
    }

    // Generates the SQL statement for table creation
    public function toSql()
    {
        $columns = implode(", ", $this->columns);
        return "CREATE TABLE {$this->table} ({$columns})";
    }
}

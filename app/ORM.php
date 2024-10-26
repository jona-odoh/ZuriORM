<?php
namespace App;

class ORM
{
    protected static $connection;

    // Sets the database connection for the ORM
    public static function setConnection($connection)
    {
        self::$connection = $connection;
    }

    // Provides a QueryBuilder instance for interacting with a specific table
    public static function table($table)
    {
        $queryBuilder = new QueryBuilder(self::$connection);
        return $queryBuilder->table($table);
    }

    // Validates data against specified rules
    public static function validate(array $data, array $rules)
    {
        $validator = new Validator();
        return $validator->validate($data, $rules);
    }

    // Returns validation errors
    public static function errors()
    {
        $validator = new Validator();
        return $validator->errors();
    }

    // Provides schema operations like creating or dropping tables
    public static function schema()
    {
        return new Schema(self::$connection);
    }
}

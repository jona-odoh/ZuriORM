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


}

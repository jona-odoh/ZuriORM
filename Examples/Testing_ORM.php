<?php

use App\ZuriORM;


// handling database connection, disconnection, and status checking.
$db = new ZuriORM("localhost", "my_database", "username", "password");
echo $db->getConnectionStatus();

// Disconnect connection
$db->closeConnection();
echo $db->getConnectionStatus();


// CRUD Operations
$db = new ZuriORM("localhost", "my_database", "username", "password");

// Insert record
$id = $db->create("users", ["name" => "Jonathan Odoh", "email" => "jonathanodoh3140@gmail.com"]);

// Select records
$users = $db->read("users", ["status" => "active"]);

// Update record
$updatedRows = $db->update("users", ["email" => "jonathanodoh3140@gmail.com"], ["id" => $id]);

// Delete record
$deletedRows = $db->delete("users", ["id" => $id]);

// WHERE
$results = $db->where('status', '=', 'active')
    ->andWhere('age', '>', 21);


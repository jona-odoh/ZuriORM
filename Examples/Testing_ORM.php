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

$results = $db->Where('status', '=', 'active')
    ->orWhere('role', '=', 'admin');



//$db = new ZuriORM("localhost", "database_name", "username", "password");

// Aggregate functions
$totalUsers = $db->table("users")->count();
$averageAge = $db->table("users")->avg("age");

// one-to-one relationship
$user = new User();
$user->id = 1; // User with ID 1

// Fetch the related post (assuming each user has one post)
$post = $user->hasOne('Post');

// If a post is found, you can access its properties
if ($post) {
    echo "Post Title: " . $post->title;
    echo "Post Content: " . $post->content;
} else {
    echo "No post found for this user.";
}

// hasMany Relationships
// Assuming you have an instance of the User model
$user = new User(1, 'John', 'Jonathan@gmail.com');

// Fetch all posts for this user
$posts = $user->posts();  // This calls the hasMany method internally

// Output posts
foreach ($posts as $post) {
    echo $post->title . "\n";
    echo $post->content . "\n";
}



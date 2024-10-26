<?php

use App\Schema;

echo "Running migrations...\n";

try {
    // Example migration: Create users table
    Schema::create('users', function ($table) {
        $table->increments('id');
        $table->string('name');
        $table->string('email')->unique();
        $table->timestamps();
    });

    echo "Migrations completed successfully!\n";
} catch (Exception $e) {
    echo "Error during migrations: " . $e->getMessage() . "\n";
}

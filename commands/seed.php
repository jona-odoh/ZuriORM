<?php

use App\ORM;

echo "Seeding database...\n";

try {
    // Insert sample data into users table
    ORM::table('users')->insert([
        'name' => 'John Doe',
        'email' => 'john@example.com',
        'created_at' => date('Y-m-d H:i:s'),
        'updated_at' => date('Y-m-d H:i:s')
    ]);

    echo "Database seeding completed successfully!\n";
} catch (Exception $e) {
    echo "Error during seeding: " . $e->getMessage() . "\n";
}

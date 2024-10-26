#!/usr/bin/env php
<?php

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../app/bootstrap.php';

use App\Schema;

// Define the list of available commands
$availableCommands = [
    'migrate' => 'Run database migrations',
    'seed' => 'Seed the database with data',
    'generate:model' => 'Generate a model for a database table',
];

// Get the command and arguments from the terminal input
$command = $argv[1] ?? null;
$arguments = array_slice($argv, 2);

if (!$command) {
    echo "Please provide a command.\n";
    echo "Available commands are:\n";
    foreach ($availableCommands as $cmd => $description) {
        echo "- $cmd: $description\n";
    }
    exit(1);
}

// Route the command to the correct file
switch ($command) {
    case 'migrate':
        require __DIR__ . '/migrate.php';
        break;
    case 'seed':
        require __DIR__ . '/seed.php';
        break;
    case 'generate:model':
        require __DIR__ . '/generate_model.php';
        break;
    default:
        echo "Command not found. Available commands are:\n";
        foreach ($availableCommands as $cmd => $description) {
            echo "- $cmd: $description\n";
        }
        exit(1);
}


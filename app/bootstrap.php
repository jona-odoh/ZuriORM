<?php

require '../vendor/autoload.php';
require 'helpers.php';
require 'core_functions.php';
require 'utils.php';

use App\Database;
use App\ORM;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Dotenv\Dotenv;



// Load environment variables from .env file
$dotenv = Dotenv::createImmutable(__DIR__ . '/../'); 
$dotenv->load();

$env = $_ENV['APP_ENV'] ?: 'production';
$configFile = "./config/{$env}.php";

if (!file_exists($configFile)) {
	die('Configuration file not found.');
}

$config = require $configFile;

// Set up logging
$log = new Logger('ZuriORM');
$logLevel = $env === 'production' ? Logger::ERROR : Logger::DEBUG;
$log->pushHandler(new StreamHandler(__DIR__ . '/logs/app.log', $logLevel));

$log->info("Environment set to {$env}. Loading configuration from {$configFile}.");

// Establish the database connection with error handling
try {
	$connection = Database::connect($config); // Pass the entire config array
	ORM::setConnection($connection);
	$log->info('Database connection established.');
} catch (PDOException $e) {
	$log->critical('Database connection failed: ' . $e->getMessage());
	if ($env !== 'production') {
		die('Database connection failed: ' . $e->getMessage());
	} else {
		die('Database connection error. Please try again later.');
	}
}


// Error handling
set_error_handler(function ($errno, $errstr, $errfile, $errline) use ($log, $env) {
	$log->error("Error [{$errno}]: {$errstr} in {$errfile} on line {$errline}");
	if ($env !== 'production' && ini_get('display_errors')) {
		echo "Error [{$errno}]: {$errstr} in {$errfile} on line {$errline}";
	}
	return true;
});

// Exception handling
set_exception_handler(function ($exception) use ($log, $env) {
	$log->critical("Uncaught exception: {$exception->getMessage()} in {$exception->getFile()} on line {$exception->getLine()}");
	if ($env !== 'production' && ini_get('display_errors')) {
		echo "Uncaught exception: {$exception->getMessage()} in {$exception->getFile()} on line {$exception->getLine()}";
	}
});

// Register shutdown function to catch fatal errors
register_shutdown_function(function () use ($log, $env) {
	$error = error_get_last();
	if ($error) {
		$log->critical("Fatal error: {$error['message']} in {$error['file']} on line {$error['line']}");
		if ($env !== 'production' && ini_get('display_errors')) {
			echo "Fatal error: {$error['message']} in {$error['file']} on line {$error['line']}";
		}
	}
});

// Set timezone
date_default_timezone_set('Africa/Lagos');
$log->info('Timezone set to Africa/Lagos.');

// Load environment-specific settings
if ($env === 'development') {
	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(E_ALL);
	$log->info('Environment set to development. Error reporting is enabled.');
} elseif ($env === 'production') {
	ini_set('display_errors', 0);
	ini_set('display_startup_errors', 0);
	error_reporting(0);
	$log->info('Environment set to production. Error reporting is disabled.');
} else {
	$log->warning("Unknown environment: {$env}. Defaulting to production settings.");
}

$log->info('Additional setup completed. ZuriORM is ready to use.');

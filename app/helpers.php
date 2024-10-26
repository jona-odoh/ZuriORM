<?php

if (!function_exists('base_url')) {
    /**
     * Get the base URL of the application.
     *
     * @param string $path Optional path to append to the base URL.
     * @return string
     */
    function base_url($path = '')
    {
        // Load the database configuration
        $config = require __DIR__ . '/../config/database.php';
        
        // Check if base_url exists in the configuration
        if (isset($config['base_url'])) {
            return rtrim($config['base_url'], '/') . '/' . ltrim($path, '/');
        }
        
        // Fallback if base_url is not set
        return 'Base URL not configured.';
    }
}

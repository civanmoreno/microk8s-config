<?php
/**
 * Database Configuration
 *
 * Retrieves database connection parameters from environment variables
 * with fallback defaults for local development.
 */

return [
    'host' => getenv('MYSQL_HOST') ?: 'mysql',
    'database' => getenv('MYSQL_DATABASE') ?: 'lamp_db',
    'username' => getenv('MYSQL_USER') ?: 'lamp_user',
    'password' => getenv('MYSQL_PASSWORD') ?: 'lamp_password',
    'charset' => 'utf8mb4',
    'options' => [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]
];

<?php
/**
 * Database Connection Manager
 *
 * Handles PDO database connections using the Singleton pattern
 * to ensure only one connection exists throughout the application.
 */

class Database
{
    private static ?Database $instance = null;
    private ?PDO $connection = null;
    private array $config;

    /**
     * Private constructor to prevent direct instantiation
     */
    private function __construct()
    {
        $this->config = require __DIR__ . '/../config/database.php';
    }

    /**
     * Get the singleton instance
     *
     * @return Database
     */
    public static function getInstance(): Database
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Get the PDO connection
     *
     * @return PDO
     * @throws PDOException
     */
    public function getConnection(): PDO
    {
        if ($this->connection === null) {
            $this->connect();
        }
        return $this->connection;
    }

    /**
     * Establish database connection
     *
     * @throws PDOException
     */
    private function connect(): void
    {
        $dsn = sprintf(
            "mysql:host=%s;dbname=%s;charset=%s",
            $this->config['host'],
            $this->config['database'],
            $this->config['charset']
        );

        $this->connection = new PDO(
            $dsn,
            $this->config['username'],
            $this->config['password'],
            $this->config['options']
        );
    }

    /**
     * Check if database connection is alive
     *
     * @return bool
     */
    public function isConnected(): bool
    {
        try {
            if ($this->connection === null) {
                return false;
            }
            $this->connection->query('SELECT 1');
            return true;
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Get database configuration
     *
     * @return array
     */
    public function getConfig(): array
    {
        return $this->config;
    }

    /**
     * Prevent cloning of the instance
     */
    private function __clone() {}

    /**
     * Prevent unserializing of the instance
     */
    public function __wakeup()
    {
        throw new Exception("Cannot unserialize singleton");
    }
}

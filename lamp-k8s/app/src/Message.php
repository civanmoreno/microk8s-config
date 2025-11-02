<?php
/**
 * Message Model
 *
 * Handles all database operations related to messages
 */

class Message
{
    private PDO $db;
    private string $table = 'messages';

    /**
     * Constructor
     *
     * @param PDO $db Database connection
     */
    public function __construct(PDO $db)
    {
        $this->db = $db;
        $this->createTableIfNotExists();
    }

    /**
     * Create the messages table if it doesn't exist
     *
     * @return void
     */
    private function createTableIfNotExists(): void
    {
        $sql = "CREATE TABLE IF NOT EXISTS {$this->table} (
            id INT AUTO_INCREMENT PRIMARY KEY,
            message VARCHAR(255) NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_created_at (created_at)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

        $this->db->exec($sql);
    }

    /**
     * Insert a new message
     *
     * @param string $message The message text
     * @return bool True on success, false on failure
     */
    public function create(string $message): bool
    {
        try {
            $sql = "INSERT INTO {$this->table} (message) VALUES (:message)";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute(['message' => $message]);
        } catch (PDOException $e) {
            error_log("Error creating message: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get the latest messages
     *
     * @param int $limit Number of messages to retrieve
     * @return array Array of messages
     */
    public function getLatest(int $limit = 10): array
    {
        try {
            $sql = "SELECT * FROM {$this->table}
                    ORDER BY id DESC
                    LIMIT :limit";
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error fetching messages: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get total count of messages
     *
     * @return int Total number of messages
     */
    public function count(): int
    {
        try {
            $sql = "SELECT COUNT(*) FROM {$this->table}";
            return (int) $this->db->query($sql)->fetchColumn();
        } catch (PDOException $e) {
            error_log("Error counting messages: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Delete all messages (for testing purposes)
     *
     * @return bool True on success, false on failure
     */
    public function truncate(): bool
    {
        try {
            $sql = "TRUNCATE TABLE {$this->table}";
            return $this->db->exec($sql) !== false;
        } catch (PDOException $e) {
            error_log("Error truncating messages: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get a message by ID
     *
     * @param int $id Message ID
     * @return array|null Message data or null if not found
     */
    public function getById(int $id): ?array
    {
        try {
            $sql = "SELECT * FROM {$this->table} WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            $stmt->execute(['id' => $id]);
            $result = $stmt->fetch();
            return $result ?: null;
        } catch (PDOException $e) {
            error_log("Error fetching message: " . $e->getMessage());
            return null;
        }
    }
}

<?php
/**
 * LAMP Stack Application - Main Entry Point
 *
 * This application demonstrates a PHP application running on MicroK8s
 * with MySQL database connectivity.
 */

// Error reporting for development
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Autoload classes
spl_autoload_register(function ($class) {
    $file = __DIR__ . '/src/' . $class . '.php';
    if (file_exists($file)) {
        require_once $file;
    }
});

// Initialize variables
$dbConnected = false;
$dbError = null;
$messages = [];
$totalMessages = 0;
$dbConfig = [];

// Attempt database connection
try {
    $database = Database::getInstance();
    $pdo = $database->getConnection();
    $dbConnected = $database->isConnected();
    $dbConfig = $database->getConfig();

    if ($dbConnected) {
        // Initialize Message model
        $messageModel = new Message($pdo);

        // Insert a new test message
        $messageModel->create('LAMP Stack running on MicroK8s - ' . date('Y-m-d H:i:s'));

        // Fetch latest messages
        $messages = $messageModel->getLatest(10);

        // Get total count
        $totalMessages = $messageModel->count();
    }
} catch (PDOException $e) {
    $dbError = $e->getMessage();
    $dbConnected = false;
}

// Get PHP extensions
$extensions = get_loaded_extensions();
sort($extensions);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LAMP Stack on MicroK8s</title>
    <link rel="stylesheet" href="public/css/style.css">
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>🚀 LAMP Stack</h1>
            <p>Running on MicroK8s with Docker</p>
        </div>

        <div class="content">
            <!-- System Information -->
            <div class="info-grid">
                <div class="info-card">
                    <h3>🖥️ Server</h3>
                    <p><?php echo htmlspecialchars($_SERVER['SERVER_SOFTWARE']); ?></p>
                </div>
                <div class="info-card">
                    <h3>🐘 PHP Version</h3>
                    <p><?php echo phpversion(); ?></p>
                </div>
                <div class="info-card">
                    <h3>🏠 Hostname</h3>
                    <p><?php echo htmlspecialchars(gethostname()); ?></p>
                </div>
            </div>

            <!-- MySQL Connection Status -->
            <div class="section">
                <h2>💾 MySQL Database Connection</h2>

                <?php if ($dbConnected): ?>
                    <span class="status-badge status-success">
                        ✓ Successfully connected to database: <?php echo htmlspecialchars($dbConfig['database']); ?>
                    </span>

                    <div class="info-grid" style="margin-top: 20px;">
                        <div class="info-card">
                            <h3>Database Host</h3>
                            <p><?php echo htmlspecialchars($dbConfig['host']); ?></p>
                        </div>
                        <div class="info-card">
                            <h3>Database Name</h3>
                            <p><?php echo htmlspecialchars($dbConfig['database']); ?></p>
                        </div>
                        <div class="info-card">
                            <h3>Charset</h3>
                            <p><?php echo htmlspecialchars($dbConfig['charset']); ?></p>
                        </div>
                    </div>

                    <?php if (count($messages) > 0): ?>
                        <h3>📋 Latest 10 Messages</h3>
                        <table>
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Message</th>
                                    <th>Created At</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($messages as $row): ?>
                                    <tr>
                                        <td><strong>#<?php echo htmlspecialchars($row['id']); ?></strong></td>
                                        <td><?php echo htmlspecialchars($row['message']); ?></td>
                                        <td><?php echo htmlspecialchars($row['created_at']); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>

                    <div class="stats">
                        <h3><?php echo $totalMessages; ?></h3>
                        <p>Total Messages in Database</p>
                    </div>

                <?php else: ?>
                    <span class="status-badge status-error">
                        ✗ Connection failed: <?php echo htmlspecialchars($dbError ?? 'Unknown error'); ?>
                    </span>

                    <div class="alert alert-warning" style="margin-top: 20px;">
                        <strong>Connection Details:</strong>
                        <ul style="margin-top: 10px; margin-left: 20px;">
                            <li>Host: <?php echo htmlspecialchars($dbConfig['host'] ?? 'N/A'); ?></li>
                            <li>Database: <?php echo htmlspecialchars($dbConfig['database'] ?? 'N/A'); ?></li>
                            <li>Username: <?php echo htmlspecialchars($dbConfig['username'] ?? 'N/A'); ?></li>
                        </ul>
                    </div>
                <?php endif; ?>
            </div>

            <!-- PHP Extensions -->
            <div class="section">
                <h2>🔧 Installed PHP Extensions</h2>
                <div class="extensions-list">
                    <?php echo implode(' • ', array_map('htmlspecialchars', $extensions)); ?>
                </div>
            </div>

            <!-- Footer Info -->
            <div class="section">
                <div class="alert alert-info">
                    <strong>ℹ️ About this application:</strong>
                    <p style="margin-top: 10px;">
                        This is a demonstration LAMP stack application running on MicroK8s.
                        Every page load creates a new entry in the MySQL database to demonstrate
                        full stack functionality.
                    </p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>

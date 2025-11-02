<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LAMP Stack on MicroK8s</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }
        .container {
            max-width: 1000px;
            margin: 0 auto;
            background: white;
            border-radius: 12px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
            overflow: hidden;
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 40px 30px;
            text-align: center;
        }
        .header h1 {
            font-size: 2.5em;
            margin-bottom: 10px;
        }
        .header p {
            font-size: 1.1em;
            opacity: 0.9;
        }
        .content {
            padding: 30px;
        }
        .status-badge {
            display: inline-block;
            padding: 8px 16px;
            border-radius: 20px;
            font-weight: bold;
            font-size: 0.9em;
            margin: 10px 0;
        }
        .status-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .status-error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin: 30px 0;
        }
        .info-card {
            background: #f8f9fa;
            border-left: 4px solid #667eea;
            padding: 20px;
            border-radius: 8px;
        }
        .info-card h3 {
            color: #667eea;
            margin-bottom: 10px;
            font-size: 1.1em;
        }
        .info-card p {
            color: #495057;
            line-height: 1.6;
        }
        .section {
            margin: 40px 0;
        }
        .section h2 {
            color: #333;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #667eea;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background: white;
            border-radius: 8px;
            overflow: hidden;
        }
        th {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 15px;
            text-align: left;
            font-weight: 600;
        }
        td {
            padding: 12px 15px;
            border-bottom: 1px solid #e9ecef;
        }
        tr:hover {
            background-color: #f8f9fa;
        }
        tr:last-child td {
            border-bottom: none;
        }
        .stats {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            border-radius: 8px;
            margin-top: 20px;
            text-align: center;
        }
        .stats h3 {
            font-size: 2em;
            margin-bottom: 5px;
        }
        .extensions-list {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            line-height: 1.8;
        }
        .icon {
            font-size: 1.2em;
            margin-right: 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🚀 LAMP Stack</h1>
            <p>Running on MicroK8s with Docker</p>
        </div>

        <div class="content">
            <!-- System Information -->
            <div class="info-grid">
                <div class="info-card">
                    <h3>🖥️ Server</h3>
                    <p><?php echo $_SERVER['SERVER_SOFTWARE']; ?></p>
                </div>
                <div class="info-card">
                    <h3>🐘 PHP Version</h3>
                    <p><?php echo phpversion(); ?></p>
                </div>
                <div class="info-card">
                    <h3>🏠 Hostname</h3>
                    <p><?php echo gethostname(); ?></p>
                </div>
            </div>

            <!-- MySQL Connection Test -->
            <div class="section">
                <h2>💾 MySQL Database Connection</h2>
                <?php
                $servername = getenv('MYSQL_HOST') ?: "mysql";
                $username = getenv('MYSQL_USER') ?: "lamp_user";
                $password = getenv('MYSQL_PASSWORD') ?: "lamp_password";
                $dbname = getenv('MYSQL_DATABASE') ?: "lamp_db";

                try {
                    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
                    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                    echo "<span class='status-badge status-success'>✓ Successfully connected to database: $dbname</span>";

                    // Create test table
                    $conn->exec("CREATE TABLE IF NOT EXISTS messages (
                        id INT AUTO_INCREMENT PRIMARY KEY,
                        message VARCHAR(255) NOT NULL,
                        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                    )");

                    // Insert test data
                    $stmt = $conn->prepare("INSERT INTO messages (message) VALUES (:message)");
                    $stmt->execute(['message' => 'LAMP Stack running on MicroK8s - ' . date('Y-m-d H:i:s')]);

                    // Query and display data
                    $stmt = $conn->query("SELECT * FROM messages ORDER BY id DESC LIMIT 10");
                    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

                    if (count($results) > 0) {
                        echo "<h3 style='margin-top: 30px;'>📋 Latest 10 Messages</h3>";
                        echo "<table>";
                        echo "<tr><th>ID</th><th>Message</th><th>Created At</th></tr>";
                        foreach ($results as $row) {
                            echo "<tr>";
                            echo "<td><strong>#{$row['id']}</strong></td>";
                            echo "<td>{$row['message']}</td>";
                            echo "<td>{$row['created_at']}</td>";
                            echo "</tr>";
                        }
                        echo "</table>";
                    }

                    // Statistics
                    $count = $conn->query("SELECT COUNT(*) FROM messages")->fetchColumn();
                    echo "<div class='stats'>";
                    echo "<h3>$count</h3>";
                    echo "<p>Total Messages in Database</p>";
                    echo "</div>";

                } catch(PDOException $e) {
                    echo "<span class='status-badge status-error'>✗ Connection failed: " . $e->getMessage() . "</span>";
                }
                ?>
            </div>

            <!-- PHP Extensions -->
            <div class="section">
                <h2>🔧 Installed PHP Extensions</h2>
                <div class="extensions-list">
                    <?php
                    $extensions = get_loaded_extensions();
                    sort($extensions);
                    echo implode(" • ", $extensions);
                    ?>
                </div>
            </div>
        </div>
    </div>
</body>
</html>

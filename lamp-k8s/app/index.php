<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LAMP Stack en MicroK8s</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .container {
            background-color: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        h1 {
            color: #333;
            border-bottom: 3px solid #4CAF50;
            padding-bottom: 10px;
        }
        .success {
            color: #4CAF50;
            font-weight: bold;
        }
        .error {
            color: #f44336;
            font-weight: bold;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #4CAF50;
            color: white;
        }
        tr:hover {
            background-color: #f5f5f5;
        }
        .info-box {
            background-color: #e7f3ff;
            border-left: 4px solid #2196F3;
            padding: 15px;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🚀 LAMP Stack en MicroK8s con Docker</h1>

        <div class="info-box">
            <h3>Información del Sistema</h3>
            <p><strong>Servidor:</strong> <?php echo $_SERVER['SERVER_SOFTWARE']; ?></p>
            <p><strong>Versión de PHP:</strong> <?php echo phpversion(); ?></p>
            <p><strong>Host:</strong> <?php echo gethostname(); ?></p>
        </div>

        <h2>Prueba de Conexión MySQL</h2>
        <?php
        $servername = getenv('MYSQL_HOST') ?: "mysql";
        $username = getenv('MYSQL_USER') ?: "lamp_user";
        $password = getenv('MYSQL_PASSWORD') ?: "lamp_password";
        $dbname = getenv('MYSQL_DATABASE') ?: "lamp_db";

        try {
            $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            echo "<p class='success'>✓ Conexión exitosa a MySQL database: $dbname</p>";

            // Crear tabla de prueba
            $conn->exec("CREATE TABLE IF NOT EXISTS messages (
                id INT AUTO_INCREMENT PRIMARY KEY,
                message VARCHAR(255) NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )");

            // Insertar datos de prueba
            $stmt = $conn->prepare("INSERT INTO messages (message) VALUES (:message)");
            $stmt->execute(['message' => 'LAMP Stack funcionando en MicroK8s con Docker - ' . date('Y-m-d H:i:s')]);

            // Consultar y mostrar datos
            $stmt = $conn->query("SELECT * FROM messages ORDER BY id DESC LIMIT 10");
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

            echo "<h3>Últimos 10 mensajes:</h3>";
            echo "<table>";
            echo "<tr><th>ID</th><th>Mensaje</th><th>Fecha de Creación</th></tr>";
            foreach ($results as $row) {
                echo "<tr>";
                echo "<td>{$row['id']}</td>";
                echo "<td>{$row['message']}</td>";
                echo "<td>{$row['created_at']}</td>";
                echo "</tr>";
            }
            echo "</table>";

            // Estadísticas
            $count = $conn->query("SELECT COUNT(*) FROM messages")->fetchColumn();
            echo "<p><strong>Total de mensajes en la base de datos:</strong> $count</p>";

        } catch(PDOException $e) {
            echo "<p class='error'>✗ Error de conexión: " . $e->getMessage() . "</p>";
        }
        ?>

        <h2>Extensiones PHP Instaladas</h2>
        <div class="info-box">
            <?php
            $extensions = get_loaded_extensions();
            sort($extensions);
            echo "<p>" . implode(", ", $extensions) . "</p>";
            ?>
        </div>
    </div>
</body>
</html>

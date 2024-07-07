<?php
$rootDir = dirname(dirname(__FILE__));
$configPath = $rootDir . '/data/config.php';
$config = require $configPath;

$servername = $config['database']['host'];
$username = $config['database']['username'];
$password = $config['database']['password'];
$dbname = $config['database']['dbname'];

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// SQL to create the question table
$sql = "CREATE TABLE IF NOT EXISTS question (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    question_text TEXT NOT NULL,
    question_type ENUM('text', 'radio', 'checkbox') NOT NULL,
    options TEXT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if ($conn->query($sql) === TRUE) {
    echo "Table question created successfully";
} else {
    echo "Error creating table: " . $conn->error;
}

$conn->close();
?>
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
$sql = "CREATE TABLE IF NOT EXISTS answers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id VARCHAR(255) NOT NULL,
    question_id INT NOT NULL,
    answer TEXT NOT NULL,
    submission_time DATETIME NOT NULL
);";

if ($conn->query($sql) === TRUE) {
    echo "Table question created successfully";
} else {
    echo "Error creating table: " . $conn->error;
}

$conn->close();
?>
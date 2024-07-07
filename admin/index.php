<?php
session_start();
$rootDir = dirname(dirname(__FILE__));
$configPath = $rootDir . '/data/config.php';
$config = require $configPath;

if (!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in']) {
    header("Location: login.php");
    exit;
}

$servername = $config['database']['host'];
$username = $config['database']['username'];
$password = $config['database']['password'];
$dbname = $config['database']['dbname'];

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $_POST['title'];
    $question_text = $_POST['question_text'];
    $question_type = $_POST['question_type'];
    $options = $_POST['options'];

    $sql = "INSERT INTO question (title, question_text, question_type, options) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssss", $title, $question_text, $question_type, $options);

    if ($stmt->execute()) {
        echo "新问题已成功添加!";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="zh">
<head>
    <meta charset="UTF-8">
    <title>极客调查 - 问卷管理</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 800px;
            margin: 50px auto;
            background-color: #ffffff;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        h1 {
            text-align: center;
            color: #333;
        }
        form {
            display: flex;
            flex-direction: column;
        }
        label {
            margin-top: 10px;
            color: #666;
        }
        input[type="text"], textarea, select {
            margin-top: 5px;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        button {
            margin-top: 20px;
            padding: 10px 20px;
            background-color: #007BFF;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>添加新问题</h1>
        <a href="watch.php">查看提交</a>
        <form method="post" action="index.php">
            <label for="title">标题:</label>
            <input type="text" id="title" name="title">

            <label for="question_text">问题:</label>
            <textarea id="question_text" name="question_text"></textarea>

            <label for="question_type">问题类型:</label>
            <select id="question_type" name="question_type">
                <option value="text">文本</option>
                <option value="radio">单选</option>
                <option value="checkbox">多选</option>
            </select>

            <label for="options">选项(用分号分隔):</label>
            <textarea id="options" name="options"></textarea>

            <button type="submit">添加问题</button>
        </form>
    </div>
</body>
</html>
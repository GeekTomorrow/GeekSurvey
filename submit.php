<?php
session_start();

// 包含数据库配置文件
$rootDir = dirname(dirname(__FILE__));
$configPath = '.\data\config.php';
$config = require $configPath;

try {
    $dsn = "mysql:host={$config['database']['host']};dbname={$config['database']['dbname']}";
    $pdo = new PDO($dsn, $config['database']['username'], $config['database']['password']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $user_id = uniqid();
        $submission_time = date('Y-m-d H:i:s');

        // 遍历问题答案
        foreach ($_POST as $key => $value) {
            if (strpos($key, 'answer_') === 0) {
                $question_id = str_replace('answer_', '', $key);
                if (is_array($value)) { // 处理复选框
                    foreach ($value as $v) {
                        $stmt = $pdo->prepare("INSERT INTO answers (user_id, question_id, answer, submission_time) VALUES (:user_id, :question_id, :answer, :submission_time)");
                        $stmt->bindParam(':user_id', $user_id);
                        $stmt->bindParam(':question_id', $question_id);
                        $stmt->bindParam(':answer', $v);
                        $stmt->bindParam(':submission_time', $submission_time);
                        $stmt->execute();
                    }
                } else { // 处理单选和文本输入
                    $stmt = $pdo->prepare("INSERT INTO answers (user_id, question_id, answer, submission_time) VALUES (:user_id, :question_id, :answer, :submission_time)");
                    $stmt->bindParam(':user_id', $user_id);
                    $stmt->bindParam(':question_id', $question_id);
                    $stmt->bindParam(':answer', $value);
                    $stmt->bindParam(':submission_time', $submission_time);
                    $stmt->execute();
                }
            }
        }

        echo "您的回答已成功提交！";
    }
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
?>
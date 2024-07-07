<?php
// install.php

$lockFilePath = '../data/install.lock';

// 检查锁定文件是否存在，如果存在则退出
if (file_exists($lockFilePath)) {
    echo "安装已成功完成！请不要重复运行此安装程序。";
    exit;
}

// 检查是否为POST请求
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $servername = $_POST['servername'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $dbname = $_POST['dbname'];

    // 创建连接
    $conn = new mysqli($servername, $username, $password);

    // 检查连接
    if ($conn->connect_error) {
        http_response_code(500); // 返回服务器错误状态码
        echo "连接失败: " . $conn->connect_error;
        $conn->close();
        exit;
    }

    // 创建数据库
    $sql = "CREATE DATABASE IF NOT EXISTS $dbname";
    if ($conn->query($sql) === TRUE) {
        echo "数据库创建成功";
    } else {
        http_response_code(500);
        echo "创建数据库失败: " . $conn->error;
        $conn->close();
        exit;
    }

    // 选择数据库
    $conn->select_db($dbname);

    // 创建表
    $sql = "CREATE TABLE IF NOT EXISTS responses (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        email VARCHAR(255),
        question1 VARCHAR(255),
        question2 VARCHAR(255),
        question3 VARCHAR(255),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";

    if ($conn->query($sql) === TRUE) {
        echo "表responses创建成功";
    } else {
        http_response_code(500);
        echo "创建表失败: " . $conn->error;
    }

    // 关闭连接
    $conn->close();

    // 创建锁定文件，表示安装已完成
    file_put_contents($lockFilePath, 'Installation completed.');

    // 写入配置文件
    $configContent = <<<EOT
<?php
return [
    'database' => [
        'host' => '$servername',
        'username' => '$username',
        'password' => '$password',
        'dbname' => '$dbname'
    ]
];
EOT;

    file_put_contents('../data/config.php', $configContent);

    echo "安装完成！配置文件已生成。";
}
?>
<!DOCTYPE html>
<html lang="zh">
<head>
    <meta charset="UTF-8">
    <title>管理面板 - 答案查看</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }
        .container {
            max-width: 960px;
            margin: 0 auto;
            padding: 20px;
            background-color: #fff;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        h1 {
            text-align: center;
            color: #333;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
            color: #333;
        }
        tr:hover {
            background-color: #f5f5f5;
        }
        .no-data {
            text-align: center;
            color: #777;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>管理面板 - 答案查看</h1>
        <div id="content">
            <?php
            session_start();

            if (!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in']) {
                header("Location: login.php");
                exit;
            }

            // 包含数据库配置文件
            $rootDir = dirname(dirname(__FILE__));
            $configPath = $rootDir . '/data/config.php';
            $config = require $configPath;

            try {
                // 设置数据库连接
                $dsn = "mysql:host={$config['database']['host']};dbname={$config['database']['dbname']}";
                $pdo = new PDO($dsn, $config['database']['username'], $config['database']['password']);
                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                // 查询所有答案记录
                $stmt = $pdo->prepare("SELECT a.id, a.user_id, q.question_text, a.answer, a.submission_time FROM answers a JOIN question q ON a.question_id = q.id");
                $stmt->execute();
                $answers = $stmt->fetchAll(PDO::FETCH_ASSOC);

                // 显示答案记录
                if (!empty($answers)) {
                    echo "<table>";
                    echo "<thead><tr><th>用户ID</th><th>问题</th><th>答案</th><th>提交时间</th></tr></thead>";
                    echo "<tbody>";
                    foreach ($answers as $answer) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($answer['user_id']) . "</td>";
                        echo "<td>" . htmlspecialchars($answer['question_text']) . "</td>";
                        echo "<td>" . htmlspecialchars($answer['answer']) . "</td>";
                        echo "<td>" . htmlspecialchars($answer['submission_time']) . "</td>";
                        echo "</tr>";
                    }
                    echo "</tbody>";
                    echo "</table>";
                } else {
                    echo "<p class='no-data'>没有找到任何答案记录。</p>";
                }
            } catch (PDOException $e) {
                echo "Connection failed: " . $e->getMessage();
            }
            ?>
        </div>
    </div>
</body>
</html>
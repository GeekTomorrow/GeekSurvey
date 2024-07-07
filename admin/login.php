<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>登录</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .login-form {
            background-color: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            width: 300px;
        }
        .login-form h1 {
            text-align: center;
            color: #333;
        }
        .login-form label {
            display: block;
            margin-bottom: 5px;
            color: #333;
        }
        .login-form input[type="text"],
        .login-form input[type="password"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 3px;
        }
        .login-form input[type="submit"] {
            width: 100%;
            padding: 10px;
            background-color: #007BFF;
            color: white;
            border: none;
            border-radius: 3px;
            cursor: pointer;
        }
        .login-form input[type="submit"]:hover {
            background-color: #0056b3;
        }
        .error-message {
            color: red;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <div class="login-form">
        <h1>登录</h1>
        <?php
        session_start();

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $username = $_POST['username'];
            $password = $_POST['password'];

            // 防止SQL注入
            $rootDir = dirname(dirname(__FILE__));
            $configPath = $rootDir . '/data/config.php';
            $config = require $configPath;

            try {
                $dsn = "mysql:host={$config['database']['host']};dbname={$config['database']['dbname']}";
                $pdo = new PDO($dsn, $config['database']['username'], $config['database']['password']);
                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                $stmt = $pdo->prepare("SELECT * FROM admins WHERE username = ? AND password = ?");
                $stmt->execute([$username, $password]);
                $admin = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($admin) {
                    $_SESSION['admin_logged_in'] = true;
                    header('Location: index.php');
                    exit;
                } else {
                    echo "<p class='error-message'>Invalid username or password.</p>";
                }
            } catch (PDOException $e) {
                die("Connection failed: " . $e->getMessage());
            }
        }
        ?>

        <form action="login.php" method="post">
            <label for="username">用户名:</label>
            <input type="text" id="username" name="username">
            <label for="password">密码:</label>
            <input type="password" id="password" name="password">
            <input type="submit" value="Login">
        </form>
    </div>
</body>
</html>
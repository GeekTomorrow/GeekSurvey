<?php
session_start();
$configPath = '.\data\config.php';
$config = require $configPath;

$servername = $config['database']['host'];
$username = $config['database']['username'];
$password = $config['database']['password'];
$dbname = $config['database']['dbname'];

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT * FROM question";
$result = $conn->query($sql);

$questions = array();
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $questions[] = $row;
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="zh">
<head>
    <meta charset="UTF-8">
    <title>极客调查</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <h1>极客调查</h1>
        <form method="post" action="submit.php" class="survey-form">
            <?php foreach ($questions as $q): ?>
                <div class="question">
                    <p class="question-text"><?php echo htmlspecialchars($q['question_text'], ENT_QUOTES, 'UTF-8'); ?></p>
                    <?php if ($q['question_type'] === 'text'): ?>
                        <input type="text" name="answer_<?php echo $q['id']; ?>" required>
                    <?php elseif ($q['question_type'] === 'radio'): ?>
                        <?php $options = explode(';', $q['options']); ?>
                        <?php foreach ($options as $option): ?>
                            <label><input type="radio" name="answer_<?php echo $q['id']; ?>" value="<?php echo htmlspecialchars($option, ENT_QUOTES, 'UTF-8'); ?>"><?php echo htmlspecialchars($option, ENT_QUOTES, 'UTF-8'); ?></label><br>
                        <?php endforeach; ?>
                    <?php elseif ($q['question_type'] === 'checkbox'): ?>
                        <?php $options = explode(';', $q['options']); ?>
                        <?php foreach ($options as $option): ?>
                            <label><input type="checkbox" name="answer_<?php echo $q['id']; ?>[]" value="<?php echo htmlspecialchars($option, ENT_QUOTES, 'UTF-8'); ?>"><?php echo htmlspecialchars($option, ENT_QUOTES, 'UTF-8'); ?></label><br>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
            <button type="submit" class="submit-button">提交</button>
        </form>
    </div>
</body>
</html>
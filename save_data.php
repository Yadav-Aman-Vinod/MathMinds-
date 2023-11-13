<?php
session_start();
require_once('dbconfig/config.php');

if (isset($_SESSION['email']) && isset($_POST['score'])) {
    $email = $_SESSION['email'];
    $operator = $_POST['operator'];
    $level = $_POST['level'];
    $time = $_POST['time'];
    $score = $_POST['score'];
    $tscore = $_POST['tscore'];

    $query = "INSERT INTO records (email, operation, level, time, score, tscore) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $con->prepare($query);
    $stmt->bind_param('ssiiid', $email, $operator, $level, $time, $score, $tscore);

    if ($stmt->execute()) {
        echo "Data saved successfully.";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $con->close();
} else {
    echo "Data not saved. Please make sure you are logged in and the necessary data is available.";
}
?>

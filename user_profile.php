<?php
session_start();
require_once('dbconfig/config.php');

if (!isset($_SESSION['email'])) {
    echo '<script>window.location.href = "login.php";</script>';
    exit;
}

if (isset($_GET['username'])) {
    $username = $_GET['username'];

    $query = "SELECT * FROM user WHERE email = '$username'";
    $result = $con->query($query);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        echo "<h2>Username: " . $row['email'] . "</h2>";
        echo "<p>Name: " . $row['name'] . "</p>";
        echo "<p>Class: " . $row['class'] . "</p>";
        echo "<p>Roll No: " . $row['rollno'] . "</p>";
    } else {
        echo "User not found.";
    }
}
?>


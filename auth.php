<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

include 'config.php';

// Refresh session user from DB
$userId = $_SESSION['user']['id'];
$result = mysqli_query($conn, "SELECT * FROM users WHERE id = $userId");
if ($row = mysqli_fetch_assoc($result)) {
    $_SESSION['user'] = $row;
}

?>

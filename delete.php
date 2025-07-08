<?php

session_start();
include 'config.php';

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

$loggedInUser = $_SESSION['user'];
if (!in_array($loggedInUser['role'], ['admin', 'superadmin'])) {
    die("Access denied");
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Invalid request");
}
$deleteId = intval($_GET['id']);

$result = mysqli_query($conn, "SELECT * FROM users WHERE id = $deleteId");
if (mysqli_num_rows($result) === 0) {
    die("User not found");
}
$targetUser = mysqli_fetch_assoc($result);

if ($targetUser['role'] === 'superadmin') {
    die("Cannot delete Super Admin");
}

mysqli_query($conn, "DELETE FROM users WHERE id = $deleteId");

if ($deleteId == $loggedInUser['id']) {
    session_destroy();
    header("Location: login.php?msg=You deleted your account and were logged out.");
    exit;
}

// success message in session
$_SESSION['delete_success'] = "User <strong>{$targetUser['name']}</strong> has been deleted successfully.";
header("Location: index.php");
exit;

?>

<?php
$host = "localhost";
$user = "root";      
$pass = "";          
$dbname = "php_crud";

$conn = mysqli_connect($host, $user, $pass, $dbname);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>

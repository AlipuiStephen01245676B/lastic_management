<?php
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "project_db"; // Updated to match your phpMyAdmin database name

$conn = mysqli_connect($host, $user, $pass, $dbname);

if (!$conn) {
    die("Database connection cluster failed: " . mysqli_connect_error());
}
?>
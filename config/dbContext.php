<?php
$host = "localhost";
$user = "root";
$password = "";
$database = "portfoliohub";

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

try {
    // Change $conn or $db to $pdo right here:
    $pdo = new PDO("mysql:host=$host;dbname=$database;charset=utf8mb4", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Kết nối thất bại: " . mysqli_connect_error());
}

?>
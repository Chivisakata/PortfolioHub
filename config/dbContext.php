<?php
$host = "localhost";
$user = "root";
$password = "";
$database = "portfoliohub";

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$conn = new mysqli($host, $user, $password, $database);

if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

?>
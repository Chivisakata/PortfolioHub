<?php
$host = "localhost";
$user = "root";
$password = "";
$database = "portfoliohub";

$conn = mysqli_connect($host, $user, $password, $database);
$conn->set_charset("utf8mb4");

if (!$conn) {
    die("Kết nối thất bại: " . mysqli_connect_error());
}
?>
<?php

require_once "../config/dbContext.php";

header("Content-Type: application/json");

$data = json_decode(file_get_contents("php://input"),true);

$id = intval($data["id"]);

$sql = "SELECT status
        FROM users
        WHERE id=$id";

$result = $conn->query($sql);

$user = $result->fetch_assoc();

if(!$user){

    echo json_encode([
        "success"=>false,
        "message"=>"Không tìm thấy người dùng."
    ]);

    exit();

}

$newStatus = ($user["status"] == 1) ? 0 : 1;

$sql = "UPDATE users
        SET status=$newStatus
        WHERE id=$id";

$conn->query($sql);

echo json_encode([

    "success"=>true,

    "status"=>$newStatus,

    "message"=>$newStatus
        ? "Đã mở khóa tài khoản."
        : "Đã khóa tài khoản."

]);
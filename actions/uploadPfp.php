<?php
session_start();
require_once "../config/dbContext.php";

header("Content-Type: application/json");

$id = $_SESSION["userId"];

// Không chọn ảnh => không làm gì
if (!isset($_FILES["avatar"]) || $_FILES["avatar"]["error"] != 0) {

    echo json_encode([
        "success" => true
    ]);
    exit();

}

$file = $_FILES["avatar"];

$ext = strtolower(pathinfo($file["name"], PATHINFO_EXTENSION));

$fileName = "avatar_" . $id . "_" . time() . "." . $ext;

move_uploaded_file($file["tmp_name"], "../images/pfps/" . $fileName);

$sql = "UPDATE profiles SET pfp='$path' WHERE uid=$id";
$conn->query($sql);

$_SESSION["pfp"] = $path;

echo json_encode([
    "success" => true,
    "path" => "../" . $path
]);
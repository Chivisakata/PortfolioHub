<?php

require_once "../config/dbContext.php";

header("Content-Type: application/json");

$data = json_decode(file_get_contents("php://input"), true);

$id = intval($data["id"]);

// PROFILES

$sql = "UPDATE profiles
        SET
            name = NULL,
            email = NULL,
            location = NULL,
            website = NULL,
            bio = NULL,
            pfp = NULL
        WHERE uid = $id";

$conn->query($sql);


// USERDETAILS

$sql = "UPDATE userdetails
        SET
            skills = NULL,
            field = NULL
        WHERE uid = $id";

$conn->query($sql);


// WORKS_EXPERIENCES

$conn->query("DELETE FROM work_exp WHERE uid = $id");


// PROJECTS

$conn->query("DELETE FROM projects WHERE uid = $id");


echo json_encode([
    "success" => true,
    "message" => "Đã xóa Portfolio thành công."
]);
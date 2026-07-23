<?php
require_once "../config/dbContext.php";

header("Content-Type: application/json");

$sql = "SELECT
            users.id,
            profiles.name,
            users.email,
            users.status
        FROM users
        LEFT JOIN profiles
        ON users.id = profiles.uid
        WHERE users.role = 1
        ORDER BY users.id DESC
        LIMIT 20"
        ;

$result = $conn->query($sql);

$users = [];

while($row = $result->fetch_assoc()){

    $users[] = [
        "id" => (int)$row["id"],
        "name" => $row["name"] ?? "Chưa cập nhật",
        "email" => $row["email"],
        "status" => (int)$row["status"]
    ];

}

echo json_encode($users);
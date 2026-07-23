<?php

require_once "../config/dbContext.php";

header("Content-Type: application/json");

$keyword = isset($_GET["q"]) ? trim($_GET["q"]) : "";

$sql = "SELECT
            users.id,
            profiles.name,
            users.email,
            users.status
        FROM users
        LEFT JOIN profiles
        ON users.id = profiles.uid
        WHERE users.role = 1
        AND profiles.name LIKE ?
        OR users.email LIKE ? 
        AND users.role = 1
        ORDER BY users.id DESC";

$stmt = $conn->prepare($sql);

$search = "%".$keyword."%";

$stmt->bind_param("ss",$search,$search);

$stmt->execute();

$result = $stmt->get_result();

$users = [];

while($row = $result->fetch_assoc()){

    $users[] = [
        "id"=>$row["id"],
        "name"=>$row["name"] ?? "Chưa cập nhật",
        "email"=>$row["email"],
        "status"=>$row["status"]
    ];

}

echo json_encode($users);
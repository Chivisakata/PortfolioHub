<?php
session_start();
header("Content-Type: application/json");
require_once '../config/dbContext.php';    // kết nối MySQL

$id = isset($_SESSION['userId']) ? $_SESSION['userId'] : "";
// Thông tin chính
$sql = "SELECT * FROM profiles WHERE uid = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i",$id);
$stmt->execute();

$result = $stmt->get_result();
$data = $result->fetch_assoc();

if(!$data){
    echo json_encode([]);
    exit;
}

// Skills
$sql = "SELECT skills FROM userdetails WHERE uid = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();

$result = $stmt->get_result();
$row = $result->fetch_assoc();

$skills = [];

if ($row && !empty($row['skills'])) {
    $skills = explode(";", $row['skills']);
}

// Experiences
$experiences = [];
$res = $conn->query("SELECT * FROM work_exp WHERE uid = $id");
while($row = $res->fetch_assoc()){
    $experiences[] = $row;
}

// Projects
$projects = [];
$res = $conn->query("SELECT * FROM projects WHERE uid = $id");
while($row = $res->fetch_assoc()){
    $projects[] = $row;
}

$data["skills"] = $skills;
$data["experiences"] = $experiences;
$data["projects"] = $projects;

echo json_encode($data);
?>
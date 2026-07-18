<?php
session_start();
require_once "../config/dbContext.php";

header("Content-Type: application/json");
$id = isset($_SESSION['userId']) ? $_SESSION['userId'] : ""; 


$data = json_decode(file_get_contents("php://input"), true);

/*=========================
    PROFILES
=========================*/

$name     = $data["name"];
$email    = $data["email"];
$location = $data["location"];
$website  = $data["website"];
$bio      = $data["bio"];

$sql = "UPDATE profiles
        SET
            name='$name',
            email='$email',
            location='$location',
            website='$website',
            bio='$bio'
        WHERE uid=$id";

$conn->query($sql);


/*=========================
    USERDETAILS
=========================*/

$skills = implode(";", $data["skills"]);
$field = $data["title"];

$sql = "UPDATE userdetails
        SET
            skills='$skills',
            field='$field'
        WHERE uid=$id";

$conn->query($sql);

/*=========================
    WORK EXPERIENCE
=========================*/

$conn->query("DELETE FROM work_exp WHERE uid=$id");

foreach($data["experiences"] as $exp){

    $company     = $exp["company"];
    $job_title   = $exp["job_title"];
    $start_date  = $exp["start_date"];
    $end_date    = $exp["end_date"];
    $description = $exp["description"];

    $sql = "INSERT INTO work_exp
            (uid,company,job_title,start_date,end_date,description)
            VALUES
            (
                $id,
                '$company',
                '$job_title',
                '$start_date',
                '$end_date',
                '$description'
            )";

    $conn->query($sql);
}


/*=========================
    PROJECTS
=========================*/

$conn->query("DELETE FROM projects WHERE uid=$id");

foreach($data["projects"] as $project){

    $project_name = $project["project_name"];
    $tech         = $project["tech"];
    $description  = $project["description"];

    $sql = "INSERT INTO projects
            (uid,project_name,tech,description)
            VALUES
            (
                $id,
                '$project_name',
                '$tech',
                '$description'
            )";

    $conn->query($sql);
}

echo json_encode([
    "success" => true
]);
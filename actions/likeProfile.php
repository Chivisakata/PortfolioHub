<?php
session_start();
require_once '../config/dbContext.php';

$error = [];

//Check to make sure admin hijack not happening
$likedBy = isset($_SESSION['userId']) ? (int)$_SESSION['userId'] : -1; 
$uid = isset($_POST['uid']) ? (int)$_POST['uid'] : -1; // The profile being liked

//Check if user is login
if($likedBy === -1)
    {
        $error[]= "Đăng nhập để thực hiện thao tác";
    }

if($uid === -1 || $uid === $likedBy){
    $error[] = "Thao tác không thể thực hiện";
}

if(!empty($error)){
    $_SESSION["error"] = $error[0];

    if($uid !== -1)
        {
            header("Location: ../pages/detail.php?id=" .$uid);
        }else{
            header("Location:login.php");
        }
    exit();
}

// 4. Check if this user has already liked this profile
$checkQuery = "SELECT * FROM likes WHERE uid = ? AND liked_by = ?";
$stmt = $conn->prepare($checkQuery);
$stmt->bind_param("ii", $uid, $likedBy);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // Unlike: Record exists => delete it
    $actionQuery = "DELETE FROM likes WHERE uid = ? AND liked_by = ?";
} else {
    // Like: Record doesn't exist => insert it
    $actionQuery = "INSERT INTO likes (uid, liked_by) VALUES (?, ?)";
}

$actionStmt = $conn->prepare($actionQuery);
$actionStmt->bind_param("ii", $uid, $likedBy);
$actionStmt->execute();

$actionStmt->close();
$stmt->close();

header("Location: ../pages/detail.php?id=" . $uid);
exit();

?>
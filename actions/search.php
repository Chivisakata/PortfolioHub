<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once '../config/dbContext.php'; 

$keyword = isset($_GET['keyword']) ? trim($_GET['keyword']) : '';
$searchResults = [];

if (!empty($keyword)) {
    // 1. Chuẩn bị từ khóa cho FULLTEXT MODE và LIKE MODE
    $searchParamBoolean = $keyword . '*'; 
    $searchParamLike = '%' . $keyword . '%';

    // 2. Câu lệnh kết hợp: Nếu MATCH AGAINST không ra (do thiếu index) thì LIKE sẽ cứu cánh
    $sql = "SELECT u.*, ud.skills, c.name AS category_name
            FROM users u
            LEFT JOIN userdetails ud ON u.id = ud.uid
            LEFT JOIN category c ON ud.field = c.id
            WHERE MATCH(ud.skills) AGAINST(? IN BOOLEAN MODE)
               OR MATCH(c.name) AGAINST(? IN BOOLEAN MODE)
               OR ud.skills LIKE ?
               OR c.name LIKE ?";
               
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        die("<div class='alert alert-danger'>Lỗi SQL syntax: " . $conn->error . "</div>");
    }
    
    // Ràng buộc 4 tham số vào dấu ? theo thứ tự
    $stmt->bind_param("ssss", $searchParamBoolean, $searchParamBoolean, $searchParamLike, $searchParamLike);
    $stmt->execute();
    $searchResults = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
} else {
    // Mặc định hiển thị tất cả nếu URL trống
    $sql = "SELECT u.*, ud.skills, c.name AS category_name
            FROM users u
            LEFT JOIN userdetails ud ON u.id = ud.uid
            LEFT JOIN category c ON ud.field = c.id";
            
    $result = $conn->query($sql);
    if ($result) {
        $searchResults = $result->fetch_all(MYSQLI_ASSOC);
    }
}
?>
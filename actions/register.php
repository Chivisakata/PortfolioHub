<?php 
    include '../config/dbContext.php';
    session_start();

    $email = isset($_POST['regEmail']) ? trim($_POST['regEmail']) : '';
    $password = isset($_POST['regPassword']) ? trim($_POST['regPassword']) : '';
    $confirmPassword = isset($_POST['confirmPassword']) ? trim($_POST['confirmPassword']) : '';

    $errors = [];


    // Kiểm tra Email
    if (empty($email)) {
        $errors[] = "Email không được để trống!";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Email không đúng định dạng!";
    }
    // Kiểm tra mật khẩu
    $passwordPattern = '/^(?=.*[a-zA-Z0-9])[a-zA-Z0-9!@#$%^&*()_+\-=\[\]{};\':"\\\\|,.<>\/?]{8,32}$/';
    if (empty($password)) {
        $errors[] = "Mật khẩu không được để trống!";
    } elseif (!preg_match($passwordPattern, $password)) {
        // ⚡ Hàm preg_match dùng để khớp chuỗi Regex trong PHP
        $errors[] = "Mật khẩu chỉ chứa kí tự A-Z, a-z hoặc 0-9 và kí tự đặc biệt, không chứa khoảng trắng và có ít nhất 1 chữ hoặc 1 số, độ dài từ 8 đến 32 kí tự nhé!";
    }
    // 3. Kiểm tra mật khẩu xác nhận có trùng khớp không
    if ($password !== $confirmPassword) {
        $errors[] = "Mật khẩu xác nhận không khớp!";
    }
    //kiểm tra email tồn tại
    $sql = "SELECT * FROM users WHERE email = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    if (mysqli_num_rows($result) > 0) {
         $errors[] = "Email đã tồn tại!";     
    }
    
    // Nếu có lỗi, trả về lỗi
    if (!empty($errors)) {
        $_SESSION["error"] = $errors[0];
        header("Location: ../pages/register.php");
        exit();
    }else{
        // Nếu không có lỗi, tiến hành đăng ký người dùng
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $role = 1; // 1 là user, 0 là admin
        $sql = "INSERT INTO users (role, email, hashedPsswd) VALUES (?, ?, ?)";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "iss", $role, $email, $hashedPassword);
        if (mysqli_stmt_execute($stmt)) {
            $_SESSION["success"] = "Đăng ký thành công! Vui lòng đăng nhập.";
            header("Location: ../pages/register.php");
            exit();
        } else {
            $_SESSION["error"] = "Đăng ký thất bại! Vui lòng thử lại.";
            header("Location: ../pages/register.php");
            exit();
        }
    }
    // Nếu không có lỗi, tiến hành đăng ký người dùng
    




    
?>
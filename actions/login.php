<?php
 require_once '../config/dbContext.php';
    session_start();

    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $password = isset($_POST['password']) ? trim($_POST['password']) : '';

    $errors = [];

    // Kiểm tra định dạng Email
    if (empty($email)) {
        $errors[] = "Email không được để trống!";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Email không đúng định dạng!";
    }
    // Kiểm tra định dạng mật khẩu
    $passwordPattern = '/^(?=.*[a-zA-Z0-9])[a-zA-Z0-9!@#$%^&*()_+\-=\[\]{};\':"\\\\|,.<>\/?]{8,32}$/';
    if (empty($password)) {
        $errors[] = "Mật khẩu không được để trống!";
    } elseif (!preg_match($passwordPattern, $password)) {
        // Hàm preg_match dùng để khớp chuỗi Regex trong PHP
        $errors[] = "Mật khẩu không hợp lệ!";
    }

    //kiểm tra email và mật khẩu có tồn tại trong cơ sở dữ liệu
    $sql = "SELECT id, email, role, hashedPsswd FROM users WHERE email = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    if (mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);
        if(password_verify($password, $user['hashedPsswd'])){
            $sql = "SELECT name, email, pfp FROM profiles WHERE uid = ?";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "i", $user['id']);
            mysqli_stmt_execute($stmt);
            $profile = mysqli_stmt_get_result($stmt);
            $profile = mysqli_fetch_assoc($profile);
            $_SESSION["userId"] = $user['id'];
            $_SESSION["email"] = $user['email'];
            $_SESSION["role"] = $user['role'];
            $_SESSION["profileName"] = $profile['name'];
            $_SESSION["profileEmail"] = $profile['email'];
            $_SESSION['pfp'] = $profile['pfp'];
            $_SESSION["success"] = "Đăng nhập thành công!";


            // var_dump($_SESSION);
            header("Location: ../index.php");
            exit();
        }
        else {
            $errors[] = "Email hoặc mật khẩu không đúng!";
        }
    } else {
       $errors[] = "Email hoặc mật khẩu không đúng!"; 
    }
     // Nếu có lỗi, trả về lỗi
    if (!empty($errors)) {
        $_SESSION["error"] = $errors[0];
        header("Location: ../pages/login.php");
        exit();
    }
?>
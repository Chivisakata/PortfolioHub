<?php
    session_start(); 
?>
<!DOCTYPE html>
<html lang="en" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập - PortfolioHub</title>
    
    <!-- Bootstrap 5.3.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <!-- Google Fonts - Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background-color 0.3s, color 0.3s;
        }
        .gradient-text {
            background: linear-gradient(135deg, #4f46e5 0%, #ec4899 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .login-card {
            width: 100%;
            max-width: 420px;
            border-radius: 16px;
            border: 1px solid rgba(0, 0, 0, 0.08);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.05);
            transition: transform 0.2s;
        }
        [data-bs-theme="dark"] .login-card {
            border: 1px solid rgba(255, 255, 255, 0.08);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.3);
        }
        .btn-social {
            transition: all 0.2s;
            border-color: rgba(0, 0, 0, 0.1);
        }
        [data-bs-theme="dark"] .btn-social {
            border-color: rgba(255, 255, 255, 0.15);
            color: #fff;
        }
        .btn-social:hover {
            background-color: rgba(0, 0, 0, 0.03);
            transform: translateY(-1px);
        }
        [data-bs-theme="dark"] .btn-social:hover {
            background-color: rgba(255, 255, 255, 0.05);
        }
    </style>
</head>
<body class="bg-body-tertiary">

<?php
    if (isset($_SESSION["error"])) {
    ?>
    <div class="position-fixed top-0 end-0 p-3" style="z-index: 9999;">
        <div class="alert alert-danger alert-dismissible fade show shadow" role="alert">
            <?= htmlspecialchars($_SESSION["error"]) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    </div>
    <?php
        unset($_SESSION["error"]);
    }
    ?>

    <div class="position-absolute top-0 end-0 p-3">
        <button class="btn btn-link text-body p-2" id="themeToggleBtn" title="Đổi giao diện Sáng/Tối">
            <i class="bi bi-sun-fill fs-5" id="themeIcon"></i>
        </button>
    </div>

    <div class="container p-3">
        <div class="card login-card mx-auto p-4 p-md-5 bg-body">
            
            <!-- Logo & Title -->
            <div class="text-center mb-4">
                 <a href="../index.php" class="text-decoration-none d-inline-block mb-3">
                     <i><image src="../images/logo.jpg" style="width:50px; height:50px;"></image></i>
                 </a>
                <h3 class="fw-bold mb-1">Mừng bạn quay lại!</h3>
                <p class="text-muted small">Khám phá và xây dựng ước mơ cùng <span class="gradient-text fw-bold">PortfolioHub</span></p>
            </div>

            <!-- Social Login Buttons -->
            <div class="d-flex flex-column gap-2 mb-4">
                <button class="btn btn-social btn-sm py-2 d-flex align-items-center justify-content-center gap-2 rounded-pill" onclick="simulateSocialLogin('Google')">
                    <i class="bi bi-google text-danger"></i>
                    <span class="small fw-semibold">Tiếp tục với Google</span>
                </button>
                <button class="btn btn-social btn-sm py-2 d-flex align-items-center justify-content-center gap-2 rounded-pill" onclick="simulateSocialLogin('GitHub')">
                    <i class="bi bi-github"></i>
                    <span class="small fw-semibold">Tiếp tục với GitHub</span>
                </button>
            </div>

            <!-- Separator -->
            <div class="d-flex align-items-center my-3">
                <hr class="flex-grow-1 text-muted">
                <span class="px-2 text-muted small">hoặc dùng Email</span>
                <hr class="flex-grow-1 text-muted">
            </div>

            <form id="loginForm" action="../actions/login.php" method="POST">
                <div class="mb-3">
                    <label for="loginEmail" class="form-label small fw-semibold">Địa chỉ Email</label>
                    <div class="input-group">
                        <span class="input-group-text bg-body-tertiary border-end-0"><i class="bi bi-envelope text-muted"></i></span>
                        <input type="email" id="loginEmail" name="email" class="form-control border-start-0" placeholder="name@example.com" required >
                    </div>
                </div>

                <div class="mb-3">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <label for="loginPassword" class="form-label small fw-semibold mb-0">Mật khẩu</label>
                        <a href="#" class="text-decoration-none small" onclick="forgotPassword()">Quên mật khẩu?</a>
                    </div>
                    <div class="input-group">
                        <span class="input-group-text bg-body-tertiary border-end-0"><i class="bi bi-lock text-muted"></i></span>
                        <input type="password" id="loginPassword" name="password" class="form-control border-start-0 border-end-0" placeholder="••••••••" required minlength="8" maxlength="32">
                        <button class="btn btn-outline-secondary border-start-0 bg-body-tertiary text-muted" type="button" id="togglePasswordBtn" onclick="togglePasswordVisibility()">
                            <i class="bi bi-eye-fill" id="passwordIcon"></i>
                        </button>
                    </div>
                </div>

                <div class="mb-4 d-flex align-items-center justify-content-between">
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="rememberMe">
                        <label class="form-check-label small" for="rememberMe">Ghi nhớ đăng nhập</label>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary w-100 rounded-pill py-2 fw-semibold shadow-sm" id="submitBtn">
                    <span class="spinner-border spinner-border-sm d-none" id="loadingSpinner"></span>
                    <span id="btnText">Đăng Nhập</span>
                </button>
            </form>

            <!-- Footer Card -->
            <div class="text-center mt-4 pt-2">
                <p class="text-muted small mb-0">Chưa có tài khoản? <a href="register.php" class="text-decoration-none fw-semibold" onclick="simulateRegister()">Đăng ký miễn phí</a></p>
            </div>

        </div>
    </div>

    <div class="position-fixed bottom-0 start-0 p-3" style="z-index: 11">
        <div id="liveToast" class="toast align-items-center text-white bg-dark border-0" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body" id="toastMessage">Thông báo!</div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        </div>
    </div>

    <!-- Bootstrap & Custom JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // --- Toggle Password Visibility ---
        function togglePasswordVisibility() {
            const passwordInput = document.getElementById("loginPassword");
            const passwordIcon = document.getElementById("passwordIcon");
            if (passwordInput.type === "password") {
                passwordInput.type = "text";
                passwordIcon.className = "bi bi-eye-slash-fill";
            } else {
                passwordInput.type = "password";
                passwordIcon.className = "bi bi-eye-fill";
            }
        }

        // --- alert time out  ---  
        setTimeout(() => {
        const alert = document.querySelector(".alert");
        if (alert) {
        bootstrap.Alert.getOrCreateInstance(alert).close();
        }
        }, 3000);
    </script>
</body>
</html>
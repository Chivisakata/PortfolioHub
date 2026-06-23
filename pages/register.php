<!DOCTYPE html>
<html lang="vi" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng ký thành viên - PortfoliHub</title>
    
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
        .register-card {
            width: 100%;
            max-width: 440px;
            border-radius: 16px;
            border: 1px solid rgba(0, 0, 0, 0.08);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.05);
            transition: transform 0.2s;
        }
        [data-bs-theme="dark"] .register-card {
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
        .password-strength-bar {
            height: 4px;
            transition: all 0.3s ease;
        }
    </style>
</head>
<body class="bg-body-tertiary">
    <!-- Alert Messages -->
    <?php
    session_start();
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
     <?php
    if (isset($_SESSION["success"])) {
    ?>
    <div class="position-fixed top-0 end-0 p-3" style="z-index: 9999;">
        <div class="alert alert-success alert-dismissible fade show shadow" role="alert">
            <?= htmlspecialchars($_SESSION["success"]) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    </div>
    <?php
        unset($_SESSION["success"]);
    }
    ?>
    

    <div class="position-absolute top-0 end-0 p-3">
        <button class="btn btn-link text-body p-2" id="themeToggleBtn" title="Đổi giao diện Sáng/Tối">
            <i class="bi bi-sun-fill fs-5" id="themeIcon"></i>
        </button>
    </div>

    <div class="container p-3">
        <div class="card register-card mx-auto p-4 p-md-5 bg-body">
            
            <!-- Logo & Title -->
            <div class="text-center mb-4">
                <a href="../index.html" class="text-decoration-none d-inline-block mb-3">
                    <i><image src="../images/logo.jpg" style="width:50px; height:50px;"></image></i>
                </a>
                <h3 class="fw-bold mb-1">Tạo tài khoản mới</h3>
                <p class="text-muted small">Bắt đầu thiết kế portfolio cá nhân cùng <span class="gradient-text fw-bold">PortfoliHub</span></p>
            </div>

            <!-- Social Registration Buttons -->
            <div class="d-flex flex-column gap-2 mb-4">
                <button class="btn btn-social btn-sm py-2 d-flex align-items-center justify-content-center gap-2 rounded-pill" onclick="simulateSocialRegister('Google')">
                    <i class="bi bi-google text-danger"></i>
                    <span class="small fw-semibold">Đăng ký bằng Google</span>
                </button>
                <button class="btn btn-social btn-sm py-2 d-flex align-items-center justify-content-center gap-2 rounded-pill" onclick="simulateSocialRegister('GitHub')">
                    <i class="bi bi-github"></i>
                    <span class="small fw-semibold">Đăng ký bằng GitHub</span>
                </button>
            </div>

            <!-- Separator -->
            <div class="d-flex align-items-center my-3">
                <hr class="flex-grow-1 text-muted">
                <span class="px-2 text-muted small">hoặc dùng Email</span>
                <hr class="flex-grow-1 text-muted">
            </div>

            <!-- Registration Form -->
            <form id="registerForm" action="../actions/register.php" method="POST">
                <!-- Email Field -->
                <div class="mb-3">
                    <label for="regEmail" class="form-label small fw-semibold">Địa chỉ Email</label>
                    <div class="input-group">
                        <span class="input-group-text bg-body-tertiary border-end-0"><i class="bi bi-envelope text-muted"></i></span>
                        <input type="email" id="regEmail" name="regEmail" class="form-control border-start-0" placeholder="name@example.com" required>
                    </div>
                </div>

                <!-- Password Field -->
                <div class="mb-3">
                    <label for="regPassword" class="form-label small fw-semibold">Mật khẩu</label>
                    <div class="input-group">
                        <span class="input-group-text bg-body-tertiary border-end-0"><i class="bi bi-lock text-muted"></i></span>
                        <input type="password" id="regPassword" name="regPassword" class="form-control border-start-0 border-end-0" placeholder="Tối thiểu 8 ký tự" required minlength="8" maxlength="32">
                        <button class="btn btn-outline-secondary border-start-0 bg-body-tertiary text-muted" type="button" onclick="togglePasswordVisibility('regPassword', 'passwordIcon')">
                            <i class="bi bi-eye-fill" id="passwordIcon"></i>
                        </button>
                    </div>
                </div>

                <!-- Confirm Password Field -->
                <div class="mb-3">
                    <label for="confirmPassword" class="form-label small fw-semibold">Nhập lại mật khẩu</label>
                    <div class="input-group">
                        <span class="input-group-text bg-body-tertiary border-end-0"><i class="bi bi-shield-lock text-muted"></i></span>
                        <input type="password" id="confirmPassword" name="confirmPassword" class="form-control border-start-0 border-end-0" placeholder="••••••••" required>
                        <button class="btn btn-outline-secondary border-start-0 bg-body-tertiary text-muted" type="button" onclick="togglePasswordVisibility('confirmPassword', 'confirmPasswordIcon')">
                            <i class="bi bi-eye-fill" id="confirmPasswordIcon"></i>
                        </button>
                    </div>
                    <div id="matchText" class="form-text small mt-1" style="font-size: 0.75rem;"></div>
                </div>

                <!-- Terms & Conditions Agree Checkbox -->
                <div class="mb-4">
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="termsCheck" required>
                        <label class="form-check-label small" for="termsCheck">
                            Tôi đồng ý với <a href="#" class="text-decoration-none">Điều khoản</a> và <a href="#" class="text-decoration-none">Bảo mật</a>
                        </label>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary w-100 rounded-pill py-2 fw-semibold shadow-sm" id="submitBtn">
                    <span class="spinner-border spinner-border-sm d-none" id="loadingSpinner"></span>
                    <span id="btnText">Đăng Ký</span>
                </button>
            </form>

            <!-- Footer Link -->
            <div class="text-center mt-4 pt-2">
                <p class="text-muted small mb-0">Đã có tài khoản? <a href="login.php" class="text-decoration-none fw-semibold">Đăng nhập</a></p>
            </div>

        </div>
    </div>

    <!-- Notification Toast System -->
    <div class="position-fixed bottom-0 start-0 p-3" style="z-index: 11">
        <div id="liveToast" class="toast align-items-center text-white bg-dark border-0" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body" id="toastMessage">Thông báo!</div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        </div>
    </div>

    <!-- Bootstrap bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <script> 

        // --- Toggle Password Visibility Utility ---
        function togglePasswordVisibility(inputId, iconId) {
            const passwordInput = document.getElementById(inputId);
            const passwordIcon = document.getElementById(iconId);
            if (passwordInput.type === "password") {
                passwordInput.type = "text";
                passwordIcon.className = "bi bi-eye-slash-fill";
            } else {
                passwordInput.type = "password";
                passwordIcon.className = "bi bi-eye-fill";
            }
        }


        // --- check validate ---    
        document.getElementById('registerForm').addEventListener('submit', function(event) {
                // 1. Lấy giá trị mật khẩu user nhập vào
                const passwordInput = document.getElementById('regPassword');
                const confirmPasswordInput = document.getElementById('confirmPassword');
                const password = passwordInput.value;
                const confirmPassword = confirmPasswordInput.value;

                // 2. Định nghĩa "bùa chú" Regex
                // Ý nghĩa: 
                // ^(?=.*[a-zA-Z0-9]) -> Phải có ít nhất 1 chữ (hoa/thường) HOẶC 1 số
                // ^[^\s]+$           -> Không được chứa khoảng trắng (dấu cách)
                const passwordPattern = /^(?=.*[a-zA-Z0-9])[a-zA-Z0-9!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]{8,32}$/; 

                passwordInput.setCustomValidity(""); // Reset custom validity
                confirmPasswordInput.setCustomValidity(""); // Reset custom validity
                if (!passwordPattern.test(password)) {
                    passwordInput.setCustomValidity("Mật khẩu chỉ chứa kí tự A-Z, a-z hoặc 0-9 và kí tự đặc biệt, không chứa khoảng trắng và có ít nhất 1 chữ hoặc 1 số nhé!");
                    passwordInput.reportValidity();
                    event.preventDefault(); 
                    passwordInput.focus();
                }
                // 4. Kiểm tra xem mật khẩu và xác nhận mật khẩu có khớp không
                if (password !== confirmPassword) {
                    confirmPasswordInput.setCustomValidity("Mật khẩu xác nhận không khớp!");
                    confirmPasswordInput.reportValidity();
                    event.preventDefault();
                    confirmPasswordInput.focus();
                }
            });
            const passwordInput = document.getElementById('regPassword');
            passwordInput.addEventListener('input', function () {
            passwordInput.setCustomValidity("");
            });

            const confirmPasswordInput = document.getElementById('confirmPassword');
            confirmPasswordInput.addEventListener('input', function () {
            confirmPasswordInput.setCustomValidity("");
            });



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
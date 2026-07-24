<?php
session_start();
require_once '../config/dbContext.php'; 

// 1. Kiểm tra đăng nhập
if (!isset($_SESSION['userId'])) {
    header("Location: ../pages/login.php");
    exit();
}

$currentUserId = $_SESSION['userId'];

// 2. Query lấy thông tin các profile đã thích
$sql = "SELECT p.*, 
               userdetails.field AS field,
               userdetails.skills as skills,
               (SELECT COUNT(*) FROM likes WHERE uid = p.uid) AS likes
        FROM profiles p
        INNER JOIN likes l ON p.uid = l.uid
        LEFT JOIN category c ON p.id = c.id
        LEFT JOIN userdetails ON p.uid = userdetails.uid
        WHERE l.liked_by = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $currentUserId); 
$stmt->execute();
$result = $stmt->get_result();
$portfolios = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portfolio đã thích</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
          <style>
            :root {
            --primary-gradient: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
            --accent-gradient: linear-gradient(135deg, #3b82f6 0%, #06b6d4 100%);
            --font-family: 'Inter', sans-serif;
            }

            body {
            font-family: var(--font-family);
            transition: background-color 0.3s ease, color 0.3s ease;        /* Transition animation when changing theme */
            overflow-x: hidden;
        }
            .feature-icon-box {
            width: 60px;
            height: 60px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, rgba(99, 102, 241, 0.1) 0%, rgba(124, 58, 237, 0.1) 100%);
            color: #4f46e5;
            font-size: 1.5rem;
            margin-bottom: 20px;
        }

            .gradient-text {
                background: var(--primary-gradient);
                font-weight: 800;
            }

            .accent-gradient-text {
                background: var(--accent-gradient);
            }
            .btn-gradient {
                background-image: linear-gradient(to right, #6a11cb 0%, #2575fc 100%);
                border: none; /* Remove default border if needed */
                color: white;
            }   

            .home-section {
                padding: 120px 0 80px 0;
                background: linear-gradient(to right, rgba(48,207,208,.3), rgba(51,8,103,.3))                     /* For newer version of browser */
            }

            .search-container {
                max-width: 600px;
                margin: 0 auto;
                border-radius: 50px;
                padding: 6px 6px 6px 16px;
                background: var(--bs-tertiary-bg);
                box-shadow: 0 4px 24px rgba(0,0,0,0.06);
                display: flex;
                align-items: center;
                border: 1px solid rgba(0,0,0,0.05);
            }

            [data-bs-theme="dark"] .search-container {
                border-color: rgba(255,255,255,0.05);
            }

            .search-container input {
                border: none;
                background: transparent;
                outline: none;
                width: 100%;
                padding: 8px;
                color: var(--bs-body-color);
            }

           .card-portfolio {
            border: none;
            border-radius: 16px;
            overflow: hidden;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            background: var(--bs-body-bg);
            box-shadow: 0 4px 20px rgba(0,0,0,0.3);
            }

            .card-portfolio:hover {
                transform: translateY(-8px);
                box-shadow: 0 12px 30px rgba(79, 70, 229, 0.45);
            }

            .category-badge {
                cursor: pointer;
                transition: all 0.2s ease;
                border: 1px solid rgba(0,0,0,0.08);
                border-radius: 50px;
                font-size: 0.9rem;
                padding: 8px 18px;
            }
            [data-bs-theme="dark"] .category-badge {
            border-color: rgba(255,255,255,0.1);
            }

            .category-badge.active, .category-badge:hover {
                background: var(--primary-gradient);
                color: #fff !important;
                border-color: transparent;
                box-shadow: 0 4px 12px rgba(99, 102, 241, 0.3);
            }
            /* Định hình góc bo tròn mềm mại cho menu thả xuống */
            .dropdown-menu {
                border-radius: 12px !important;
                padding: 8px;
            }
            .dropdown-item {
                border-radius: 8px;
            }
        </style>
</head>
<body>
 <nav class="navbar navbar-expand-lg fixed-top border-bottom py-3" style="backdrop-filter: blur(12px); border-color: black;">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center gap-2" href="../index.php">
                <span class="p-2 rounded-3 d-flex align-items-center justify-content-center text-white">
                    <img src="../images/logo.jpg" style="width: 50px; height: 50px; border-radius: 8px;" alt="Logo">
                </span>
                <span class="fs-4 fw-bold text-body">Portfolio<span class="text-primary">Hub</span></span>
            </a>
                
            <!-- Nút Hamburger hiển thị trên mobile -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent" aria-controls="navbarContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarContent">
                <div class="d-flex flex-column flex-lg-row align-items-start align-items-lg-center gap-3 ms-auto mt-3 mt-lg-0">
                    
                    <!-- Light/Dark Mode Switcher -->
                    <button class="btn btn-link text-body p-0 p-lg-2" id="themeToggleBtn" type="button" aria-label="Đổi chủ đề" title="Đổi giao diện Sáng/Tối">
                        <i class="bi bi-sun-fill fs-5" id="themeIcon"></i>
                    </button>
                    
                    <!-- Auth Actions -->
                    <?php if (isset($_SESSION["userId"])): ?>
                        <div class="dropdown" id="userDropdownBlock">
                            <a class="d-flex align-items-center gap-2 text-decoration-none text-body dropdown-toggle border-0 outline-none shadow-none" href="#" role="button" id="profileDropdown" data-bs-toggle="dropdown" aria-expanded="false" style="box-shadow: none !important; outline: none !important;">
                                <div class="rounded-circle bg-transparent d-flex align-items-center justify-content-center overflow-hidden border" style="width: 40px; height: 40px; min-width: 40px;" id="userAvatar">
                                    <img src="<?= htmlspecialchars(!empty($_SESSION['pfp']) ? '../images/pfps/' . $_SESSION['pfp'] : '../images/profile.png') ?>" class="w-100 h-100 rounded-circle" style="object-fit: cover; display: block;" alt="Avatar">
                                </div>
                            </a>

                            <ul class="dropdown-menu dropdown-menu-end shadow-lg border mt-2" aria-labelledby="profileDropdown">
                                <li>
                                    <a class="dropdown-item d-flex align-items-center gap-2 py-2 text-body" href="pages/detail.php?id=<?= $_SESSION['userId'] ?>">
                                        <i class="bi bi-person-vcard text-primary fs-5"></i>
                                        <span>Portfolio của tôi</span>
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item py-2 d-flex align-items-center" href="#">
                                        <i class="bi bi-heart-fill text-danger me-2"></i>
                                        <span>Portfolio đã thích</span>
                                    </a>
                                    </li>
                                <li>
                                    <a class="dropdown-item d-flex align-items-center gap-2 py-2 text-body" href="#">
                                        <i class="bi bi-gear text-secondary fs-5"></i>
                                        <span>Cài đặt tài khoản</span>
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider my-1"></li>
                                <li>
                                    <a class="dropdown-item d-flex align-items-center gap-2 py-2 text-danger" href="../actions/signout.php">
                                        <i class="bi bi-box-arrow-right fs-5"></i>
                                        <span class="fw-medium">Đăng xuất</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    <?php else: ?>
                        <button class="btn btn-outline-secondary px-4 rounded-pill w-100 w-lg-auto" type="button" onclick="window.location.href='login.php'">Đăng nhập</button>
                        <button class="btn btn-primary px-4 rounded-pill shadow-sm w-100 w-lg-auto" style="background: var(--primary-gradient); border: none;" type="button" onclick="window.location.href='register.php'">Bắt đầu ngay</button>
                    <?php endif; ?>
                    
                </div>
            </div>
        </div>
    </nav>
    <div class="container py-5 mt-5">
        <h3 class="fw-bold mt-4 mb-4"><i class="bi bi-heart-fill text-danger me-2"></i>Portfolio bạn đã thích</h3>

        <div class="row g-4" id="portfolioGrid">
            <?php if (!empty($portfolios)): ?>
                <?php foreach ($portfolios as $portfolio): ?>
                    <div class="col-md-6 col-lg-4">
                       <div class="card card-portfolio h-100 p-3">
                            
                            <div class="d-flex align-items-center gap-3 mb-3">
                                <img src="<?= !empty($portfolio['pfp']) ? htmlspecialchars('../images/pfps/' . $portfolio['pfp']) : '../images/profile.png' ?>" 
                                     class="rounded-circle border" style="width: 50px; height: 50px; object-fit: cover;" alt="Avatar">
                                <div>
                                    <h5 class="fw-bold mb-0 text-body-emphasis" style="font-size: 1.05rem;"><?= htmlspecialchars($portfolio['name'] ?? 'Chưa cập nhật') ?></h5>
                                    <span class="text-primary fw-medium" style="font-size: 0.82rem;">
                                        <i class="bi bi-tag-fill me-1"></i><?= htmlspecialchars($portfolio['field'] ?? $portfolio['title'] ?? 'Chưa phân loại') ?>
                                    </span>
                                </div>
                            </div>

                            <div class="mb-3">
                                <h6 class="fw-bold text-body mb-2" style="font-size: 0.95rem;">
                                    <?= htmlspecialchars($portfolio['title'] ?? $portfolio['field'] ?? '') ?>
                                </h6>
                                
                                <div class="d-flex flex-wrap gap-1 mt-2">
                                        <?php 
                                        if (!empty($portfolio['skills'])) {
                                            // Tách chuỗi kỹ năng bằng dấu chấm phẩy
                                            $skillsArray = preg_split('/[;,]/', $portfolio['skills']);
                                            foreach ($skillsArray as $skill) {
                                                $trimmedSkill = trim($skill);
                                                if ($trimmedSkill !== '') {
                                                    echo '<span class="badge bg-secondary-subtle text-secondary-emphasis rounded-pill fw-normal px-2 py-1" style="font-size: 0.75rem;">' . htmlspecialchars($trimmedSkill) . '</span>';
                                                }
                                            }
                                        } else {
                                            echo '<span class="text-muted text-xs" style="font-size: 0.8rem;">Chưa cập nhật kỹ năng</span>';
                                        }
                                        ?>
                                    </div>
                            </div>

                            <div class="card-footer bg-transparent border-0 p-0 mt-auto pt-3 border-top d-flex align-items-center justify-content-between text-secondary" style="font-size: 0.85rem;">
                                <div class="d-flex align-items-center gap-1">
                                    <i class="bi bi-heart"></i>
                                    <span><?= (int)$portfolio['likes'] ?></span>
                                </div>
                                <a href="./detail.php?id=<?= $portfolio['uid'] ?>" class="text-primary text-decoration-none fw-semibold">
                                    Xem Portfolio <i class="bi bi-arrow-right"></i>
                                </a>
                            </div>

                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12 text-center text-muted py-5">
                    <i class="bi bi-heartbreak display-4 d-block mb-3"></i>
                    <p class="mb-0">Bạn chưa yêu thích portfolio nào.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
     <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // --- alert time out  ---  
        setTimeout(() => {
        const alert = document.querySelector(".alert");
        if (alert) {
        bootstrap.Alert.getOrCreateInstance(alert).close();
        }
        }, 3000);

        // --- Light/Dark Mode Toggle ---
        const themeToggleBtn = document.getElementById("themeToggleBtn");
        const themeIcon = document.getElementById("themeIcon");
        const htmlElement = document.documentElement;

        // Initialize Theme
        const storedTheme = localStorage.getItem("theme") || "light";
        htmlElement.setAttribute("data-bs-theme", storedTheme);
        updateThemeIcon(storedTheme);

        themeToggleBtn.addEventListener("click", () => {
            const currentTheme = htmlElement.getAttribute("data-bs-theme");
            const newTheme = currentTheme === "light" ? "dark" : "light";
            htmlElement.setAttribute("data-bs-theme", newTheme);
            localStorage.setItem("theme", newTheme);
            updateThemeIcon(newTheme);
            showToast(`Đã chuyển sang chế độ ${newTheme === 'light' ? 'Sáng' : 'Tối'}!`);
        });

        function updateThemeIcon(theme) {
            if (theme === "dark") {
                themeIcon.className = "bi bi-moon-fill fs-5";
            } else {
                themeIcon.className = "bi bi-sun-fill fs-5";
            }
        }
    </script>
</body>
</html>
<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once '../config/dbContext.php'; 

$keyword = isset($_GET['keyword']) ? trim($_GET['keyword']) : '';
$searchResults = [];

if (!empty($keyword)) {
    // Convert the keyword to lowercase safely handling UTF-8 characters
    $searchParam = '%' . mb_strtolower($keyword, 'UTF-8') . '%';

    // Added LOWER() to the database columns to make the search case-insensitive
    $sql = "SELECT u.id AS uid, p.name, p.bio, p.pfp, ud.skills AS tech, ud.field, c.cate_name AS category_name,
                   (SELECT COUNT(*) FROM likes WHERE likes.uid = u.id) AS likes,
                   0 AS views
            FROM users u
            LEFT JOIN profiles p ON u.id = p.uid
            LEFT JOIN userdetails ud ON u.id = ud.uid
            LEFT JOIN category c ON p.category = c.id
            WHERE LOWER(ud.skills) LIKE ? 
               OR LOWER(ud.field) LIKE ? 
               OR LOWER(c.cate_name) LIKE ?";
               
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        die("<div class='alert alert-danger'>Lỗi cú pháp SQL: " . $conn->error . "</div>");
    }
    
    $stmt->bind_param("sss", $searchParam, $searchParam, $searchParam);
    $stmt->execute();
    $searchResults = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
} else {
    // Trạng thái mặc định khi chưa tìm kiếm: Tải tất cả tài khoản người dùng
    $sql = "SELECT u.id AS uid, p.name, p.bio, p.pfp, ud.skills AS tech, ud.field, c.cate_name AS category_name,
                   (SELECT COUNT(*) FROM likes WHERE likes.uid = u.id) AS likes,
                   0 AS views
            FROM users u
            LEFT JOIN profiles p ON u.id = p.uid
            LEFT JOIN userdetails ud ON u.id = ud.uid
            LEFT JOIN category c ON p.category = c.id";
            
    $result = $conn->query($sql);
    if (!$result) {
        die("<div class='alert alert-danger'>Lỗi tải dữ liệu mặc định: " . $conn->error . "</div>");
    }
    $searchResults = $result->fetch_all(MYSQLI_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Tìm kiếm Thành viên - PortfolioHub</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
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

    </style>
</head>
<body>
<!-- Navbar (Thanh điều hướng cố định đã sửa responsive) -->
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

            <!-- SỬA TẠI ĐÂY: Bổ sung lớp collapse để thu gọn giao diện trên thiết bị nhỏ -->
            <div class="collapse navbar-collapse" id="navbarContent">
                <!-- Thêm flex-column trên mobile, flex-row trên PC và dịch qua phải bằng ms-auto -->
                <div class="d-flex flex-column flex-lg-row align-items-start align-items-lg-center gap-3 ms-auto mt-3 mt-lg-0">
                    
                    <!-- Light/Dark Mode Switcher -->
                    <button class="btn btn-link text-body p-0 p-lg-2" id="themeToggleBtn" type="button" aria-label="Đổi chủ đề" title="Đổi giao diện Sáng/Tối">
                        <i class="bi bi-sun-fill fs-5" id="themeIcon"></i>
                    </button>
                    
                    <!-- Auth Actions -->
                    <?php if (isset($_SESSION["userId"])): ?>
                        <div class="dropdown" id="userDropdownBlock">
                            <a class="d-flex align-items-center gap-2 text-decoration-none text-body dropdown-toggle" href="#" role="button" id="profileDropdown" data-bs-toggle="dropdown" aria-expanded="false" style="outline: none; box-shadow: none;">
                                <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center fw-bold" style="width: 40px; height: 40px; font-size: 0.95rem;" id="userAvatar">
                                    <?php echo isset($_SESSION["profileName"]) ? substr($_SESSION["profileName"], 0, 2) : 'U'; ?>
                                </div>
                                <span class="fw-semibold small" id="userFullName"><?php echo isset($_SESSION["profileName"]) ? $_SESSION["profileName"] : ''; ?></span>
                            </a>

                            <ul class="dropdown-menu dropdown-menu-end shadow-lg border mt-2" aria-labelledby="profileDropdown">
                                <li>
                                    <a class="dropdown-item d-flex align-items-center gap-2 py-2 text-body" href="pages/detail.php?id=<?= $_SESSION['userId'] ?>">
                                        <i class="bi bi-person-vcard text-primary fs-5"></i>
                                        <span>Portfolio của tôi</span>
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
                                    <a class="dropdown-item d-flex align-items-center gap-2 py-2 text-danger" href="actions/signout.php">
                                        <i class="bi bi-box-arrow-right fs-5"></i>
                                        <span class="fw-medium">Đăng xuất</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    <?php else: ?>
                        <!-- Thêm class w-100 w-lg-auto để các nút chiếm trọn chiều rộng khi thu nhỏ trên mobile -->
                        <button class="btn btn-outline-secondary px-4 rounded-pill w-100 w-lg-auto" type="button" onclick="window.location.href='login.php'">Đăng nhập</button>
                        <button class="btn btn-primary px-4 rounded-pill shadow-sm w-100 w-lg-auto" style="background: var(--primary-gradient); border: none;" type="button" onclick="window.location.href='register.php'">Bắt đầu ngay</button>
                    <?php endif; ?>
                    
                </div>
            </div>
        </div>
    </nav>
    <div calss="container" style="margin-top:130px;">
        <div class="search-container mt-4 mb-4">
        <i class="bi bi-search text-secondary fs-5 me-2"></i>
        <input type="text" id="homeSearchInput" placeholder="Tìm kiếm tài năng, kỹ năng (ví dụ: React, UI/UX, Figma...)" aria-label="Tìm kiếm portfolio" value="<?= htmlspecialchars($keyword) ?>">
        
        <button class="btn btn-primary px-4 py-2 rounded-pill shadow-sm" 
                style="background: var(--primary-gradient); border: none;" 
                onclick="const kw = encodeURIComponent(document.getElementById('homeSearchInput').value); window.location.href='searchingResult.php?keyword=' + kw;">
            Tìm
        </button>
    </div>
    </div>
    
    

    <div class="container py-5">
        <h4 class="mb-4 fw-bold text-secondary">
            <?= !empty($keyword) ? "Kết quả tìm kiếm cho: '" . htmlspecialchars($keyword) . "'" : "Khám phá tất cả các Portfolio" ?>
        </h4>

        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
            <?php if (!empty($searchResults)): ?>
                <?php foreach ($searchResults as $portfolio): ?>
                    <!-- Bỏ qua tài khoản hệ thống trống thông tin để giữ giao diện đẹp -->
                    <?php if (empty($portfolio['name']) && empty($portfolio['tech'])) continue; ?>

                    <div class="col">
                        <!-- Khởi đầu cấu trúc component Card chuẩn của bạn -->
                        <div class="card card-portfolio h-100 p-3">
                            <div class="d-flex align-items-center gap-3 mb-3">
                                <!-- Đường dẫn ảnh đại diện lùi 1 thư mục cấp cha nếu thư mục lưu trữ images nằm ở root -->
                                <img src="<?= htmlspecialchars($portfolio['pfp'] ?: '../images/profile.png') ?>" class="rounded-circle shadow-sm" style="width: 55px; height: 55px; object-fit: cover;" alt="Avatar">
                                <div>
                                    <h5 class="fw-bold mb-0 text-body" style="font-size: 1.1rem;"><?= htmlspecialchars($portfolio['name'] ?? 'Thành viên mới') ?></h5>
                                    <span class="text-xs text-primary fw-medium" style="font-size: 0.8rem;">
                                        <i class="bi bi-tag-fill me-1"></i><?= htmlspecialchars($portfolio['category_name'] ?? 'Chưa phân mục') ?>
                                    </span>
                                </div>
                            </div>
                            <div class="card-body p-0 mb-3">
                                <h6 class="fw-semibold text-body mb-2"><?= htmlspecialchars($portfolio['field'] ?? 'Lĩnh vực trống') ?></h6>
                                <div class="d-flex flex-wrap mt-2 text-secondary" style="font-size: 0.9rem;">
                                    <?= htmlspecialchars($portfolio['tech'] ?? 'Chưa cập nhật từ khóa công nghệ.') ?>
                                </div>
                            </div>
                            <div class="card-footer bg-transparent border-0 p-0 mt-auto pt-3 border-top d-flex align-items-center justify-content-between">
                                <div class="d-flex gap-3 text-secondary" style="font-size: 0.85rem;">
                                    <span><i class="bi bi-heart me-1"></i><?= (int)$portfolio['likes'] ?></span>
                                </div>
                                <button class="btn btn-link btn-sm p-0 text-primary text-decoration-none fw-semibold" onclick="window.location.href='detail.php?id=<?= $portfolio['uid'] ?>'">
                                    Xem Portfolio <i class="bi bi-arrow-right"></i>
                                </button>
                            </div>
                        </div>
                        <!-- Kết thúc cấu trúc Card -->
                    </div>

                <?php endforeach; ?>
            <?php else: ?>
                <div class="text-center w-100 py-5">
                    <i class="bi bi-search fs-1 text-muted d-block mb-3"></i>
                    <p class="text-muted fs-5">Không tìm thấy thành viên nào có kỹ năng hoặc lĩnh vực phù hợp.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
    <script>
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
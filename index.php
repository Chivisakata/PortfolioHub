
<?php
require_once 'config/dbContext.php';

try {
    $query = "
        SELECT 
            profiles.id, 
            name, 
            pfp, 
            field AS field, 
            MAX(tech) AS tech, 
            1250 AS views,  -- Hardcoded placeholder for layout testing
            340 AS likes    -- Hardcoded placeholder for layout testing
        FROM profiles
        LEFT JOIN userdetails ON profiles.id = userdetails.id
        LEFT JOIN projects ON profiles.uid = projects.uid
        GROUP BY profiles.id, name, pfp, field
        ORDER BY profiles.id ASC 
        LIMIT 7
    ";

    $stmt = $pdo->query($query);
    $portfolios = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch(PDOException $e) {
    die("Query failed: " . $e->getMessage());
}
?>


<!DOCTYPE html>
<html lang="en" data-bs-theme="light">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>PortfoliHub - Nền Tảng Thiết Kế & Chia Sẻ Portfolio Chuyên Nghiệp</title>
        
        <!-- Bootstrap 5.3.3 CSS -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
        <!-- Bootstrap Icons -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
        <!-- Google Fonts - Inter -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

        <style>
            :root {
            --primary-gradient: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
            --accent-gradient: linear-gradient(135deg, #3b82f6 0%, #06b6d4 100%);
            --font-family: 'Inter', sans-serif;
            }

            body {
                font-family: var(--font-family);
                overflow-x: hidden;
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
                background: -webkit-linear-gradient(to right, rgba(48,207,208,.2), rgba(51,8,103,.2));
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
        <!--Alert Messages -->
          <?php
            session_start();
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
        <nav class="navbar navbar-expand-lg fixed-top border-bottom py-3" style="backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px); border-color:black ;">        <!--Name on navbar-->
            <div class="container">
                <a class="navbar-brand d-flex align-items-center gap-2" href="#">
                <span class="p-2 rounded-3 d-flex align-items-center justify-content-center text-white">
                <i><image src="images/logo.jpg" style="width:50px; height:50px;"></image></i>
                </span>
                <span class="fs-4 fw-bold text-body">Portfolio<span class="text-primary">Hub</span></span>
                </a>
                
                <!--Hamburger button when window scale down-->
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent" aria-controls="navbarContent" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <!--Items in the middle of navbar-->
                <div class="collapse navbar-collapse" id="navbarContent">
                <ul class="navbar-nav mx-auto mb-2 mb-lg-0 gap-1 gap-lg-3">
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="#home">Trang chủ</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#explore">Khám phá</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#features">Tính năng</a>
                    </li>
                </ul>

                <div class="d-flex align-items-center gap-3">       <!--Buttons on the right side of navbar-->
                        <!-- Light/Dark Mode Switcher -->
                        <button class="btn btn-link text-body p-2" id="themeToggleBtn" type="button" aria-label="Đổi chủ đề" title="Đổi giao diện Sáng/Tối">
                            <i class="bi bi-sun-fill fs-5" id="themeIcon"></i>
                        </button>
                        <!-- Auth Actions -->
                        <?php if (isset($_SESSION["userId"])): ?>
                            <div class="dropdown" id="userDropdownBlock">
                                <a class="d-flex align-items-center gap-2 text-decoration-none text-body dropdown-toggle" href="#" role="button" id="profileDropdown" data-bs-toggle="dropdown" aria-expanded="false" style="outline: none; box-shadow: none;">
                                    <!-- Avatar tròn viết tắt tên -->
                                    <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center fw-bold" style="width: 40px; height: 40px; font-size: 0.95rem;" id="userAvatar">
                                        <?php echo isset($_SESSION["profileName"]) ? substr($_SESSION["profileName"], 0, 2) : 'U    '; ?>
                                    </div>
                                    <!-- Tên hiển thị (chỉ hiện trên màn hình máy tính) -->
                                    <span class="fw-semibold small d-none d-md-inline" id="userFullName"><?php echo isset($_SESSION["profileName"]) ? $_SESSION["profileName"] : ''; ?></span>
                                </a>

                                <!-- Danh sách thả xuống Dropdown Menu -->
                                <ul class="dropdown-menu dropdown-menu-end shadow-lg border mt-2" aria-labelledby="profileDropdown">
                                    <!-- Tên di động ẩn/hiện -->
                                    <li class="px-3 py-2 border-bottom d-md-none">
                                        <span class="d-block small fw-bold text-body" id="userFullNameMobile"><?php echo isset($_SESSION["profileName"]) ? $_SESSION["profileName"] : ''; ?></span>
                                        <span class="d-block text-muted" style="font-size: 0.75rem;" id="userEmailMobile"><?php echo isset($_SESSION["profileEmail"]) ? $_SESSION["profileEmail"] : ''; ?></span>
                                    </li>
                                    <li>
                                        <a class="dropdown-item d-flex align-items-center gap-2 py-2 text-body" href="detail.html">
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
                            <button class="btn btn-outline-secondary px-4 rounded-pill" type="button" onclick="window.location.href='./pages/login.php'">Đăng nhập</button>
                        <?php endif; ?>
                        <button class="btn btn-primary px-4 rounded-pill shadow-sm" style="background: var(--primary-gradient); border: none;" type="button">Bắt đầu ngay</button>
                    </div>
            </div>
        </div>
    </nav>

        <!--Home-->
        <section class="home-section w-100" id="home">
            <div class="container mt-5">
                <div class="row align-items-center g-5">
                    <div class="col-lg-12 text-center text-lg-start">
                        <h1 class="display-4 fw-extrabold text-body lh-sm mb-3">
                            Tỏa sáng với Portfolio của riêng bạn tại <span class="gradient-text">PortfolioHub</span>
                        </h1>
                        <p class="lead text-secondary mb-4">
                            Nơi hội tụ hàng nghìn hồ sơ năng lực ấn tượng từ các lập trình viên, nhà thiết kế, nhiếp ảnh gia và người sáng tạo nội dung hàng đầu. Thiết lập dễ dàng trong 5 phút.
                        </p>
                        
                        <!-- Integrated dynamic search inside home -->
                        <div class="search-container mb-4">
                            <i class="bi bi-search text-secondary fs-5 me-2"></i>
                            <input type="text" id="homeSearchInput" placeholder="Tìm kiếm tài năng, kỹ năng (ví dụ: React, UI/UX, Figma...)" aria-label="Tìm kiếm portfolio">
                            <button class="btn btn-primary px-4 py-2 rounded-pill shadow-sm" style="background: var(--primary-gradient); border: none;" onclick="triggerhomeSearch()">Tìm</button>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="py-5 bg-body border-top border-bottom" id="categories-section">
        <div class="container text-center">
            <h5 class="text-secondary uppercase mb-4 text-xs tracking-wider">KHÁM PHÁ THEO LĨNH VỰC HÀNG ĐẦU</h5>
            <div class="d-flex flex-wrap justify-content-center gap-3">
                <span class="category-badge active text-secondary" onclick="filterCategory('All', this)">
                    <i class="bi bi-grid-fill me-1"></i> Tất cả
                </span>
                <span class="category-badge text-secondary" onclick="filterCategory('Development', this)">
                    <i class="bi bi-code-slash me-1"></i> Phát triển (Dev)
                </span>
                <span class="category-badge text-secondary" onclick="filterCategory('Design', this)">
                    <i class="bi bi-brush-fill me-1"></i> Thiết kế (UI/UX)
                </span>
                <span class="category-badge text-secondary" onclick="filterCategory('Photography', this)">
                    <i class="bi bi-camera-fill me-1"></i> Nhiếp ảnh
                </span>
                <span class="category-badge text-secondary" onclick="filterCategory('Writing', this)">
                    <i class="bi bi-pen-fill me-1"></i> Viết lách & Content
                </span>
                <span class="category-badge text-secondary" onclick="filterCategory('Marketing', this)">
                    <i class="bi bi-megaphone-fill me-1"></i> Marketing
                </span>
            </div>
        </div>
    </section>

    <section class="py-5" id="explore">
        <div class="container">
            <div class="d-flex flex-wrap align-items-center justify-content-between mb-5">
                <div>
                    <h2 class="fw-bold mb-1 text-body">Mẫu Portfolio Nổi Bật</h2>
                    <p class="text-muted">Được chọn lọc và bình chọn bởi cộng đồng chất lượng</p>
                </div>
                <div class="d-flex gap-2">
                    <button class="btn btn-outline-secondary rounded-pill btn-sm px-3" onclick="sortPortfolios('views')">
                        <i class="bi bi-fire text-danger me-1"></i>Xem nhiều nhất
                    </button>
                    <button class="btn btn-outline-secondary rounded-pill btn-sm px-3" onclick="sortPortfolios('likes')">
                        <i class="bi bi-heart-fill text-danger me-1"></i>Yêu thích nhất
                    </button>
                </div>
            </div>

            <!-- Portfolio Grid -->
            <div class="row g-4" id="portfolioGrid">
                <!--card-->
                <div class="row g-4" id="portfolioGrid">
                    <?php if (!empty($portfolios)): ?>
                        <?php foreach ($portfolios as $portfolio): ?>
                            <div class="col-md-6 col-lg-4">
                                <div class="card card-portfolio h-100 p-3">
                                    <div class="d-flex align-items-center gap-3 mb-3">
                                        <img src="<?= htmlspecialchars($portfolio['pfp'] ?: 'images/profile.png') ?>" class="rounded-circle shadow-sm" style="width: 55px; height: 55px; object-fit: cover;" alt="Avatar">
                                        <div>
                                            <h5 class="fw-bold mb-0 text-body" style="font-size: 1.1rem;"><?= htmlspecialchars($portfolio['name']) ?></h5>
                                            <span class="text-xs text-primary fw-medium" style="font-size: 0.8rem;">
                                                <i class="bi bi-tag-fill me-1"></i><?= htmlspecialchars($portfolio['field']) ?>      <!--Add category-->
                                            </span>
                                        </div>
                                    </div>
                                    <div class="card-body p-0 mb-3">
                                        <h6 class="fw-semibold text-body mb-2"><?= htmlspecialchars($portfolio['field']) ?></h6>
                                        <div class="d-flex flex-wrap mt-2 text-secondary" style="font-size: 0.9rem;">
                                            <?= htmlspecialchars($portfolio['tech']) ?>
                                        </div>
                                    </div>
                                    <div class="card-footer bg-transparent border-0 p-0 mt-auto pt-3 border-top d-flex align-items-center justify-content-between">
                                        <div class="d-flex gap-3 text-secondary" style="font-size: 0.85rem;">
                                            <span><i class="bi bi-eye me-1"></i><?= (int)$portfolio['views'] ?></span>          <!--Add views and likes tables-->
                                            <span><i class="bi bi-heart me-1"></i><?= (int)$portfolio['likes'] ?></span>        <!--Add views and likes tables-->
                                        </div>
                                        <button class="btn btn-link btn-sm p-0 text-primary text-decoration-none fw-semibold" onclick="window.location.href='./pages/detail.php?id=<?= $portfolio['profiles.id'] ?>'">
                                            Xem Portfolio <i class="bi bi-arrow-right"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="col-12 text-center text-muted py-5">
                            <p class="mb-0">Hiện chưa có mẫu portfolio nào hiển thị.</p>
                        </div>
                    <?php endif; ?>
                </div>
              
            </div>
           
            <!--End of users card inforamtion-->

            <!-- Load More Mock Action -->
            <div class="text-center mt-5">
                <button class="btn btn-outline-primary px-5 py-3 rounded-pill fw-semibold" id="btnLoadMore" onclick="loadMorePortfolios()">
                    <span class="spinner-border spinner-border-sm d-none me-2" id="loadMoreSpinner" role="status"></span>
                    Xem thêm các thiết kế độc đáo khác
                </button>
            </div>
        </div>
    </section>

    <section class="py-5 text-white" style="background: var(--primary-gradient);">
        <div class="container py-4 text-center">
            <h2 class="fw-bold mb-3 display-6">Sẵn Sàng Xây Dựng Thương Hiệu Cá Nhân?</h2>
            <p class="lead mb-4">Tham gia cùng hơn 100,000+ lập trình viên và nhà thiết kế đang tỏa sáng mỗi ngày.</p>
                    <button class="btn btn-light text-primary fw-bold px-5 py-3 rounded-pill shadow"  onclick="window.location.href='./pages/login.html'" >Tạo Portfolio của Bạn Ngay</button>
        </div>
    </section>

    <!--Include footer-->
    <?php include "components/footer.php";?>
    
     <!-- Bootstrap & Custom JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
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

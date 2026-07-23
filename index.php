<?php
    session_start(); 

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
        <!--Alert Messages -->
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

        <!--Fetch data -->
        <?php
        require_once 'config/dbContext.php';

        try {
            // Cập nhật câu SQL: Lấy thêm cột ud.skills và thêm vào GROUP BY
            $query = "
                SELECT 
                    p.uid, 
                    p.name, 
                    p.pfp, 
                    ud.field AS field,
                    ud.skills AS skills,
                    MAX(pj.tech) AS tech,
                    (select count(*) from likes where uid = p.uid) as likes
                FROM profiles p
                LEFT JOIN userdetails ud ON p.uid = ud.uid 
                LEFT JOIN projects pj ON p.uid = pj.uid
                WHERE p.name IS NOT NULL
                AND TRIM(p.name) <> ''
                GROUP BY p.uid, p.name, p.pfp, ud.field, ud.skills
                ORDER BY likes DESC, p.id ASC
                LIMIT 20
            ";

            // Run query with MySQLi
            $result = $conn->query($query);

            if (!$result) {
                throw new Exception("Query failed: " . $conn->error);
            }

            // Fetch all rows as associative array
            $portfolios = [];
            while ($row = $result->fetch_assoc()) {
                $portfolios[] = $row;
            }

            } catch(Exception $e) {
                die("Query failed: " . $e->getMessage());
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
                        <?php if (isset($_SESSION["userId"])):?>
                            <div class="dropdown" id="userDropdownBlock">
                            <a class="d-flex align-items-center gap-2 text-decoration-none text-body dropdown-toggle border-0 outline-none shadow-none" href="#" role="button" id="profileDropdown" data-bs-toggle="dropdown" aria-expanded="false" style="box-shadow: none !important; outline: none !important;">
                                <!-- Avatar tròn bo góc hoàn hảo, dùng bg-transparent để không bị lộ viền xanh -->
                                <div class="rounded-circle bg-transparent d-flex align-items-center justify-content-center overflow-hidden border" style="width: 40px; height: 40px; min-width: 40px;" id="userAvatar">
                                    <img src="<?= htmlspecialchars(!empty($_SESSION['pfp']) ? 'images/pfps/' . $_SESSION['pfp'] : 'images/profile.png') ?>" class="w-100 h-100 rounded-circle" style="object-fit: cover; display: block;" alt="Avatar">
                                </div>
                                
                                <!-- Tên hiển thị -->
                                <span class="fw-semibold small d-none d-md-inline" id="userFullName">
                                    <?= htmlspecialchars($_SESSION["profileName"] ?? '') ?>
                                </span>

                            </a>

                                <!-- Danh sách thả xuống Dropdown Menu -->
                                <ul class="dropdown-menu dropdown-menu-end shadow-lg border mt-2" aria-labelledby="profileDropdown">
                                    <!-- Tên di động ẩn/hiện -->
                                    <li class="px-3 py-2 border-bottom d-md-none">
                                        <span class="d-block small fw-bold text-body" id="userFullNameMobile"><?php echo isset($_SESSION["profileName"]) ? $_SESSION["profileName"] : ''; ?></span>
                                        <span class="d-block text-muted" style="font-size: 0.75rem;" id="userEmailMobile"><?php echo isset($_SESSION["profileEmail"]) ? $_SESSION["profileEmail"] : ''; ?></span>
                                    </li>
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
                            <!-- Chỉ hiển thị cụm nút này khi CHƯA đăng nhập -->
                            <button class="btn btn-outline-secondary px-4 rounded-pill" type="button" onclick="window.location.href='./pages/login.php'">Đăng nhập</button>
                            <button class="btn btn-primary px-4 rounded-pill shadow-sm" style="background: var(--primary-gradient); border: none;" type="button" onclick="window.location.href='./pages/register.php'">Bắt đầu ngay</button>
                        <?php endif; ?>
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
                            
                            <!-- Cập nhật lại thuộc tính onclick để truyền kèm giá trị của ô input -->
                            <button class="btn btn-primary px-4 py-2 rounded-pill shadow-sm" 
                                    style="background: var(--primary-gradient); border: none;" 
                                    onclick="const kw = encodeURIComponent(document.getElementById('homeSearchInput').value); window.location.href='./pages/searchingResult.php?keyword=' + kw;">
                                Tìm
                            </button>
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
                    <button class="btn btn-outline-secondary rounded-pill btn-sm px-3" onclick="sortPortfolios('likes')">
                        <i class="bi bi-heart-fill text-danger me-1"></i>Yêu thích nhất
                    </button>
                </div>
            </div>

            <!-- Portfolio Grid -->
          <div class="row g-4" id="portfolioGrid">
                <?php if (!empty($portfolios)): ?>
                    <?php 
                    $index = 0;
                    foreach ($portfolios as $portfolio): 
                        // Gán class 'extra-profile' cố định và 'd-none' để ẩn ban đầu
                        $extraClass = ($index >= 6) ? 'extra-profile d-none' : '';
                        $index++;
                    ?>
                        <div class="col-md-6 col-lg-4 <?= $extraClass ?>">
                            <div class="card card-portfolio h-100 p-3">
                                <div class="d-flex align-items-center gap-3 mb-3">
                                <img src="<?= !empty($portfolio['pfp'])? htmlspecialchars('images/pfps/' . $portfolio['pfp']): 'images/profile.png' ?>" class="rounded-circle shadow-sm" style="width: 55px; height: 55px; object-fit: cover;" alt="Avatar">
                                    <div>
                                        <h5 class="fw-bold mb-0 text-body" style="font-size: 1.1rem;"><?= htmlspecialchars($portfolio['name']) ?></h5>
                                        <span class="text-xs text-primary fw-medium" style="font-size: 0.8rem;">
                                            <i class="bi bi-tag-fill me-1"></i><?= htmlspecialchars($portfolio['field']) ?>
                                        </span>
                                    </div>
                                </div>
                                <div class="card-body p-0 mb-3">
                                    <h6 class="fw-semibold text-body mb-2"><?= htmlspecialchars($portfolio['field']) ?></h6>
                                    
                                    <!-- Khu vực hiển thị Kỹ năng dưới dạng Badge nhỏ gọn, tinh tế -->
                                    <div class="d-flex flex-wrap gap-1 mt-2">
                                        <?php 
                                        if (!empty($portfolio['skills'])) {
                                            // Tách chuỗi kỹ năng bằng dấu chấm phẩy hoặc dấu phẩy
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
                                <div class="card-footer bg-transparent border-0 p-0 mt-auto pt-3 border-top d-flex align-items-center justify-content-between">
                                    <div class="d-flex gap-3 text-secondary" style="font-size: 0.85rem;">
                                        <span><i class="bi bi-heart me-1"></i><?= (int)$portfolio['likes'] ?></span>
                                    </div>
                                    <button class="btn btn-link btn-sm p-0 text-primary text-decoration-none fw-semibold" onclick="window.location.href='./pages/detail.php?id=<?= $portfolio['uid'] ?>'">
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
       
            <!-- End of users card information -->

            <!-- Load More Mock Action -->
             <?php if (!empty($portfolios) && count($portfolios) > 6): ?>
                <div class="text-center mt-4" id="seeMoreWrapper">
                    <button id="seeMoreBtn" class="btn btn-outline-primary rounded-pill px-4 py-2 fw-semibold">
                        <!-- Trạng thái 1: Xem thêm -->
                        <span class="btn-text-more">
                            Xem thêm <i class="bi bi-chevron-down ms-1"></i>
                        </span>
                        <!-- Trạng thái 2: Thu gọn (Mặc định ẩn) -->
                        <span class="btn-text-less d-none">
                            Thu gọn <i class="bi bi-chevron-up ms-1"></i>
                        </span>
                    </button>
                </div>
            <?php endif; ?>
        </div>
    </section>

                     
    <!-- How it Works Step-by-Step -->
    <section class="py-5" id="how-it-works">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="fw-bold text-body">Quy Trình Hoạt Động Cực Kỳ Đơn Giản</h2>
                <p class="text-muted">Thiết kế hoàn thiện chỉ với 3 bước cơ bản nhanh chóng</p>
            </div>

            <div class="row g-4 align-items-center">
                <div class="col-lg-4 text-center">
                    <div class="p-4 bg-body rounded-4 shadow-sm border h-100">
                        <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3 fs-3 fw-bold" style="width: 60px; height: 60px;">1</div>
                        <h5 class="fw-bold">Chọn Giao Diện Ưu Thích</h5>
                        <p class="text-muted mb-0">Lựa chọn từ kho template bắt mắt từ đơn giản đến nghệ thuật độc đáo.</p>
                    </div>
                </div>
                <div class="col-lg-4 text-center">
                    <div class="p-4 bg-body rounded-4 shadow-sm border h-100">
                        <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3 fs-3 fw-bold" style="width: 60px; height: 60px;">2</div>
                        <h5 class="fw-bold">Cập Nhật Thông Tin</h5>
                        <p class="text-muted mb-0">Điền thông tin giới thiệu, các dự án, kỹ năng của bạn mà không cần đụng đến code.</p>
                    </div>
                </div>
                <div class="col-lg-4 text-center">
                    <div class="p-4 bg-body rounded-4 shadow-sm border h-100">
                        <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3 fs-3 fw-bold" style="width: 60px; height: 60px;">3</div>
                        <h5 class="fw-bold">Xuất Bản & Chia Sẻ</h5>
                        <p class="text-muted mb-0">Chia sẻ đường dẫn chuyên nghiệp đến các nhà tuyển dụng hàng đầu trên thế giới.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="py-5 text-white" style="background: var(--primary-gradient);">
        <div class="container py-4 text-center">
            <h2 class="fw-bold mb-3 display-6">Sẵn Sàng Xây Dựng Thương Hiệu Cá Nhân?</h2>
            <p class="lead mb-4">Tham gia cùng hơn 100,000+ lập trình viên và nhà thiết kế đang tỏa sáng mỗi ngày.</p>
                    <button class="btn btn-light text-primary fw-bold px-5 py-3 rounded-pill shadow"  onclick="window.location.href='<?php echo isset($_SESSION['userId']) ? "pages/createprofile.php" : "pages/login.php"; ?>'">Tạo Portfolio của Bạn Ngay</button>
        </div>
    </section>

    <!-- ================= KHU VỰC CHỨNG CHỈ XML DOM ================= -->
    <section class="py-5 bg-body-tertiary border-top border-bottom" id="certificates-section">
        <div class="container text-center">
            <div class="mb-4">
                <span class="badge bg-primary-subtle text-primary rounded-pill px-3 py-2 mb-2">
                    <i class="bi bi-patch-check-fill me-1"></i> Dữ liệu chuẩn quốc tế
                </span>
                <h3 class="fw-bold text-body mb-2">Chứng Chỉ Công Nghệ Hàng Đầu</h3>
                <p class="text-muted small mx-auto" style="max-width: 600px;">
                    Tra cứu các chứng chỉ chuyên môn được săn đón nhất trong ngành IT
            </div>

            <!-- Nút bấm Bật / Tắt danh sách -->
            <button class="btn btn-outline-primary rounded-pill px-4 py-2 fw-semibold shadow-sm" id="btnToggleCert" onclick="toggleCertificates()">
                </i> Xem các chứng chỉ phổ biến
            </button>

            <!-- Khung chứa bảng dữ liệu XML (Mặc định ẩn d-none) -->
            <div class="mt-4 d-none" id="certContainer">
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden text-start">
                    <div class="card-header bg-primary text-white py-3 d-flex justify-content-between align-items-center">
                        <h6 class="fw-bold mb-0"></i> Danh sách chứng chỉ</h6>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light small text-uppercase">
                                <tr>
                                    <th style="width: 80px;" class="text-center">Mã ID</th>
                                    <th>Tên chứng chỉ</th>
                                    <th>Tổ chức cấp</th>
                                    <th>Lĩnh vực</th>
                                    <th>Cấp độ</th>
                                    <th>Mô tả chi tiết</th>
                                </tr>
                            </thead>
                            <tbody id="certTableBody" class="small">
                                <!-- Dữ liệu XML sẽ được duyệt Node và chèn vào đây -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
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

        // --- Toggle Certificates Section ---
        let isXmlLoaded = false;

    function toggleCertificates() {
        const container = document.getElementById("certContainer");
        const btn = document.getElementById("btnToggleCert");

        // Nếu đang ẩn -> Hiện lên
        if (container.classList.contains("d-none")) {
            // Nếu chưa tải dữ liệu XML thì gọi AJAX tải
            if (!isXmlLoaded) {
                loadCertificatesXML();
            }
            container.classList.remove("d-none");
            btn.innerHTML = `<i class="bi bi-eye-slash me-1"></i> Ẩn danh sách chứng chỉ`;
            btn.className = "btn btn-outline-danger rounded-pill px-4 py-2 fw-semibold shadow-sm";
        } 
        // Nếu đang hiện -> Ẩn đi
        else {
            container.classList.add("d-none");
            btn.innerHTML = `<i class="bi bi-award me-1"></i> Xem các chứng chỉ phổ biến`;
            btn.className = "btn btn-outline-primary rounded-pill px-4 py-2 fw-semibold shadow-sm";
        }
    }

    function loadCertificatesXML() {
        // 1. Khởi tạo đối tượng XMLHttpRequest
        const xhr = new XMLHttpRequest();

        xhr.onreadystatechange = function() {
            if (this.readyState === 4 && this.status === 200) {
                const xmlDoc = this.responseXML;
                const tbody = document.getElementById("certTableBody");
                tbody.innerHTML = "";

                // Lấy danh sách các thẻ <cert>
                const certNodes = xmlDoc.getElementsByTagName("cert");
    

                // 2. Vận dụng các thuộc tính Node (firstChild, nextSibling) để duyệt XML
                for (let i = 0; i < certNodes.length; i++) {
                    const cert = certNodes[i];
                    const certId = cert.getAttribute("id");

                    // Bắt đầu từ node con đầu tiên của <cert> bằng firstChild
                    let childNode = cert.firstChild;

                    let certName = "", org = "", field = "", level = "", desc = "";

                    // Duyệt qua tất cả các sibling node con bằng nextSibling
                    while (childNode) {
                        // Chỉ xử lý các Element node (nodeType === 1) để bỏ qua ký tự xuống dòng / khoảng trắng
                        if (childNode.nodeType === 1) {
                            const tagName = childNode.nodeName;
                            const textContent = childNode.textContent.trim();

                            if (tagName === "name") certName = textContent;
                            else if (tagName === "organization") org = textContent;
                            else if (tagName === "field") field = textContent;
                            else if (tagName === "level") level = textContent;
                            else if (tagName === "description") desc = textContent;
                        }
                        // Nhảy sang node kế tiếp bằng nextSibling
                        childNode = childNode.nextSibling;
                    }

                    // Render dữ liệu vào dòng của bảng
                    const row = `
                        <tr>
                            <td class="text-center fw-bold text-secondary">${certId}</td>
                            <td class="fw-bold text-primary">${certName}</td>
                            <td><span class="badge bg-secondary-subtle text-secondary">${org}</span></td>
                            <td><i class="bi bi-tag me-1"></i>${field}</td>
                            <td><span class="badge bg-info-subtle text-info-emphasis">${level}</span></td>
                            <td class="text-muted" style="max-width: 300px;">${desc}</td>
                        </tr>
                    `;
                    tbody.innerHTML += row;
                }

                isXmlLoaded = true; // Đánh dấu đã nạp thành công
            }
        };

        // Mở kết nối và gửi yêu cầu tới file certificates.xml
        xhr.open("GET", "XML/certificates.xml", true);
        xhr.send();
    }
    </script>  

    <!--Reload page automatically-->
    <script>
        window.addEventListener('pageshow', function (event) {
            // Kiểm tra nếu trang được tải ra từ bộ nhớ tạm (Cache/BFCache)
            var historyTraversal = event.persisted || 
                (typeof window.performance != 'undefined' && window.performance.navigation.type === 2);
            
            if (historyTraversal) {
                // Tải lại trang để lấy số lượt thích mới nhất từ database
                window.location.reload();
            }
        });
        </script>                  

        <!--Xem them button-->
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const seeMoreBtn = document.getElementById('seeMoreBtn');

                if (seeMoreBtn) {
                    seeMoreBtn.addEventListener('click', function (e) {
                        e.preventDefault();
                        
                        const extraCards = document.querySelectorAll('.extra-profile');
                        const textMore = seeMoreBtn.querySelector('.btn-text-more');
                        const textLess = seeMoreBtn.querySelector('.btn-text-less');
                        const isExpanded = seeMoreBtn.getAttribute('data-expanded') === 'true';

                        // 1. Ẩn / Hiện các card từ thứ 7 trở đi
                        extraCards.forEach(card => card.classList.toggle('d-none', isExpanded));

                        // 2. Chuyển đổi giữa "Xem thêm" và "Thu gọn"
                        if (textMore && textLess) {
                            textMore.classList.toggle('d-none', !isExpanded);
                            textLess.classList.toggle('d-none', isExpanded);
                        }

                        // 3. Cập nhật thuộc tính trạng thái
                        seeMoreBtn.setAttribute('data-expanded', !isExpanded);

                        // 4. Cuộn mượt lên lại đầu danh sách khi thu gọn
                        if (isExpanded) {
                            document.getElementById('portfolioGrid').scrollIntoView({ behavior: 'smooth' });
                        }
                    });
                }
            });
            </script>
    </body>
</html>
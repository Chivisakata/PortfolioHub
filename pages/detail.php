<?php
// 1. MUST BE AT THE ABSOLUTE TOP to enable user session tracking!
session_start();

require_once '../config/dbContext.php'; 

// 2. Capture the unique user id sent from the URL query parameter (?id=X)
$userId = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($userId <= 0) {
    die("Yêu cầu không hợp lệ. Không tìm thấy ID người dùng.");
}

try {
    // 3. Fetch basic profile info matching this specific ID
    $userQuery = "
        SELECT 
            name, 
            profiles.pfp AS pfp, 
            field,
            bio,
            email,
            website,
            location
        FROM profiles
        LEFT JOIN userdetails ON userdetails.uid = profiles.uid
        WHERE userdetails.uid = ?
    ";

    $stmt = $conn->prepare($userQuery);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if (!$user) {
        die("Người dùng này không tồn tại trong hệ thống.");
    }

    // 4. Fetch all projects tied to this user's unique id (projects.uid)
    $projectQuery = "
        SELECT project_name, tech, description 
        FROM projects 
        WHERE uid =?
    ";

    $stmtProject = $conn->prepare($projectQuery);
    $stmtProject->bind_param("i", $userId);
    $stmtProject->execute();
    $resultProjects = $stmtProject->get_result();
    $projects = [];
    while ($row = $resultProjects->fetch_assoc()) {
        $projects[] = $row;
    }
  
    // Showing not set avatar with the initials of user's name
    $nameParts = explode(" ", $user['name']);
    $initials = (count($nameParts) >= 2) 
        ? mb_substr($nameParts[count($nameParts)-2], 0, 1) . mb_substr($nameParts[count($nameParts)-1], 0, 1)
        : mb_substr($user['name'], 0, 2);
    $initials = mb_strtoupper($initials);

} catch(Exception $e) {
    die("Lỗi kết nối cơ sở dữ liệu: " . $e->getMessage());
}

// 5. Updated Login Fallback to -1 (Fixes the admin ID 0 collision)
$currentLoggedInUser = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : -1;
$hasLiked = false;

if ($currentLoggedInUser !== -1) {
    // Check if the current logged-in user is one of the people who liked this profile
    $likeCheck = "SELECT * FROM likes WHERE uid = ? AND liked_by = ?";
    $stmtLike = $conn->prepare($likeCheck);
    $stmtLike->bind_param("ii", $userId, $currentLoggedInUser);
    $stmtLike->execute();
    $likeResult = $stmtLike->get_result();
    
    if ($likeResult->num_rows > 0) {
        $hasLiked = true; 
    }
    $stmtLike->close();
}

// Get the total number of likes for this profile (counting rows matching this uid)
$countQuery = "SELECT COUNT(*) as total_likes FROM likes WHERE uid = ?";
$stmtCount = $conn->prepare($countQuery);
$stmtCount->bind_param("i", $userId);
$stmtCount->execute();
$countResult = $stmtCount->get_result()->fetch_assoc();
$totalLikes = $countResult['total_likes'];
$stmtCount->close();
?>

<!DOCTYPE html>
<html lang="en" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chi tiết Portfolio - <?php echo htmlspecialchars($user['name']); ?></title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800&display=swap" rel="stylesheet">

    <style>
        body { font-family: 'Inter', sans-serif; transition: background-color 0.3s, color 0.3s; }
        .gradient-bg { background: linear-gradient(135deg, #4f46e5 0%, #ec4899 100%); }
        .profile-cover { height: 220px; border-radius: 24px; margin-top: 24px; }
        .avatar-overlap { margin-top: -80px; border: 6px solid var(--bs-body-bg); box-shadow: 0 10px 20px rgba(0,0,0,0.1); }
        .timeline-item { position: relative; padding-left: 30px; border-left: 2px solid var(--bs-border-color); margin-bottom: 30px; }
        .timeline-item::before { content: ''; position: absolute; left: -7px; top: 5px; width: 12px; height: 12px; border-radius: 50%; background-color: #4f46e5; }
        .card-custom { border-radius: 20px; border: 1px solid var(--bs-border-color); box-shadow: 0 4px 12px rgba(0,0,0,0.02); }
        p {font-size: 18px;}
        .web:hover{cursor:pointer;}
    </style>
</head>
<body class="bg-body-tertiary">
    <nav class="navbar navbar-expand-lg fixed-top border-bottom py-3" style="backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px); border-color:black ;">        <!--Name on navbar-->
            <div class="container">
                <a class="navbar-brand d-flex align-items-center gap-2" href="../index.php">
                <span class="p-2 rounded-3 d-flex align-items-center justify-content-center text-white">
                <i><image src="../images/logo.jpg" style="width:50px; height:50px;"></image></i>
                </span>
                <span class="fs-4 fw-bold text-body">Portfolio<span class="text-primary">Hub</span></span>
                
                </a>
                    
                <!--Hamburger button when window scale down-->
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent" aria-controls="navbarContent" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="d-flex align-items-center gap-3">       <!--Buttons on the right side of navbar-->
                        <!-- Light/Dark Mode Switcher -->
                        <button class="btn btn-link text-body p-2" id="themeToggleBtn" type="button" aria-label="Đổi chủ đề" title="Đổi giao diện Sáng/Tối">
                            <i class="bi bi-sun-fill fs-5" id="themeIcon"></i>
                        </button>
                        <!-- Auth Actions -->
                        <?php if (isset($_SESSION["userId"])):?>
                            <div class="dropdown" id="userDropdownBlock">
                                <a class="d-flex align-items-center gap-2 text-decoration-none text-body dropdown-toggle" href="#" role="button" id="profileDropdown" data-bs-toggle="dropdown" aria-expanded="false" style="outline: none; box-shadow: none;">
                                    <!-- Avatar tròn viết tắt tên -->
                                    <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center fw-bold" style="width: 40px; height: 40px; font-size: 0.95rem;" id="userAvatar">
                                        <?php echo isset($_SESSION["profileName"]) ? substr($_SESSION["profileName"], 0, 2) : 'U'; ?>
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
                            <button class="btn btn-outline-secondary px-4 rounded-pill" type="button" onclick="window.location.href='login.php'">Đăng nhập</button>
                            <button class="btn btn-primary px-4 rounded-pill shadow-sm" style="background: var(--primary-gradient); border: none;" type="button" onclick="window.location.href='register.php'">Bắt đầu ngay</button>
                        <?php endif; ?>
                    </div>
            </div>
        </div>
    </nav>
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
    ?>>


        <main class="container pb-5">
            <div class="profile-cover gradient-bg d-flex align-items-end p-4 text-white shadow-sm">
                <span class="badge bg-white/20 backdrop-blur-md text-white mb-2 py-2 px-3 rounded-pill"><i class="bi bi-patch-check-fill me-1"></i> Hồ sơ đã xác minh</span>
            </div>

            <div class="row g-4 mt-1">
                <div class="col-lg-4">
                    <div class="card card-custom p-4 bg-body sticky-top" style="top: 100px; z-index: 10;">
                        <div class="text-center">
                            <?php if (!empty($user['pfp'])): ?>
                                <img src="../images/<?php echo htmlspecialchars($user['pfp']); ?>" class="rounded-circle avatar-overlap object-fit-cover" style="width: 140px; height: 140px;" alt="Avatar">
                            <?php else: ?>
                                <div class="rounded-circle avatar-overlap bg-primary-subtle text-primary d-inline-flex align-items-center justify-content-center" style="width: 140px; height: 140px; font-weight: 800; font-size: 3rem;">
                                    <?php echo htmlspecialchars($initials); ?>
                                </div>
                            <?php endif; ?>

                            <h3 class="fw-bold mt-3 mb-1"><?php echo htmlspecialchars($user['name']); ?></h3>
                            <p class="text-primary fw-semibold mb-3"><?php echo htmlspecialchars($user['field']); ?></p>
                            
                            <!-- CỤM NÚT LIKE ĐÃ TỐI ƯU HÓA SESSION -->
                            <div class="d-flex justify-content-center align-items-center gap-2 mb-4">

                                <?php if ($currentLoggedInUser === $userId): ?>
                                    <!-- Chủ sở hữu profile: Chỉ hiển thị số lượt thích -->
                                    <button type="button" class="btn btn-primary rounded-pill btn-sm px-3" disabled>
                                        <i class="bi bi-heart-fill me-1"></i> Thích (<?= $totalLikes ?>)
                                    </button>

                                <?php else: ?>
                                    <!-- Cả thành viên và Khách chưa đăng nhập đều dùng chung Form này -->
                                    <form action="../actions/likeProfile.php" method="POST" class="m-0">
                                        <input type="hidden" name="uid" value="<?= $userId ?>">
                                        <button type="submit" class="btn <?= $hasLiked ? 'btn-danger' : 'btn-outline-primary' ?> rounded-pill btn-sm px-3">
                                            <i class="bi <?= $hasLiked ? 'bi-heart-fill' : 'bi-heart' ?> me-1"></i> 
                                            <?= $hasLiked ? 'Bỏ thích' : 'Thích' ?> (<?= $totalLikes ?>)
                                        </button>
                                    </form>
                                <?php endif; ?>
                                <a href="#contactSection" class="btn btn-outline-secondary rounded-pill btn-sm px-3">Liên hệ</a>
                            </div>
                        </div>

                        <hr class="my-4">

                        <div class="mb-4">
                            <h6 class="fw-bold mb-3"><i class="bi bi-info-circle me-2 text-primary"></i>Thông tin cơ bản</h6>
                            <div class="d-flex align-items-center gap-3 mb-2 small"><i class="bi bi-envelope text-muted"></i><span class="text-muted"><?php echo $user['email']?></span></div>
                            <div class="d-flex align-items-center gap-3 mb-2 small"><i class="bi bi-geo-alt text-muted"></i><span class="text-muted"><?php echo $user['location']?></span></div>
                            <div class="d-flex align-items-center gap-3 mb-2 small"><i class="bi bi-globe text-muted"></i><span class="text-primary web"><?php echo $user['website']?></span></div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-8">
                    <div class="card card-custom p-4 bg-body mb-4">
                        <h4 class="fw-bold mb-3"><i class="bi bi-person text-primary me-2"></i>Giới thiệu bản thân</h4>
                        <p class="text-muted leading-relaxed mb-0"><?php echo $user['bio']?></p>
                    </div>

                    <div class="card card-custom p-4 bg-body mb-4">
                        <h4 class="fw-bold mb-4"><i class="bi bi-code-slash text-primary me-2"></i>Dự án nổi bật</h4>
                        <div class="row g-3">
                            <?php if (!empty($projects)): ?>
                                <?php foreach ($projects as $project): ?>
                                    <div class="col-md-6">
                                        <div class="border rounded-4 p-3 h-100 bg-body-tertiary">
                                            <h6 class="fw-bold mb-2">💡 <?php echo htmlspecialchars($project['project_name']); ?></h6>
                                            <p class="text-muted small mb-3"><?php echo $project['description']; ?></p>
                                            <div class="d-flex gap-1 flex-wrap">
                                                <?php 
                                                $techBadges = explode(',', $project['tech']);
                                                foreach ($techBadges as $badge):
                                                    if(trim($badge) !== ''):
                                                ?>
                                                    <span class="badge bg-light text-dark border"><?php echo htmlspecialchars(trim($badge)); ?></span>
                                                <?php 
                                                    endif;
                                                endforeach; 
                                                ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class="col-12">
                                    <p class="text-muted small mb-0">Thành viên này chưa cập nhật thông tin dự án lên hệ thống.</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    <footer class="py-4 border-top bg-body mt-5">
        <div class="container text-center text-muted small">
            <p class="mb-0">© 2026 PortfoliHub · Portfolio của <?php echo htmlspecialchars($user['name']); ?>.</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
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
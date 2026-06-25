
<?php

require_once '../config/dbContext.php'; 

// 1. Capture the unique user id sent from the URL query parameter (?id=X)
$userId = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($userId <= 0) {
    die("Yêu cầu không hợp lệ. Không tìm thấy ID người dùng.");
}

try {
    // 2. Fetch basic profile info matching this specific ID
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
        LEFT JOIN userdetails ON userdetails.id = profiles.id
        WHERE userdetails.id = ?
    ";

    $stmt = $conn->prepare($userQuery);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if (!$user) {
        die("Người dùng này không tồn tại trong hệ thống.");
    }

    // 3. Fetch all projects tied to this user's unique id (projects.uid)
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
                        
                        <div class="d-flex justify-content-center gap-2 mb-4">
                            <button class="btn btn-primary rounded-pill btn-sm px-3">
                                <i class="bi bi-heart-fill me-1"></i> Thích (340)
                            </button>
                            <a href="#contactSection" class="btn btn-outline-secondary rounded-pill btn-sm px-3">Liên hệ</a>
                        </div>
                    </div>

                    <hr class="my-4">

                    <div class="mb-4">
                        <h6 class="fw-bold mb-3"><i class="bi bi-info-circle me-2 text-primary"></i>Thông tin cơ bản</h6>
                        <div class="d-flex align-items-center gap-3 mb-2 small"><i class="bi bi-envelope text-muted"></i><span class="text-muted"><?php echo $user['email']?></span></div>
                        <div class="d-flex align-items-center gap-3 mb-2 small"><i class="bi bi-geo-alt text-muted"></i><span class="text-muted"><?php echo $user['location']?></span></div>
                        <div class="d-flex align-items-center gap-3 mb-2 small"><i class="bi bi-globe text-muted"></i><span class="text-primary web""><?php echo $user['website']?></span></div>
                    </div>
                </div>
            </div>

            <div class="col-lg-8">
                <div class="card card-custom p-4 bg-body mb-4">
                    <h4 class="fw-bold mb-3"><i class="bi bi-person text-primary me-2"></i>Giới thiệu bản thân</h4>
                    <p class="text-muted leading-relaxed mb-0"><?php echo $user['bio']?></p>
                </div>

                <div class="card card-custom p-4 bg-body mb-4">                 <!--NOT YET FIX THIS-->
                    <h4 class="fw-bold mb-4"><i class="bi bi-code-slash text-primary me-2"></i>Dự án nổi bật</h4>
                    <div class="row g-3">
                        <?php if (!empty($projects)): ?>
                            <?php foreach ($projects as $project): ?>
                                <div class="col-md-6">
                                    <div class="border rounded-4 p-3 h-100 bg-body-tertiary">
                                        <h6 class="fw-bold mb-2">💡 <?php echo htmlspecialchars($project['project_name']); ?></h6>
                                        <p class="text-muted small mb-3">Dự án xây dựng và phát triển giải pháp tối ưu hóa dữ liệu.</p>
                                        <div class="d-flex gap-1 flex-wrap">
                                            <?php 
                                            // Splitting technolgies listed with commas (e.g. "PHP, MySQL" -> individual badges)
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
</body>
</html>

<?php
// Adjust this path if your config folder is up one directory level
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
            userdetails.name, 
            userdetails.pfp AS image_url, 
            IFNULL(profiles.field, 'Chưa cập nhật') AS category
        FROM userdetails
        LEFT JOIN profiles ON userdetails.id = profiles.id
        WHERE userdetails.id = :id
    ";
    $stmt = $pdo->prepare($userQuery);
    $stmt->execute(['id' => $userId]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        die("Người dùng này không tồn tại trong hệ thống.");
    }

    // 3. Fetch all projects tied to this user's unique id (projects.uid)
    $projectQuery = "
        SELECT title, tech 
        FROM projects 
        WHERE uid = :id
    ";
    $stmtProject = $pdo->prepare($projectQuery);
    $stmtProject->execute(['id' => $userId]);
    $projects = $stmtProject->fetchAll(PDO::FETCH_ASSOC);

    // Dynamic clean Initials generator for the profile circle icon (e.g., "Trần Anh Quân" -> "AQ")
    $nameParts = explode(" ", $user['name']);
    $initials = (count($nameParts) >= 2) 
        ? mb_substr($nameParts[count($nameParts)-2], 0, 1) . mb_substr($nameParts[count($nameParts)-1], 0, 1)
        : mb_substr($user['name'], 0, 2);
    $initials = mb_strtoupper($initials);

} catch(PDOException $e) {
    die("Lỗi kết nối cơ sở dữ liệu: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chi tiết Portfolio - Trần Anh Quân</title>
    
    <!-- Bootstrap 5.3.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <!-- Google Fonts - Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Inter', sans-serif;
            transition: background-color 0.3s, color 0.3s;
        }
        .gradient-bg {
            background: linear-gradient(135deg, #4f46e5 0%, #ec4899 100%);
        }
        .gradient-text {
            background: linear-gradient(135deg, #4f46e5 0%, #ec4899 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .profile-cover {
            height: 220px;
            border-radius: 24px;
            margin-top: 24px;
        }
        .avatar-overlap {
            margin-top: -80px;
            border: 6px solid var(--bs-body-bg);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }
        .timeline-item {
            position: relative;
            padding-left: 30px;
            border-left: 2px solid var(--bs-border-color);
            margin-bottom: 30px;
        }
        .timeline-item::before {
            content: '';
            position: absolute;
            left: -7px;
            top: 5px;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background-color: #4f46e5;
        }
        .card-custom {
            border-radius: 20px;
            border: 1px solid var(--bs-border-color);
            box-shadow: 0 4px 12px rgba(0,0,0,0.02);
        }
    </style>
</head>
<body class="bg-body-tertiary">

    <!-- Main Container -->
    <main class="container pb-5">
        
        <!-- Banner bìa phong cách chuyên nghiệp -->
        <div class="profile-cover gradient-bg d-flex align-items-end p-4 text-white shadow-sm">
            <span class="badge bg-white/20 backdrop-blur-md text-white mb-2 py-2 px-3 rounded-pill"><i class="bi bi-patch-check-fill me-1"></i> Hồ sơ đã xác minh</span>
        </div>

        <!-- Bố cục chính -->
        <div class="row g-4 mt-1">
            
            <!-- CỘT TRÁI: Thông tin cá nhân & Kỹ năng -->
            <div class="col-lg-4">
                <div class="card card-custom p-4 bg-body sticky-top" style="top: 100px; z-index: 10;">
                    
                    <!-- Avatar đè lên cover -->
                    <div class="text-center">
                        <div class="rounded-circle avatar-overlap bg-primary-subtle text-primary d-inline-flex align-items-center justify-content-center" style="width: 140px; height: 140px; font-weight: 800; font-size: 3rem;">
                            AQ
                        </div>
                        <h3 class="fw-bold mt-3 mb-1">Trần Anh Quân</h3>
                        <p class="text-primary fw-semibold mb-3">Fullstack Developer</p>
                        
                        <!-- Nút tương tác nhanh -->
                        <div class="d-flex justify-content-center gap-2 mb-4">
                            <button class="btn btn-primary rounded-pill btn-sm px-3" onclick="likeProfile()">
                                <i class="bi bi-heart-fill me-1"></i> Thích (<span id="likeCount">340</span>)
                            </button>
                            <a href="#contactSection" class="btn btn-outline-secondary rounded-pill btn-sm px-3">Liên hệ</a>
                        </div>
                    </div>

                    <hr class="my-4">

                    <!-- Thông tin liên hệ nhanh -->
                    <div class="mb-4">
                        <h6 class="fw-bold mb-3"><i class="bi bi-info-circle me-2 text-primary"></i>Thông tin cơ bản</h6>
                        <div class="d-flex align-items-center gap-3 mb-2 small">
                            <i class="bi bi-envelope text-muted"></i>
                            <span class="text-muted">quan.tran36@gmail.com</span>
                        </div>
                        <div class="d-flex align-items-center gap-3 mb-2 small">
                            <i class="bi bi-geo-alt text-muted"></i>
                            <span class="text-muted">Quận 1, TP. Hồ Chí Minh</span>
                        </div>
                        <div class="d-flex align-items-center gap-3 small">
                            <i class="bi bi-globe text-muted"></i>
                            <a href="#" class="text-decoration-none text-primary">anhquan.dev</a>
                        </div>
                    </div>

                    <!-- Kỹ năng chuyên môn -->
                    <div>
                        <h6 class="fw-bold mb-3"><i class="bi bi-lightning-charge me-2 text-primary"></i>Kỹ năng & Công cụ</h6>
                        
                        <!-- ReactJS -->
                        <div class="mb-2">
                            <div class="d-flex justify-content-between small mb-1">
                                <span class="fw-medium">ReactJS & Next.js</span>
                                <span class="text-muted">90%</span>
                            </div>
                            <div class="progress" style="height: 6px;">
                                <div class="progress-bar bg-primary" role="progressbar" style="width: 90%"></div>
                            </div>
                        </div>

                        <!-- NodeJS -->
                        <div class="mb-2">
                            <div class="d-flex justify-content-between small mb-1">
                                <span class="fw-medium">NodeJS & Express</span>
                                <span class="text-muted">85%</span>
                            </div>
                            <div class="progress" style="height: 6px;">
                                <div class="progress-bar bg-primary" role="progressbar" style="width: 85%"></div>
                            </div>
                        </div>

                        <!-- Database -->
                        <div class="mb-2">
                            <div class="d-flex justify-content-between small mb-1">
                                <span class="fw-medium">MongoDB & PostgreSQL</span>
                                <span class="text-muted">80%</span>
                            </div>
                            <div class="progress" style="height: 6px;">
                                <div class="progress-bar bg-primary" role="progressbar" style="width: 80%"></div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <!-- CỘT PHẢI: Giới thiệu, Kinh nghiệm, Dự án nổi bật -->
            <div class="col-lg-8">
                
                <!-- Phần Giới thiệu chi tiết -->
                <div class="card card-custom p-4 bg-body mb-4">
                    <h4 class="fw-bold mb-3"><i class="bi bi-person text-primary me-2"></i>Giới thiệu bản thân</h4>
                    <p class="text-muted leading-relaxed">
                        Tôi là một lập trình viên Fullstack với niềm đam mê mãnh liệt trong việc tạo dựng các sản phẩm số chất lượng cao, tối ưu hóa trải nghiệm người dùng và đặc biệt thích viết những đoạn code gọn gàng, dễ bảo trì. 
                    </p>
                    <p class="text-muted leading-relaxed mb-0">
                        Với hơn 3 năm chinh chiến qua các dự án từ Startup cho đến doanh nghiệp lớn, tôi đã rèn luyện cho mình tư duy giải quyết vấn đề sắc bén và khả năng thích nghi nhanh với các công nghệ mới nhất.
                    </p>
                </div>

                <!-- Phần Kinh nghiệm làm việc -->
                <div class="card card-custom p-4 bg-body mb-4">
                    <h4 class="fw-bold mb-4"><i class="bi bi-briefcase text-primary me-2"></i>Kinh nghiệm làm việc</h4>
                    
                    <!-- Item 1 -->
                    <div class="timeline-item">
                        <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-1">
                            <h5 class="fw-bold mb-0">Senior Fullstack Developer</h5>
                            <span class="badge bg-primary-subtle text-primary">06/2024 - Hiện tại</span>
                        </div>
                        <div class="text-primary fw-medium small mb-2">Công ty Công nghệ AlphaTech</div>
                        <p class="text-muted small mb-0">Chịu trách nhiệm kiến trúc lại hệ thống lõi giúp tăng tốc độ tải trang lên 45%. Hướng dẫn chuyên môn và phân chia công việc cho 4 lập trình viên junior trong nhóm.</p>
                    </div>

                    <!-- Item 2 -->
                    <div class="timeline-item mb-0">
                        <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-1">
                            <h5 class="fw-bold mb-0">Frontend Developer</h5>
                            <span class="badge bg-secondary-subtle text-secondary">01/2022 - 05/2024</span>
                        </div>
                        <div class="text-primary fw-medium small mb-2">Giải pháp Số BetaWeb</div>
                        <p class="text-muted small mb-0">Phát triển và bảo trì hơn 15 giao diện landing page, web app thương mại điện tử tương thích mượt mà trên mọi thiết bị di động. Phối hợp chặt chẽ với team UI/UX.</p>
                    </div>
                </div>

                <!-- Phần Dự án cá nhân -->
                <div class="card card-custom p-4 bg-body mb-4">
                    <h4 class="fw-bold mb-4"><i class="bi bi-code-slash text-primary me-2"></i>Dự án nổi bật</h4>
                    <div class="row g-3">
                        <!-- Dự án 1 -->
                        <div class="col-md-6">
                            <div class="border rounded-4 p-3 h-100 bg-body-tertiary">
                                <h6 class="fw-bold mb-2">💡 Bug-Free-World</h6>
                                <p class="text-muted small mb-3">Một ước mơ viển vông của mọi lập trình viên... Đây là công cụ phân tích code tự động phát hiện lỗi tiềm ẩn bằng AI thời gian thực.</p>
                                <div class="d-flex gap-1">
                                    <span class="badge bg-light text-dark border">NodeJS</span>
                                    <span class="badge bg-light text-dark border">OpenAI</span>
                                </div>
                            </div>
                        </div>
                        <!-- Dự án 2 -->
                        <div class="col-md-6">
                            <div class="border rounded-4 p-3 h-100 bg-body-tertiary">
                                <h6 class="fw-bold mb-2">🛒 Mini-Ecom</h6>
                                <p class="text-muted small mb-3">Hệ thống website bán hàng siêu nhẹ với tốc độ phản hồi dưới 0.5 giây. Tích hợp thanh toán QR Code tự động cực kỳ tiện lợi.</p>
                                <div class="d-flex gap-1">
                                    <span class="badge bg-light text-dark border">React</span>
                                    <span class="badge bg-light text-dark border">MongoDB</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Form liên hệ -->
                <div class="card card-custom p-4 bg-body" id="contactSection">
                    <h4 class="fw-bold mb-3"><i class="bi bi-chat-dots text-primary me-2"></i>Gửi tin nhắn trực tiếp</h4>
                    <p class="text-muted small mb-4">Nếu bạn muốn hợp tác dự án hoặc tuyển dụng tôi, hãy để lại lời nhắn dưới đây nhé!</p>
                    
                    <form id="contactForm" onsubmit="sendMessage(event)">
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold">Họ tên của bạn</label>
                                <input type="text" class="form-control form-control-sm" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold">Email liên hệ</label>
                                <input type="email" class="form-control form-control-sm" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-semibold">Nội dung tin nhắn</label>
                            <textarea class="form-control form-control-sm" rows="4" required placeholder="Xin chào Quân, tôi muốn trao đổi về công việc..."></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary rounded-pill btn-sm px-4">Gửi tin nhắn <i class="bi bi-send ms-1"></i></button>
                    </form>
                    <div class="alert alert-success d-none mt-3 rounded-3" id="successAlert" role="alert">
                        <i class="bi bi-check-circle-fill me-2"></i> Tin nhắn của bạn đã được gửi đi thành công! Xin cảm ơn!
                    </div>
                </div>

            </div>

        </div>

    </main>

    <!-- Footer -->
    <footer class="py-4 border-top bg-body mt-5">
        <div class="container text-center text-muted small">
            <p class="mb-0">© 2026 PortfoliHub · Portfolio của Trần Anh Quân.</p>
        </div>
    </footer>

    <!-- Bootstrap & Custom JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
   
</body>
</html>
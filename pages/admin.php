<?php
    require_once '../config/dbContext.php';
    session_start();
?>
<!DOCTYPE html>
<html lang="vi" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản Trị Hệ Thống - PortfoliHub Admin</title>
    
    <!-- Bootstrap 5.3.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <!-- Google Fonts - Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Inter', sans-serif;
            transition: background-color 0.3s, color 0.3s;
        }
        .stat-card {
            border-radius: 16px;
            border: 1px solid var(--bs-border-color);
            transition: transform 0.2s;
        }
        .stat-card:hover {
            transform: translateY(-3px);
        }
        .avatar-circle {
            width: 42px;
            height: 42px;
            font-weight: 700;
            font-size: 0.95rem;
        }
        .admin-badge {
            font-size: 0.7rem;
            letter-spacing: 0.5px;
        }
        .table-custom {
            border-radius: 16px;
            overflow: hidden;
            border: 1px solid var(--bs-border-color);
        }
    </style>
</head>
<body class="bg-body-tertiary">

    <!-- Header / Navbar -->
    <nav class="navbar navbar-expand-lg fixed-top bg-body border-bottom py-3" style="backdrop-filter: blur(8px); background: rgba(var(--bs-body-bg-rgb), 0.85) !important; z-index: 1030;">
        <div class="container-fluid px-4">
            <a class="navbar-brand fw-bold d-flex align-items-center gap-2" href="index.php">
                <span class="badge bg-danger p-2"><i class="bi bi-shield-lock-fill"></i></span>
                <span>Portfoli<span class="text-primary">Hub</span> <span class="badge bg-danger-subtle text-danger admin-badge text-uppercase ms-1">Admin</span></span>
            </a>
            
            <div class="d-flex align-items-center gap-2">
                <a href="../index.php" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                    <i class="bi bi-box-arrow-right me-1"></i> Về trang chủ
                </a>
            </div>
        </div>
    </nav>

    <!-- Main Admin Container -->
    <main class="container-fluid px-4 py-4" style="margin-top: 75px;">
        
        <!-- Page Title -->
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
            <div>
                <h3 class="fw-bold mb-1"><i class="bi bi-speedometer2 text-primary me-2"></i>Bảng Quản Trị Hệ Thống</h3>
            </div>
            <button class="btn btn-primary btn-sm rounded-pill px-3" onclick="refreshData()">
                <i class="bi bi-arrow-clockwise me-1"></i> Làm mới dữ liệu
            </button>
        </div>

        <!-- 1. Stats Bar (Thống kê số lượng người dùng) -->
        <?php
            // Lấy tổng số người dùng từ cơ sở dữ liệu
            $sql = "SELECT COUNT(*) AS total_users FROM users";
            $result = $conn->query($sql);
            $row = $result->fetch_assoc();
            $totalUsers = $row["total_users"];

            // Lấy tổng số portfolio từ cơ sở dữ liệu
            $sql = "SELECT COUNT(*) AS total_portfolios FROM profiles";
            $result = $conn->query($sql);
            $row = $result->fetch_assoc();
            $totalPortfolios = $row["total_portfolios"];
        ?>
        <div class="row g-3 mb-4">
            <div class="col-6 col-md-3">
                <div class="card stat-card p-3 bg-body shadow-sm">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-muted small d-block mb-1">Tổng người dùng</span>
                            <h3 class="fw-bold mb-0" id="statTotalUsers"><?php echo $totalUsers;?></h3>
                        </div>
                        <div class="rounded-circle bg-primary-subtle text-primary p-3 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                            <i class="bi bi-people-fill fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>


            <div class="col-6 col-md-3">
                <div class="card stat-card p-3 bg-body shadow-sm">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-muted small d-block mb-1">Tổng Portfolio</span>
                            <h3 class="fw-bold mb-0 text-info" id="statTotalPortfolios"><?php echo $totalPortfolios; ?></h3>
                        </div>
                        <div class="rounded-circle bg-info-subtle text-info p-3 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                            <i class="bi bi-journal-album fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 2. Search & Filter Bar (Tìm kiếm & Lọc người dùng) -->
        <div class="card border-0 shadow-sm rounded-4 p-3 mb-4 bg-body">
            <div class="row g-3 align-items-center">
                <div class="col-md-6 col-lg-5">
                    <div class="input-group">
                        <span class="input-group-text bg-body-tertiary border-end-0"><i class="bi bi-search text-muted"></i></span>
                        <input type="text" id="adminSearchInput" class="form-control border-start-0" placeholder="Tìm kiếm theo tên người dùng, email...">
                    </div>
                </div>

            </div>
        </div>

        <!-- 3. User List Table (Bảng danh sách người dùng & Thao tác) -->
        <div class="table-custom bg-body shadow-sm">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light small text-uppercase">
                        <tr>
                            <th class="ps-4" style="width: 100px;">UID</th>
                            <th>Người dùng</th>
                            <th>Trạng thái</th>
                            <th class="text-end pe-4" style="width: 220px;">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody id="userTableBody">
                        
                    </tbody>
                </table>
            </div>
        </div>

    </main>

    <!-- Modal Xác Nhận Xóa Hồ Sơ -->
    <div class="modal fade" id="deleteConfirmModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content rounded-4 border-0 shadow">
                <div class="modal-body text-center py-4">
                    <div class="rounded-circle bg-danger-subtle text-danger d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                        <i class="bi bi-exclamation-triangle-fill fs-3"></i>
                    </div>
                    <h5 class="fw-bold mb-2">Xóa Hồ Sơ Này?</h5>
                    <p class="text-muted small mb-4" id="deleteTargetName">Hành động này sẽ xóa hoàn toàn hồ sơ người dùng khỏi hệ thống và không thể khôi phục.</p>
                    <div class="d-flex gap-2 justify-content-center">
                        <button type="button" class="btn btn-light rounded-pill px-3 btn-sm" data-bs-dismiss="modal">Hủy bỏ</button>
                        <button type="button" class="btn btn-danger rounded-pill px-3 btn-sm" id="confirmDeleteBtn">Xác nhận xóa</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Toast Thông Báo -->
    <div class="position-fixed bottom-0 start-0 p-3" style="z-index: 1060">
        <div id="liveToast" class="toast align-items-center text-white bg-dark border-0 shadow" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body" id="toastMessage">Thông báo Admin!</div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // --- Mock Data Danh Sách Người Dùng ---
        let usersData = [];

        let deleteTargetId = null;

        // --- Render Danh Sách Người Dùng ---
        function renderUserTable(data) {
            const tbody = document.getElementById("userTableBody");
            tbody.innerHTML = "";


            if (data.length === 0) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="4" class="text-center py-5 text-muted">
                            <i class="bi bi-person-x fs-1 d-block mb-2 text-secondary"></i>
                            Không tìm thấy người dùng nào phù hợp với từ khóa.
                        </td>
                    </tr>
                `;
                return;
            }

            data.forEach((user) => {
                const isLocked = user.status == 0;
                const statusBadge = isLocked 
                    ? `<span class="badge bg-danger-subtle text-danger"><i class="bi bi-lock-fill me-1"></i>Bị khóa</span>`
                    : `<span class="badge bg-success-subtle text-success"><i class="bi bi-check-circle-fill me-1"></i>Hoạt động</span>`;

                const lockBtnText = isLocked ? "Mở khóa" : "Khóa";
                const lockBtnIcon = isLocked ? "bi-unlock-fill" : "bi-lock-fill";
                const lockBtnClass = isLocked ? "btn-outline-success" : "btn-outline-warning";

                tbody.innerHTML += `
                    <tr>
                        <td class="ps-4 fw-bold text-muted">${user.id}</td>
                        <td>
                            <div>
                                <h6 class="fw-bold mb-0 text-body">${user.name}</h6>
                                <span class="text-muted small">${user.email}</span>
                            </div>
                        </td>
                        <td>${statusBadge}</td>
                        <td class="text-end pe-4">
                            <div class="d-flex gap-1 justify-content-end">
                                <a href="detail.php?id=${user.id}" class="btn btn-outline-primary btn-sm rounded-pill px-2 py-1" title="Xem hồ sơ cá nhân">
                                    <i class="bi bi-eye-fill me-1"></i> Xem
                                </a>
                                <button type="button" class="btn ${lockBtnClass} btn-sm rounded-pill px-2 py-1" onclick="toggleUserLock(${user.id})" title="${lockBtnText} tài khoản">
                                    <i class="bi ${lockBtnIcon} me-1"></i> ${lockBtnText}
                                </button>
                                <button type="button" class="btn btn-outline-danger btn-sm rounded-pill px-2 py-1" onclick="openDeleteModal(${user.id})" title="Xóa hồ sơ">
                                    <i class="bi bi-trash-fill"></i> Xóa
                                </button>
                            </div>
                        </td>
                    </tr>
                `;
            });
        }

        // --- Tìm Kiếm & Lọc Trạng Thái ---
        function handleAdminSearch() {
            const query = document.getElementById("adminSearchInput").value.toLowerCase().trim();
            renderUserTable(usersData);
        }

        // --- Đổi Trạng Thái Khóa / Mở Khóa ---
       async function toggleUserLock(userId){
            const response = await fetch("../actions/toggleUserStatus.php",{
                method:"POST",
                headers:{
                    "Content-Type":"application/json"
                },
                body:JSON.stringify({
                    id:userId
                })
            });

            const result = await response.json();

            if(result.success){

                const user = usersData.find(u => u.id == userId);

                user.status = result.status;

                renderUserTable(usersData);

                showToast(result.message,"success");

            }else{

                showToast(result.message,"danger");

            }

        }

        // --- Modal Xóa Hồ Sơ ---
        function openDeleteModal(userId) {
            const user = usersData.find(u => u.id === userId);
            if (!user) return;

            deleteTargetId = userId;
            document.getElementById("deleteTargetName").innerText = `Bạn có chắc chắn muốn xóa vĩnh viễn hồ sơ của "${user.name}" (${user.email})?`;

            const modal = new bootstrap.Modal(document.getElementById('deleteConfirmModal'));
            modal.show();
        }

            document.getElementById("confirmDeleteBtn").addEventListener("click", async () => {

            if(deleteTargetId == null) return;

            const response = await fetch("../actions/deleteProfile.php",{

                method:"POST",

                headers:{
                    "Content-Type":"application/json"
                },

                body:JSON.stringify({
                    id:deleteTargetId
                })

            });

            const result = await response.json();

            if(result.success){

                const modal = bootstrap.Modal.getInstance(document.getElementById("deleteConfirmModal"));
                modal.hide();

                showToast(result.message,"success");

            }else{

                showToast(result.message,"danger");

            }

        });
        // --- Tìm kiếm người dùng khi nhấn Enter ---
        document.getElementById("adminSearchInput").addEventListener("keydown", async function(e){

            if(e.key !== "Enter") return;

            const keyword = this.value.trim();

            let url = "../actions/getUsers.php";

            if(keyword !== ""){
                url = "../actions/adminSearch.php?q=" + encodeURIComponent(keyword);
            }

            const response = await fetch(url);

            usersData = await response.json();

            renderUserTable(usersData);

        });
        // --- Tìm kiếm người dùng khi không nhập từ khóa ---
        document.getElementById("adminSearchInput").addEventListener("input", async function(){

        if(this.value.trim() !== "") return;

        const response = await fetch("../actions/getUsers.php");

        usersData = await response.json();

        renderUserTable(usersData);

        });

        // --- Refresh Data ---
        function refreshData() {
            document.getElementById("adminSearchInput").value = "";
            renderUserTable(usersData);
            showToast("Đã cập nhật lại bảng dữ liệu mới nhất!", "info");
        }

        // --- Toast Helper ---
        function showToast(msg, type = "dark") {
            const toastEl = document.getElementById("liveToast");
            const msgEl = document.getElementById("toastMessage");
            msgEl.innerText = msg;

            let bgClass = "bg-dark";
            if (type === "success") bgClass = "bg-success";
            if (type === "warning") bgClass = "bg-warning text-dark";
            if (type === "danger") bgClass = "bg-danger";
            if (type === "info") bgClass = "bg-primary";

            toastEl.className = `toast align-items-center text-white border-0 ${bgClass}`;
            const t = new bootstrap.Toast(toastEl);
            t.show();
        }

        // --- Initial Load ---
       window.onload = async () => {

        const response = await fetch("../actions/getUsers.php");

        usersData = await response.json();

        renderUserTable(usersData);

     };
    </script>
</body>
</html>
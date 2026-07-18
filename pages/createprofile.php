<!DOCTYPE html>
<html lang="en" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tạo Portfolio của bạn - PortfolioHub</title>
    
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
        .form-section-title {
            font-size: 1rem;
            font-weight: 700;
            color: #4f46e5;
            border-bottom: 2px solid rgba(79, 70, 229, 0.1);
            padding-bottom: 8px;
            margin-top: 24px;
            margin-bottom: 16px;
        }
        .gradient-bg-1 { background: linear-gradient(135deg, #4f46e5 0%, #ec4899 100%); }
        .gradient-bg-2 { background: linear-gradient(135deg, #3b82f6 0%, #06b6d4 100%); }
        .gradient-bg-3 { background: linear-gradient(135deg, #111827 0%, #374151 100%); }
        .gradient-bg-4 { background: linear-gradient(135deg, #f59e0b 0%, #e11d48 100%); }

        .theme-dot {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            cursor: pointer;
            border: 2px solid transparent;
            transition: transform 0.2s;
        }
        .theme-dot:hover {
            transform: scale(1.1);
        }
        .theme-dot.active {
            border-color: var(--bs-body-color);
        }
    </style>
</head>
<body class="bg-body-tertiary">

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg fixed-top bg-body border-bottom py-3" style="backdrop-filter: blur(8px); background: rgba(var(--bs-body-bg-rgb), 0.8) !important; z-index: 1030;">
        <div class="container-fluid px-4">
            <a class="navbar-brand d-flex align-items-center gap-2" href="../index.php">
                <span class="p-2 rounded-3 d-flex align-items-center justify-content-center text-white">
                <i><image src="../images/logo.jpg" style="width:50px; height:50px;"></image></i>
                </span>
                <span class="fs-4 fw-bold text-body">Portfolio<span class="text-primary">Hub</span></span>
                </a>
            <div class="d-flex align-items-center gap-2">
                <!-- Theme Switcher -->
                <button class="btn btn-link text-body p-2" id="themeToggleBtn" type="button" aria-label="Đổi chủ đề" title="Đổi giao diện Sáng/Tối">
                            <i class="bi bi-sun-fill fs-5" id="themeIcon"></i>
                        </button>
                <a href="../index.php" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                    <i class="bi bi-arrow-left me-1"></i> Quay lại
                </a>
            </div>
        </div>
    </nav>

    <!-- Main Workspace Container -->
    <div class="container py-4" style="margin-top: 80px;">
        <div class="row justify-content-center">
            
            <!-- Cột Trung Tâm: Form điền thông tin -->
            <div class="col-lg-8 pb-5">
                <div class="card border-0 shadow-sm p-4 rounded-4 bg-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <h4 class="fw-bold mb-0">Thiết Kế Portfolio Cá Nhân</h4>
                    </div>
                    <p class="text-muted small mb-3">Điền thông tin của bạn vào biểu mẫu bên dưới và nhấn lưu.</p>

                    <form id="portfolioForm">
                        
                        <!-- Section 1: Giao diện & Chủ đề -->
                        <div class="form-section-title"><i class="bi bi-palette me-2"></i>Chủ Đề & Giao Diện</div>
                        <div class="mb-3">
                            <label class="form-label small fw-semibold">Chọn màu chủ đạo ảnh bìa (Cover Banner)</label>
                            <div class="d-flex gap-2" id="themePicker">
                                <div class="theme-dot gradient-bg-1 active" data-theme="gradient-bg-1" title="Tím Indigo"></div>
                                <div class="theme-dot gradient-bg-2" data-theme="gradient-bg-2" title="Xanh Cyan"></div>
                                <div class="theme-dot gradient-bg-3" data-theme="gradient-bg-3" title="Xám Đen Tối Giản"></div>
                                <div class="theme-dot gradient-bg-4" data-theme="gradient-bg-4" title="Cam Đỏ Hoàng Hôn"></div>
                            </div>
                        </div>

                        <!-- Section 2: Thông tin cơ bản -->
                        <div class="form-section-title"><i class="bi bi-person me-2"></i>Thông Tin Cơ Bản</div>
                        <div class="row g-2 mb-3">
                            <div class="col-md-6">
                                <label for="inputName" class="form-label small fw-semibold">Họ và tên</label>
                                <input type="text" class="form-control form-control-sm rounded-3" id="inputName" placeholder="Nguyễn Văn A">
                            </div>
                            <div class="col-md-6">
                                <label for="inputTitle" class="form-label small fw-semibold">Chức danh nghề nghiệp</label>
                                <input type="text" class="form-control form-control-sm rounded-3" id="inputTitle" placeholder="Ví dụ: UI/UX Designer">
                            </div>
                        </div>
                        <div class="row g-2 mb-3">
                            <div class="col-md-6">
                                <label for="inputEmail" class="form-label small fw-semibold">Địa chỉ Email</label>
                                <input type="email" class="form-control form-control-sm rounded-3" id="inputEmail" placeholder="name@domain.com">
                            </div>
                            <div class="col-md-6">
                                <label for="inputLocation" class="form-label small fw-semibold">Địa điểm / Thành phố</label>
                                <input type="text" class="form-control form-control-sm rounded-3" id="inputLocation" placeholder="Địa chỉ">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="inputWebsite" class="form-label small fw-semibold">Website cá nhân / GitHub / Behance</label>
                            <input type="text" class="form-control form-control-sm rounded-3" id="inputWebsite" placeholder="Link trang web cá nhân hoặc hồ sơ GitHub/Behance">
                        </div>

                        <!-- Section 3: Giới thiệu & Trợ lý AI -->
                        <div class="form-section-title d-flex justify-content-between align-items-center">
                            <span><i class="bi bi-chat-quote me-2"></i>Giới Thiệu Bản Thân</span>
                            <button type="button" class="btn btn-link btn-sm p-0 text-decoration-none text-primary fw-bold" onclick="generateBioAI()">
                                <span class="spinner-border spinner-border-sm d-none" id="aiLoading"></span>
                            </button>
                        </div>
                        <div class="mb-3">
                            <textarea class="form-control form-control-sm rounded-3" id="inputBio" rows="4" placeholder="Giới thiệu về bản thân..."></textarea>
                        </div>

                        <!-- Section 4: Kỹ năng chuyên môn -->
                        <div class="form-section-title"><i class="bi bi-lightning me-2"></i>Kỹ Năng Chuyên Môn</div>
                        <div class="input-group mb-2">
                            <input type="text" class="form-control form-control-sm" id="inputSkillName" placeholder="Nhập một kỹ năng (Ví dụ: React, Figma)">
                            <!-- <input type="number" class="form-control form-control-sm" id="inputSkillPercent" placeholder="% Thành thạo" min="1" max="100" style="max-width: 130px;"> -->
                            <button class="btn btn-primary btn-sm" type="button" onclick="addSkill()"><i class="bi bi-plus-lg"></i> Thêm</button>
                        </div>
                        <div class="border rounded-3 p-2 bg-light-subtle d-flex flex-wrap gap-2 mb-3" id="skillsContainer" style="min-height: 40px;">
                            <!-- Các tag kỹ năng động sẽ render ở đây -->
                        </div>

                        <!-- Section 5: Kinh nghiệm làm việc -->
                        <div class="form-section-title"><i class="bi bi-briefcase me-2"></i>Kinh Nghiệm Làm Việc</div>
                        <div class="p-3 border rounded-3 bg-light-subtle mb-3">
                            <div class="mb-2">
                                <label class="form-label small mb-0 fw-semibold">Tên công ty / Tổ chức</label>
                                <input type="text" class="form-control form-control-sm rounded-2" id="expCompany" placeholder="Công ty Công nghệ AlphaTech">
                            </div>
                            <div class="mb-2">
                                <label class="form-label small mb-0 fw-semibold">Vị trí / Chức danh</label>
                                <input type="text" class="form-control form-control-sm rounded-2" id="expRole" placeholder="Senior Fullstack Developer">
                            </div>
                               <div class="mb-2">
                                <label class="form-label small mb-0 fw-semibold">Mô tả công việc</label>
                                <input type="text" class="form-control form-control-sm rounded-2" id="expDescription" placeholder="Mô tả chi tiết về công việc">
                            </div>
                            <div class="row g-2 mb-2">
                                <div class="col-md-6">
                                    <label class="form-label small mb-0 fw-semibold">Thời gian (Bắt đầu - Kết thúc)</label>
                                    <input type="text" class="form-control form-control-sm rounded-2" id="expDuration" placeholder="06/2024 - Hiện tại">
                                </div>
                                <div class="col-md-6 d-flex align-items-end">
                                    <button class="btn btn-outline-primary btn-sm w-100 rounded-pill" type="button" onclick="addExperience()">
                                        <i class="bi bi-plus-circle me-1"></i> Lưu kinh nghiệm này
                                    </button>
                                </div>
                            </div>
                            <div class="mt-2" id="experienceListContainer">
                                <!-- Danh sách kinh nghiệm đã thêm -->
                            </div>
                        </div>

                        <!-- Section 6: Dự án cá nhân -->
                        <div class="form-section-title"><i class="bi bi-code-slash me-2"></i>Dự Án Tiêu Biểu</div>
                        <div class="p-3 border rounded-3 bg-light-subtle mb-4">
                            <div class="mb-2">
                                <label class="form-label small mb-0 fw-semibold">Tên dự án</label>
                                <input type="text" class="form-control form-control-sm rounded-2" id="projTitle" placeholder="💡 Dự án Bug-Free-World">
                            </div>
                            <div class="mb-2">
                                <label class="form-label small mb-0 fw-semibold">Mô tả ngắn dự án</label>
                                <textarea class="form-control form-control-sm rounded-2" id="projDesc" rows="2" placeholder="Mô tả ngắn về dự án..."></textarea>
                            </div>
                            <div class="row g-2">
                                <div class="col-md-7">
                                    <label class="form-label small mb-0 fw-semibold">Công nghệ sử dụng (Cách nhau bằng dấu chấm phẩy)</label>
                                    <input type="text" class="form-control form-control-sm rounded-2" id="projTech" placeholder="NodeJS, OpenAI, API">
                                </div>
                                <div class="col-md-5 d-flex align-items-end">
                                    <button class="btn btn-outline-primary btn-sm w-100 rounded-pill" type="button" onclick="addProject()">
                                        <i class="bi bi-plus-circle me-1"></i> Lưu dự án này
                                    </button>
                                </div>
                            </div>
                            <div class="mt-2" id="projectListContainer">
                                <!-- Danh sách dự án đã thêm -->
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-primary w-100 rounded-pill py-2 fw-semibold shadow-sm" onclick="savePortfolio()">
                                <i class="bi bi-floppy me-1"></i> Lưu trực tuyến
                            </button>
                        </div>

                    </form>
                </div>
            </div>

        </div>
    </div>

    <!-- Notification Toast -->
    <div class="position-fixed bottom-0 start-0 p-3" style="z-index: 1060">
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
        <!-- Global Variables -->
        let skills = [];
        let experiences = [];
        let projects = [];

       

        // --- Theme Picker Handler ---
        const dots = document.querySelectorAll(".theme-dot");
        dots.forEach(dot => {
            dot.addEventListener("click", () => {
                dots.forEach(d => d.classList.remove("active"));
                dot.classList.add("active");
                coverClass = dot.getAttribute("data-theme");
            });
        });

        // --- SKILLS HANDLERS ---
            function renderSkills() {
            const container = document.getElementById("skillsContainer");
            container.innerHTML = "";

            skills.forEach((skill, index) => {
                container.innerHTML += `
                    <span class="badge bg-secondary text-white p-2 rounded-pill d-flex align-items-center gap-1">
                        ${skill}
                        <i class="bi bi-x-circle-fill"
                        onclick="removeSkill(${index})"
                        style="cursor:pointer"></i>
                    </span>
                `;
            });
        }
        function addSkill() {
            const nameInput = document.getElementById("inputSkillName");
            const name = nameInput.value.trim();

            if (!name) {
                showToast("Vui lòng nhập tên kỹ năng!", "warning");
                return;
            }

            skills.push(name);

            nameInput.value = "";

            renderSkills();

            showToast("Đã thêm kỹ năng!", "success");
        }

        function removeSkill(index) {
            skills.splice(index, 1);
            renderSkills();
        }

        // --- EXPERIENCE HANDLERS ---
        function renderExperiences() {

        const container = document.getElementById("experienceListContainer");

        container.innerHTML = "";

        experiences.forEach((exp, index) => {

            container.innerHTML += `
                <div class="border rounded-3 p-2 mb-2 bg-white">
                    <div class="fw-bold">${exp.job_title}</div>
                    <div class="text-primary">${exp.company}</div>
                    <div class="small text-muted">
                        ${exp.start_date} - ${exp.end_date}
                    </div>

                    <div class="small mt-2">
                        ${exp.description}
                    </div>

                    <button
                        class="btn btn-sm btn-outline-danger mt-2"
                        onclick="removeExperience(${index})">
                        Xóa
                    </button>
                </div>
            `;

        });

    }

        function addExperience() {

        const company = document.getElementById("expCompany").value.trim();
        const job_title = document.getElementById("expRole").value.trim();
        const duration = document.getElementById("expDuration").value.trim();
        const description = document.getElementById("expDescription").value.trim();

        if (!company || !job_title || !duration || !description) {
            showToast("Vui lòng điền đầy đủ các thông tin kinh nghiệm làm việc!", "warning");
            return;
        }

        // Tách chuỗi "05/2022 - Hiện tại"
        const parts = duration.split("-");

        const start_date = parts[0] ? parts[0].trim() : "";
        const end_date = parts[1] ? parts[1].trim() : "";

        experiences.push({
            company,
            job_title,
            start_date,
            end_date,
            description
        });

        // Xóa dữ liệu trên form
        document.getElementById("expCompany").value = "";
        document.getElementById("expRole").value = "";
        document.getElementById("expDuration").value = "";
        document.getElementById("expDescription").value = "";

        renderExperiences();
        showToast("Đã ghi nhận kinh nghiệm thành công!", "success");
    }

        function removeExperience(index) {
            experiences.splice(index, 1);
            renderExperiences();
        }

        // --- PROJECTS HANDLERS ---
        function renderProjects() {

            const container = document.getElementById("projectListContainer");

            container.innerHTML = "";

            projects.forEach((project, index) => {

                container.innerHTML += `
                    <div class="border rounded-3 p-3 mb-2 bg-white">

                        <div class="fw-bold">
                            ${project.project_name}
                        </div>

                        <div class="small text-muted mt-2">
                            ${project.description}
                        </div>

                    <button
                        class="btn btn-sm btn-outline-danger mt-2"
                        onclick="removeProject(${index})">
                        Xóa
                    </button>

                </div>
            `;

        });

        }

       function addProject() {

            const project_name = document.getElementById("projTitle").value.trim();
            const description = document.getElementById("projDesc").value.trim();
            const tech = document.getElementById("projTech").value.trim();

            if (!project_name || !description || !tech) {
                showToast("Vui lòng điền đầy đủ thông tin về dự án của bạn!", "warning");
                return;
            }

            projects.push({
                project_name,
                description,
                tech
            });

            // Clear inputs
            document.getElementById("projTitle").value = "";
            document.getElementById("projDesc").value = "";
            document.getElementById("projTech").value = "";

            renderProjects();
            showToast("Đã lưu trữ dự án nổi bật!", "success");
        }

        function removeProject(index) {
            projects.splice(index, 1);
            renderProjects();
        }
        
        // --- Save to LocalStorage ---
       async function savePortfolio() {

            const data = {

                name: document.getElementById("inputName").value,
                title: document.getElementById("inputTitle").value,
                email: document.getElementById("inputEmail").value,
                location: document.getElementById("inputLocation").value,
                website: document.getElementById("inputWebsite").value,
                bio: document.getElementById("inputBio").value,

                skills,
                experiences,
                projects
            };

            const response = await fetch("../actions/savePortfolio.php", {

                method: "POST",

                headers: {
                    "Content-Type": "application/json"
                },

                body: JSON.stringify(data)

            });

            const result = await response.json();

            if(result.success){

                showToast("Đã lưu thành công!", "success");

            }else{

                showToast(result.message, "danger");

            }

        }
            // Generate initials
            const parts = name.trim().split(" ");
            let initials = "AQ";
            if(parts.length >= 2) initials = parts[0].substring(0,1) + parts[parts.length-1].substring(0,1);

            // Generate Skills HTML
           const skillsHtml = skills.map(s => `
                        <div class="mb-2">
                            ${s}
                        </div>
                        `).join('');

            // Generate Experience HTML
            const expHtml = experiences.map(e => `
                <div class="timeline-item">
                    <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-1">
                        <h5 class="fw-bold mb-0">${e.role}</h5>
                        <span class="badge bg-primary-subtle text-primary">${e.duration}</span>
                    </div>
                    <div class="text-primary fw-medium small mb-2">${e.company}</div>
                </div>
            `).join('\n');

            // Generate Projects HTML
            const projHtml = projects.map(p => `
                <div class="col-md-6">
                    <div class="border rounded-4 p-3 h-100 bg-body-tertiary">
                        <h6 class="fw-bold mb-2">${p.title}</h6>
                        <p class="text-muted small mb-3">${p.desc}</p>
                        <div class="d-flex gap-1">
                            ${p.tech.split(",").map(t => `<span class="badge bg-light text-dark border">${t.trim()}</span>`).join('\n')}
                        </div>
                    </div>
                </div>
            `).join('\n');

        

        // --- Toast Alert Helper ---
        function showToast(msg, type = "dark") {
            const toastEl = document.getElementById("liveToast");
            const msgEl = document.getElementById("toastMessage");
            msgEl.innerText = msg;
            toastEl.className = `toast align-items-center text-white border-0 bg-${type}`;
            const t = new bootstrap.Toast(toastEl);
            t.show();
        }

        // --- Initialization ---
       window.onload = async () => {

        const response = await fetch("../actions/getPortfolio.php");
        const parsed = await response.json();

        if (Object.keys(parsed).length > 0) {

            document.getElementById("inputName").value = parsed.name || "";
            document.getElementById("inputTitle").value = parsed.title || "";
            document.getElementById("inputEmail").value = parsed.email || "";
            document.getElementById("inputLocation").value = parsed.location || "";
            document.getElementById("inputWebsite").value = parsed.website || "";
            document.getElementById("inputBio").value = parsed.bio || "";

            skills = parsed.skills || [];
            experiences = parsed.experiences || [];
            projects = parsed.projects || [];

            coverClass = parsed.coverClass || "gradient-bg-1";

            dots.forEach(d => {
                d.classList.toggle("active", d.dataset.theme === coverClass);
            });
        }

        renderSkills();
        renderExperiences();
        renderProjects();
    };


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
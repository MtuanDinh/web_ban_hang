<div id="global-loader">
    <div class="loader-spinner"></div>
</div>

<div class="topbar">
    <div style="display: flex; align-items: center;">
        <button id="sidebar-toggle"><i class="fa-solid fa-bars"></i></button>
        <h2>Dashboard</h2>
    </div>

    <div id="right_top_bar">
        <span><i class="fa-solid fa-user-shield" style="color: #00E5FF; margin-right: 5px;"></i> <?php echo "{$_SESSION['admin_name']}"; ?></span>
        <form action="/web_ban_hang/admin/logout.php" method="post">
            <button type="submit" name="logout"><i class="fa-solid fa-right-from-bracket"></i> Đăng xuất</button>
        </form>
    </div>
</div>

<script>
    /* ==========================================
       1. XỬ LÝ MÀN HÌNH LOADING KHI CHUYỂN TRANG
       ========================================== */
    // Khi trang đã tải xong dữ liệu -> Ẩn Loading đi (Fade out)
    window.addEventListener('load', function() {
        const loader = document.getElementById('global-loader');
        if(loader) {
            loader.classList.add('hidden');
        }
    });

    document.addEventListener("DOMContentLoaded", function() {
        // Khi click vào bất kỳ link nào trong Menu Sidebar -> Hiện Loading lên (Fade in)
        const sidebarLinks = document.querySelectorAll('.sidebar a');
        sidebarLinks.forEach(link => {
            link.addEventListener('click', function(e) {
                // Chỉ hiện loading nếu link có href (Không phải dạng #)
                if(this.getAttribute('href') && this.getAttribute('href') !== '#') {
                    document.getElementById('global-loader').classList.remove('hidden');
                }
            });
        });

        /* ==========================================
           2. XỬ LÝ THU GỌN SIDEBAR
           ========================================== */
        const toggleBtn = document.getElementById("sidebar-toggle");
        const sidebar = document.getElementById("adminSidebar");
        
        toggleBtn.addEventListener("click", function() {
            sidebar.classList.toggle("collapsed");
            
            if(sidebar.classList.contains("collapsed")) {
                localStorage.setItem('sidebarState', 'collapsed');
            } else {
                localStorage.setItem('sidebarState', 'expanded');
            }
        });
    });
</script>
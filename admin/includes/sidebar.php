<!-- Bổ sung thư viện FontAwesome nếu chưa có -->
<script src="https://kit.fontawesome.com/da1a483940.js" crossorigin="anonymous"></script>

<div class="sidebar" id="adminSidebar">
    <script>
        if(localStorage.getItem('sidebarState') === 'collapsed') {
            var sb = document.getElementById('adminSidebar');
            sb.classList.add('collapsed');
            sb.style.transition = 'none'; // Tắt hiệu ứng
            setTimeout(() => { sb.style.transition = ''; }, 100); // Bật lại sau 0.1s
        }
    </script>
    <div id="logo_sector">
        <a href="/web_ban_hang/admin/index.php"><img id="logo" src="/web_ban_hang/admin/assets/image/logo.png" alt="Logo"></a>
        <a href="/web_ban_hang/admin/index.php"><h2>PhoneStore</h2></a>
    </div>
    
    <ul>
        <a href="/web_ban_hang/admin/index.php">
            <li><i class="fa-solid fa-chart-line"></i> <span>Tổng quan</span></li>
        </a>
        <a href="/web_ban_hang/admin/orders/list.php">
            <li><i class="fa-solid fa-cart-shopping"></i> <span>Quản lý đơn hàng</span></li>
        </a>
        <a href="/web_ban_hang/admin/products/list.php">
            <li><i class="fa-solid fa-mobile-screen-button"></i> <span>Quản lý sản phẩm</span></li>
        </a>
        <a href="/web_ban_hang/admin/categories/list.php">
            <li><i class="fa-solid fa-list-ul"></i> <span>Quản lý danh mục</span></li>
        </a>
        <a href="/web_ban_hang/admin/users/list.php">
            <li><i class="fa-solid fa-users"></i> <span>Quản lý khách hàng</span></li>
        </a>
    </ul>
</div>


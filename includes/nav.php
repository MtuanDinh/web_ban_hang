<nav>
    <ul>
        <a href="/web_ban_hang/index.php"><li><img src="assets/image/smartphone-anhlogo.png" alt=""></li></a>

        <li>
            <a href="#"><i class="fa-solid fa-list"></i> Danh mục <i class="fa-solid fa-angle-down"></i></a>
            <ul class="submenu">
                <li><a href="category_phone.php"><i class="fa-solid fa-mobile-screen-button"></i> Điện thoại, Tablet</a></li>
                <li><a href="category_accessory.php"><i class="fa-solid fa-headphones"></i> Phụ kiện</a></li>
            </ul>
        </li>
        <li><a href="#"><i class="fa-solid fa-location-crosshairs"> </i> Vị trí <i class="fa-solid fa-angle-down"></i></a>
            
        </li>
        <form class="search-box" action="search.php" method="GET" style="display: inline-flex; align-items: center;">
            <i class="fa-solid fa-magnifying-glass"></i>
            <input type="text" name="keyword" placeholder="Bạn muốn mua gì hôm nay?">
        </form>
        <li><a href="/web_ban_hang/cart.php">Giỏ hàng <i class="fa-solid fa-cart-shopping"></i></a></li>

        <?php if (isset($_SESSION['user_client'])): ?>
            <li class="user-dropdown" style="background-color: transparent;">
                <a href="#" style="color: white; font-weight: 600; cursor: default;">
                    <i class="fa-regular fa-circle-user"></i> 
                    Chào, <?= htmlspecialchars($_SESSION['user_client']['name']) ?> 
                    <i class="fa-solid fa-caret-down" style="font-size: 12px; margin-left: 5px;"></i>
                </a>
                
                <div class="dropdown-content">
                    <a href="cart.php"><i class="fa-solid fa-cart-shopping"></i> Giỏ hàng của tôi</a>
                    <a href="my_orders.php"><i class="fa-solid fa-clipboard-list"></i> Đơn mua của tôi</a>
                    <a href="logout.php" style="color: #dc3545 !important; border-bottom: none;"><i class="fa-solid fa-arrow-right-from-bracket"></i> Đăng xuất</a>
                </div>
            </li>
        <?php else: ?>
            <li><a href="cart.php">Giỏ hàng <i class="fa-solid fa-cart-shopping"></i></a></li>
            <li><a href="login.php">Đăng nhập <i class="fa-regular fa-circle-user"></i></a></li>
        <?php endif; ?>

        <style>
            .user-dropdown {
                position: relative;
                display: inline-block;
                padding: 0 10px;
            }
            
            /* Khu vực nội dung sẽ xổ xuống */
            .dropdown-content {
                display: none;
                position: absolute;
                top: 100%;
                right: 0;
                background-color: #fff;
                min-width: 200px;
                box-shadow: 0px 8px 20px rgba(0,0,0,0.15);
                z-index: 1000;
                border-radius: 8px;
                overflow: hidden;
                border: 1px solid #eee;
            }

            /* Hiệu ứng: Chỉ hiển thị khi di chuột vào nút cha */
            .user-dropdown:hover .dropdown-content {
                display: block;
                animation: fadeInDown 0.3s ease;
            }

            /* Định dạng các đường link bên trong Dropdown */
            .dropdown-content a {
                color: #333 !important;
                padding: 12px 18px !important;
                text-decoration: none;
                display: block;
                font-weight: 500 !important;
                border-bottom: 1px solid #f9f9f9;
                font-size: 15px;
            }

            .dropdown-content a i {
                margin-right: 8px;
                color: #888;
                width: 20px;
                text-align: center;
            }

            .dropdown-content a:hover {
                background-color: #fffafb;
                color: #d70018 !important;
            }

            .dropdown-content a:hover i {
                color: #d70018;
            }

            @keyframes fadeInDown {
                from { opacity: 0; transform: translateY(-10px); }
                to { opacity: 1; transform: translateY(0); }
            }
        </style>
    </ul>
</nav>

<!-- Khung vị trí -->
<div class="modal-location">
    <div class="modal-location-content">
        <div class="modal-header">
            <div class="search-box">
                <i class="fa-solid fa-magnifying-glass"></i>
                <input type="text" placeholder="Nhập tên tỉnh thành">

            </div>
            <button class="close-btn">Đóng <i class="fa-solid fa-xmark"></i></button>
        </div>
        <div class="modal-body">
            <p class="modal-note">Vui lòng chọn tỉnh, thành phố để biết chính xác giá, khuyến mãi và tồn kho
            </p>
            <ul class="location-list">
                <li>Hồ Chí Minh</li>
                <li class="active">Hà Nội <i class="fa-solid fa-circle-check"></i></li>
            </ul>
        </div>
    </div>
</div>

<?php
// ========================================================
// LẤY DỮ LIỆU ĐỘNG TỪ DATABASE CHO MEGA MENU
// ========================================================

// 1. Lấy danh sách Hãng Điện thoại (Danh mục con của ID = 1) - Lấy tối đa 6 hãng
$sql_nav_phones = "SELECT * FROM categories WHERE parent_id = 1 ORDER BY id ASC LIMIT 6";
$res_nav_phones = mysqli_query($conn, $sql_nav_phones);
$nav_phones = [];
if ($res_nav_phones && mysqli_num_rows($res_nav_phones) > 0) {
    while ($row = mysqli_fetch_assoc($res_nav_phones)) {
        $nav_phones[] = $row;
    }
}

// 2. Lấy danh sách Danh mục Phụ kiện (Giả sử danh mục cha của Phụ kiện có ID = 2)
// LƯU Ý: Nếu trong DB của bạn danh mục cha Phụ kiện là số khác (VD: 3, 4), hãy đổi số 2 ở dưới.
$sql_nav_acc = "SELECT * FROM categories WHERE parent_id = 2 ORDER BY id ASC LIMIT 6";
$res_nav_acc = mysqli_query($conn, $sql_nav_acc);
$nav_acc = [];
if ($res_nav_acc && mysqli_num_rows($res_nav_acc) > 0) {
    while ($row = mysqli_fetch_assoc($res_nav_acc)) {
        $nav_acc[] = $row;
    }
}

// 3. Lấy 3 Sản phẩm mới nhất (Hoặc HOT nhất) để trưng bày
$sql_nav_hot = "SELECT id, name FROM products ORDER BY id DESC LIMIT 3";
$res_nav_hot = mysqli_query($conn, $sql_nav_hot);
$nav_hot = [];
if ($res_nav_hot && mysqli_num_rows($res_nav_hot) > 0) {
    while ($row = mysqli_fetch_assoc($res_nav_hot)) {
        $nav_hot[] = $row;
    }
}

// 4. Đếm số lượng sản phẩm trong Giỏ hàng (Lấy từ Database) để hiển thị lên Navbar
$cart_count = 0;
if (isset($_SESSION['user_client'])) {
    $u_id = (int)$_SESSION['user_client']['id'];
    $sql_count = "SELECT SUM(quantity) as total_items FROM cart WHERE user_id = $u_id";
    $res_count = mysqli_query($conn, $sql_count);
    if ($res_count) {
        $row_count = mysqli_fetch_assoc($res_count);
        $cart_count = $row_count['total_items'] ? $row_count['total_items'] : 0;
    }
}
?>

<!-- THÊM CLASS 'sticky-nav' ĐỂ NAVBAR DÍNH TRÊN ĐỈNH -->
<nav class="sticky-nav">
    <ul>
        <a href="index.php"><li><img src="assets/image/icon.png" alt="Logo" style="width: 25px;"></li></a>

        <li class="category-dropdown">
            <a href="#" style="cursor: default;"><i class="fa-solid fa-list"></i> Danh mục <i class="fa-solid fa-angle-down"></i></a>
            
            <div class="mega-menu">
                <ul class="mega-sidebar">
                    <!-- ... (Giữ nguyên toàn bộ phần mega-item Điện thoại và Phụ kiện cũ của bạn) ... -->
                    <li class="mega-item">
                        <a href="category_phone.php">
                            <span><i class="fa-solid fa-mobile-screen-button"></i> Điện thoại, Tablet</span> 
                            <i class="fa-solid fa-angle-right"></i>
                        </a>
                        <div class="mega-content">
                            <div class="mega-col">
                                <h4>Hãng điện thoại</h4>
                                <?php if (!empty($nav_phones)): foreach ($nav_phones as $brand): ?>
                                    <a href="category_phone.php?brand=<?= $brand['id'] ?>"><?= htmlspecialchars($brand['name']) ?></a>
                                <?php endforeach; else: echo '<a href="#">Đang cập nhật...</a>'; endif; ?>
                            </div>
                            <div class="mega-col">
                                <h4>Mức giá</h4>
                                <a href="category_phone.php?price=duoi-5">Dưới 5 triệu</a>
                                <a href="category_phone.php?price=5-15">Từ 5 - 15 triệu</a>
                                <a href="category_phone.php?price=15-25">Từ 15 - 25 triệu</a>
                                <a href="category_phone.php?price=tren-25">Trên 25 triệu</a>
                            </div>
                            <div class="mega-col">
                                <h4>Sản phẩm Mới ⚡</h4>
                                <?php if (!empty($nav_hot)): foreach ($nav_hot as $hot_prod): ?>
                                    <a href="detail.php?id=<?= $hot_prod['id'] ?>">
                                        <?= htmlspecialchars($hot_prod['name']) ?> <span class="badge-hot">MỚI</span>
                                    </a>
                                <?php endforeach; else: echo '<a href="#">Chưa có sản phẩm</a>'; endif; ?>
                            </div>
                        </div>
                    </li>
                    <li class="mega-item">
                        <a href="category_accessory.php">
                            <span><i class="fa-solid fa-headphones"></i> Phụ kiện</span>
                            <i class="fa-solid fa-angle-right"></i>
                        </a>
                        <div class="mega-content">
                            <div class="mega-col">
                                <h4>Loại phụ kiện</h4>
                                <?php if (!empty($nav_acc)): foreach ($nav_acc as $acc): ?>
                                    <a href="category_accessory.php?type=<?= $acc['id'] ?>"><?= htmlspecialchars($acc['name']) ?></a>
                                <?php endforeach; else: echo '<a href="#">Đang cập nhật...</a>'; endif; ?>
                            </div>
                        </div>
                    </li>
                </ul>
            </div>
        </li>

        <li><a href="#" id="btn-location"><i class="fa-solid fa-location-crosshairs"> </i> <span id="location-text">Vị trí</span> <i class="fa-solid fa-angle-down"></i></a></li>
        
        <form class="search-box" id="main-search-form" action="search.php" method="GET" style="display: inline-flex; align-items: center; width: 35%; position: relative;">
            <i class="fa-solid fa-magnifying-glass"></i>
            <input type="text" name="keyword" id="search-input" placeholder="Bạn muốn mua gì hôm nay?" autocomplete="off">
            
            <div class="search-suggest-box" id="search-suggest-box">
                <div id="recent-searches">
                    <div class="suggest-header">
                        <span><i class="fa-solid fa-clock-rotate-left"></i> Lịch sử tìm kiếm</span>
                        <span id="clear-recent" style="cursor: pointer; color: #d70018; font-weight: bold;">Xóa</span>
                    </div>
                    <ul id="recent-list">
                        </ul>
                </div>
                
                <div id="live-results" style="display: none;">
                    <div class="suggest-header">
                        <span><i class="fa-solid fa-bolt" style="color: #ffb74d;"></i> Sản phẩm gợi ý</span>
                    </div>
                    <ul id="live-list">
                        </ul>
                </div>
            </div>
        </form>

        <style>
            /* CSS GIAO DIỆN HỘP TÌM KIẾM TỰ ĐỘNG */
            .search-suggest-box {
                position: absolute;
                top: 110%; /* Nằm ngay sát dưới thanh input */
                left: 0;
                width: 100%;
                background: #fff;
                border-radius: 8px;
                box-shadow: 0 10px 25px rgba(0,0,0,0.15);
                z-index: 1000;
                border: 1px solid #eee;
                display: none; /* Ẩn đi khi chưa bấm vào */
                overflow: hidden;
            }
            .suggest-header {
                display: flex;
                justify-content: space-between;
                padding: 10px 15px;
                font-size: 13px;
                font-weight: 600;
                color: #888;
                background: #f8f9fa;
                border-bottom: 1px solid #f0f0f0;
            }
            .search-suggest-box ul { list-style: none; padding: 0; margin: 0; }
            .search-suggest-box li { padding: 8px 15px; border-bottom: 1px solid #f9f9f9; cursor: pointer; transition: 0.2s; }
            .search-suggest-box li:last-child { border-bottom: none; }
            .search-suggest-box li:hover { background: #f1f5f9; }
            .search-suggest-box li a { text-decoration: none; color: #333; display: flex; align-items: center; gap: 12px; }
            
            /* Giao diện cho thẻ Sản phẩm gợi ý */
            .live-item-img { width: 45px; height: 45px; object-fit: contain; border-radius: 4px; border: 1px solid #eee; padding: 2px;}
            .live-item-info { flex: 1; }
            .live-item-name { font-weight: 500; font-size: 14px; display: -webkit-box; -webkit-line-clamp: 1; -webkit-box-orient: vertical; overflow: hidden; margin-bottom: 3px; }
            .live-item-price { color: #d70018; font-weight: bold; font-size: 13px; }
            .recent-item { color: #555; font-size: 14px; }
        </style>

        <script src="assets/js/live_search.js"></script>

        <!-- NÚT GIỎ HÀNG RIÊNG BIỆT (LUÔN HIỂN THỊ CÙNG BADGE) -->
        <li>
            <a href="cart.php" class="nav-cart-btn">
                <i class="fa-solid fa-cart-shopping"></i> Giỏ hàng
                <?php if ($cart_count > 0): ?>
                    <span class="cart-badge"><?= $cart_count ?></span>
                <?php endif; ?>
            </a>
        </li>

        <!-- KHU VỰC USER (ĐÃ ĐƯỢC LÀM GỌN) -->
        <?php if (isset($_SESSION['user_client'])): ?>
            <li class="user-dropdown" style="background-color: transparent;">
                <a href="#" style="color: white; font-weight: 600; cursor: default;">
                    <i class="fa-regular fa-circle-user"></i> 
                    Chào, <?= htmlspecialchars(explode(' ', trim($_SESSION['user_client']['name']))[0]) ?> <!-- Chỉ lấy tên gọi cho gọn -->
                    <i class="fa-solid fa-caret-down" style="font-size: 12px; margin-left: 5px;"></i>
                </a>
                
                <div class="dropdown-content">
                    <a href="my_orders.php"><i class="fa-solid fa-clipboard-list"></i> Đơn mua của tôi</a>
                    <a href="logout.php" style="color: #dc3545 !important; border-bottom: none;"><i class="fa-solid fa-arrow-right-from-bracket"></i> Đăng xuất</a>
                </div>
            </li>
        <?php else: ?>
            <li><a href="login.php">Đăng nhập <i class="fa-regular fa-circle-user"></i></a></li>
        <?php endif; ?>

        <style>
            /* STICKY NAVBAR: Dán chặt thanh menu trên đỉnh màn hình */
            .sticky-nav {
                position: sticky;
                top: 0;
                z-index: 9999; /* Đảm bảo luôn nằm trên các đối tượng khác */
                box-shadow: 0 4px 15px rgba(0,0,0,0.15); /* Đổ bóng nhẹ cho đẹp */
            }

            /* CSS CHUẨN CHO BADGE GIỎ HÀNG */
            .nav-cart-btn { position: relative; }
            .cart-badge {
                position: absolute;
                top: -5px;
                right: -10px;
                background-color: #ffc107; /* Màu vàng nổi bật */
                color: #d70018; /* Chữ đỏ */
                font-size: 11px;
                font-weight: 900;
                padding: 2px 6px;
                border-radius: 50%;
                border: 2px solid rgb(223, 60, 71); /* Viền tiệp màu với Navbar */
                box-shadow: 0 2px 5px rgba(0,0,0,0.2);
                animation: popIn 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            }
            @keyframes popIn { 0% { transform: scale(0); } 100% { transform: scale(1); } }

            /* ... (Giữ nguyên toàn bộ CSS của .user-dropdown và .mega-menu cũ của bạn ở dưới đây) ... */
            .user-dropdown { position: relative; display: inline-block; padding: 0 10px; }
            .dropdown-content { display: none; position: absolute; top: 100%; right: 0; background-color: #fff; min-width: 200px; box-shadow: 0px 8px 20px rgba(0,0,0,0.15); z-index: 1000; border-radius: 8px; overflow: hidden; border: 1px solid #eee; }
            .user-dropdown:hover .dropdown-content { display: block; animation: fadeInDown 0.3s ease; }
            .dropdown-content a { color: #333 !important; padding: 12px 18px !important; text-decoration: none; display: block; font-weight: 500 !important; border-bottom: 1px solid #f9f9f9; font-size: 15px; }
            .dropdown-content a i { margin-right: 8px; color: #888; width: 20px; text-align: center; }
            .dropdown-content a:hover { background-color: #fffafb; color: #d70018 !important; }
            .dropdown-content a:hover i { color: #d70018; }
            @keyframes fadeInDown { from { opacity: 0; transform: translateY(-10px); } to { opacity: 1; transform: translateY(0); } }

            .category-dropdown { position: relative; }
            .category-dropdown:hover .mega-menu { display: block; animation: fadeInDown 0.2s ease; }
            .mega-menu { display: none; position: absolute; top: 100%; left: 0; background: #fff; border-radius: 8px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); z-index: 1000; border: 1px solid #eee; width: 250px; }
            .mega-menu::before { content: ""; position: absolute; top: -15px; left: 0; width: 100%; height: 15px; background: transparent; }
            .mega-sidebar { list-style: none; padding: 10px 0; margin: 0; }
            .mega-item { position: static; } 
            .mega-item > a { display: flex; justify-content: space-between; align-items: center; padding: 12px 20px; color: #333; text-decoration: none; font-weight: 500; font-size: 15px; transition: 0.2s; }
            .mega-item > a span i { width: 25px; color: #666; text-align: left;}
            .mega-item:hover > a { background: #f8f9fa; color: #d70018; }
            .mega-item:hover > a span i, .mega-item:hover > a i.fa-angle-right { color: #d70018; }
            .mega-content { display: none; position: absolute; top: 0; left: 250px; background: #fff; width: 700px; min-height: 100%; border-radius: 0 8px 8px 8px; border-left: 1px solid #eee; padding: 25px; box-shadow: 5px 10px 30px rgba(0,0,0,0.05); }
            .mega-item:hover .mega-content { display: flex; gap: 40px; }
            .mega-col { flex: 1; }
            .mega-col h4 { margin-top: 0; margin-bottom: 15px; font-size: 16px; color: #333; font-weight: 700; border-bottom: 1px solid #eee; padding-bottom: 10px; }
            .mega-col a { display: block; color: #555; text-decoration: none; padding: 8px 0; font-size: 14px; transition: 0.2s; }
            .mega-col a:hover { color: #d70018; transform: translateX(5px); font-weight: 600;}
            .badge-hot { background: #d70018; color: #fff; font-size: 10px; padding: 2px 6px; border-radius: 4px; margin-left: 5px; font-weight: bold; }
        </style>
    </ul>
</nav>

<!-- Giữ nguyên phần <div class="modal-location"> bên dưới của bạn -->
<div class="modal-location">
    <div class="modal-location-content">
        <div class="modal-header">
            <div class="search-box" style="width: 100%; max-width: none;">
                <i class="fa-solid fa-magnifying-glass"></i>
                <input type="text" id="search-province" placeholder="Tìm nhanh tỉnh/thành phố...">
            </div>
            <button class="close-btn">Đóng <i class="fa-solid fa-xmark"></i></button>
        </div>
        <div class="modal-body">
            <p class="modal-note">Chọn khu vực để xem giá ưu đãi và tồn kho chính xác nhất:</p>
            <ul class="location-list">
                <li data-discount="0" class="active">Hà Nội <i class="fa-solid fa-circle-check"></i></li>
                <li data-discount="5">Hồ Chí Minh</li>
                <li data-discount="2">Đà Nẵng</li>
                <li data-discount="3">Hải Phòng</li>
                <li data-discount="1">Cần Thơ</li>
                <li data-discount="2">Đồng Nai</li>
                <li data-discount="4">Bình Dương</li>
                <li data-discount="1">Bà Rịa - Vũng Tàu</li>
                <li data-discount="2">Bắc Ninh</li>
                <li data-discount="36">Thanh Hóa</li>
            </ul>
        </div>
    </div>
</div>

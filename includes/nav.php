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
?>

<nav>
    <ul>
        <a href="/web_ban_hang/index.php"><li><img src="assets/image/smartphone-anhlogo.png" alt=""></li></a>

        <li class="category-dropdown">
            <a href="#" style="cursor: default;"><i class="fa-solid fa-list"></i> Danh mục <i class="fa-solid fa-angle-down"></i></a>
            
            <div class="mega-menu">
                <ul class="mega-sidebar">
                    <li class="mega-item">
                        <a href="category_phone.php">
                            <span><i class="fa-solid fa-mobile-screen-button"></i> Điện thoại, Tablet</span> 
                            <i class="fa-solid fa-angle-right"></i>
                        </a>
                        
                        <div class="mega-content">
                            <div class="mega-col">
                                <h4>Hãng điện thoại</h4>
                                <?php if (!empty($nav_phones)): ?>
                                    <?php foreach ($nav_phones as $brand): ?>
                                        <a href="category_phone.php?brand=<?= $brand['id'] ?>"><?= htmlspecialchars($brand['name']) ?></a>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <a href="#">Đang cập nhật...</a>
                                <?php endif; ?>
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
                                <?php if (!empty($nav_hot)): ?>
                                    <?php foreach ($nav_hot as $hot_prod): ?>
                                        <a href="detail.php?id=<?= $hot_prod['id'] ?>">
                                            <?= htmlspecialchars($hot_prod['name']) ?> <span class="badge-hot">MỚI</span>
                                        </a>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <a href="#">Chưa có sản phẩm</a>
                                <?php endif; ?>
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
                                <?php if (!empty($nav_acc)): ?>
                                    <?php foreach ($nav_acc as $acc): ?>
                                        <a href="category_accessory.php?type=<?= $acc['id'] ?>"><?= htmlspecialchars($acc['name']) ?></a>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <a href="#">Đang cập nhật...</a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </li>
                </ul>
            </div>
        </li>

        <li><a href="#" id="btn-location"><i class="fa-solid fa-location-crosshairs"> </i> <span id="location-text">Vị trí</span> <i class="fa-solid fa-angle-down"></i></a></li>
        
        <form class="search-box" action="search.php" method="GET" style="display: inline-flex; align-items: center;">
            <i class="fa-solid fa-magnifying-glass"></i>
            <input type="text" name="keyword" placeholder="Bạn muốn mua gì hôm nay?">
        </form>

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
            /* CSS CHO USER DROPDOWN */
            .user-dropdown { position: relative; display: inline-block; padding: 0 10px; }
            .dropdown-content { display: none; position: absolute; top: 100%; right: 0; background-color: #fff; min-width: 200px; box-shadow: 0px 8px 20px rgba(0,0,0,0.15); z-index: 1000; border-radius: 8px; overflow: hidden; border: 1px solid #eee; }
            .user-dropdown:hover .dropdown-content { display: block; animation: fadeInDown 0.3s ease; }
            .dropdown-content a { color: #333 !important; padding: 12px 18px !important; text-decoration: none; display: block; font-weight: 500 !important; border-bottom: 1px solid #f9f9f9; font-size: 15px; }
            .dropdown-content a i { margin-right: 8px; color: #888; width: 20px; text-align: center; }
            .dropdown-content a:hover { background-color: #fffafb; color: #d70018 !important; }
            .dropdown-content a:hover i { color: #d70018; }
            @keyframes fadeInDown { from { opacity: 0; transform: translateY(-10px); } to { opacity: 1; transform: translateY(0); } }

            /* CSS CHO MEGA MENU CỰC XỊN */
            /* =========================================
               CSS CHO MEGA MENU CỰC XỊN (ĐÃ FIX LỆCH NÚT)
               ========================================= */
               
            /* Trả lại vị trí cân bằng hoàn hảo cho nút đỏ Danh mục */
            .category-dropdown { 
                position: relative; 
            }
            
            .category-dropdown:hover .mega-menu { 
                display: block; 
                animation: fadeInDown 0.2s ease; 
            }
            
            .mega-menu { 
                display: none; position: absolute; top: 100%; left: 0; 
                background: #fff; border-radius: 8px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); 
                z-index: 1000; border: 1px solid #eee; width: 250px; 
            }

            /* TUYỆT CHIÊU: Cầu nối tàng hình giúp rê chuột không bị rớt menu */
            .mega-menu::before {
                content: "";
                position: absolute;
                top: -15px; /* Vươn lên trên 15px để hứng chuột */
                left: 0;
                width: 100%;
                height: 15px;
                background: transparent;
            }

            /* ... (Các đoạn CSS của .mega-sidebar, .mega-item, .mega-content ở dưới bạn giữ nguyên nhé) ... */
            .mega-sidebar { list-style: none; padding: 10px 0; margin: 0; }
            .mega-item { position: static; } 
            .mega-item > a { display: flex; justify-content: space-between; align-items: center; padding: 12px 20px; color: #333; text-decoration: none; font-weight: 500; font-size: 15px; transition: 0.2s; }
            .mega-item > a span i { width: 25px; color: #666; text-align: left;}
            .mega-item:hover > a { background: #f8f9fa; color: #d70018; }
            .mega-item:hover > a span i, .mega-item:hover > a i.fa-angle-right { color: #d70018; }

            /* VÙNG NỘI DUNG SIÊU LỚN (MEGA CONTENT) */
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
                <li data-discount="3">Thanh Hóa</li>
            </ul>
        </div>
    </div>
</div>
<script src="assets/js/script.js"></script>
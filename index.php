<?php
require_once("includes/connect_db.php");

// 1. Lấy danh mục hiển thị bộ lọc (Điện thoại)
$sql_cats = "SELECT id, name FROM categories WHERE parent_id = 1 ORDER BY id ASC LIMIT 5";
$result_cats = mysqli_query($conn, $sql_cats);

// ==========================================
// FIX LỖI "XÂM LẤN": Mặc định luôn giới hạn hiển thị trong nhóm Điện thoại (ID 1)
// ==========================================
$where_clause = "WHERE p.category_id = 1 OR p.category_id IN (SELECT id FROM categories WHERE parent_id = 1)";

if (isset($_GET['category_id']) && is_numeric($_GET['category_id']) && $_GET['category_id'] != 0) {
    $cat_id = (int)$_GET['category_id'];
    $where_clause = "WHERE p.category_id = $cat_id 
                     OR p.category_id IN (SELECT id FROM categories WHERE parent_id = $cat_id)";
}

// 2. Lấy sản phẩm đang bán (Điện thoại)
$sql = "SELECT p.id, p.name, p.image, MIN(pv.price) as min_price 
FROM products p 
LEFT JOIN product_variants pv ON p.id = pv.product_id 
$where_clause
GROUP BY p.id 
ORDER BY p.id DESC
LIMIT 8";
$result = mysqli_query($conn, $sql);

// THÊM MỚI 1: Lấy 4 sản phẩm ngẫu nhiên cho FLASH SALE
$sql_flash = "SELECT p.id, p.name, p.image, MIN(pv.price) as min_price 
              FROM products p LEFT JOIN product_variants pv ON p.id = pv.product_id 
              GROUP BY p.id ORDER BY RAND() LIMIT 4";
$res_flash = mysqli_query($conn, $sql_flash);

// THÊM MỚI 2: Lấy 5 sản phẩm Phụ kiện mới nhất
$sql_acc_prods = "SELECT p.id, p.name, p.image, MIN(pv.price) as min_price 
                  FROM products p LEFT JOIN product_variants pv ON p.id = pv.product_id 
                  WHERE p.category_id = 2 OR p.category_id IN (SELECT id FROM categories WHERE parent_id = 2)
                  GROUP BY p.id ORDER BY p.id DESC LIMIT 5";
$res_acc_prods = mysqli_query($conn, $sql_acc_prods);

// ==========================================
// THÊM MỚI 3: Lấy 6 danh mục Phụ kiện động từ DB để làm Nút bấm
// ==========================================
$sql_acc_cats = "SELECT id, name FROM categories WHERE parent_id = 2 ORDER BY id ASC LIMIT 6";
$res_acc_cats = mysqli_query($conn, $sql_acc_cats);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PhoneStore - Mua bán điện thoại chính hãng</title>
    <script src="https://kit.fontawesome.com/da1a483940.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="assets/css/style_client.css">
    <link rel="stylesheet" href="assets/css/style_flashsale_accessory.css">
</head>

<body>
    <?php include "includes/header.php"; include "includes/nav.php"; ?>

    <main class="main-content">
        <div class="main-banner-wrapper">
            <div class="side-banner banner-left">
                <img class="img-side" src="assets/image/bannerleft.jpg" alt="Banner điện thoại trái">
            </div>
            <div class="center-column">
                <div class="slideshow-container">
                    <div class="slide fade"><img class="img-banner" src="assets/image/iPhone17ProMax_slide.webp"></div>
                    <div class="slide fade"><img class="img-banner" src="assets/image/Oppofin x9 ultra_slide.webp"></div>
                    <div class="slide fade"><img class="img-banner" src="assets/image/samsung-galaxy-slide.webp"></div>
                </div>
                <div class="sub-banners">
                    <div class="sub-banner-item"><img src="assets/image/anhbannersamsungsmall.jpg"></div>
                    <div class="sub-banner-item"><img src="assets/image/iphonebannersmall.jpg"></div>
                    <div class="sub-banner-item"><img src="assets/image/anhbannerxiaomismall.jpg"></div>
                </div>
            </div>
            <div class="side-banner banner-right">
                <img class="img-side" src="assets/image/bannerright.jpg" alt="Banner điện thoại phải">
            </div>
        </div>

        <div class="flash-sale-wrapper">
            <div class="flash-header">
                <h2 class="flash-title"><i class="fa-solid fa-bolt" style="color: #ffcc00; font-size: 30px;"></i> FLASH SALE GIỜ VÀNG</h2>
                <div class="countdown-timer">
                    <span class="cd-text">Kết thúc trong:</span>
                    <span class="cd-box">02</span><span class="cd-dot">:</span>
                    <span class="cd-box">45</span><span class="cd-dot">:</span>
                    <span class="cd-box">18</span>
                </div>
            </div>
            
            <div class="flash-grid">
                <?php if ($res_flash && mysqli_num_rows($res_flash) > 0): 
                    while ($f_row = mysqli_fetch_assoc($res_flash)):
                        $f_price = $f_row['min_price'] ? $f_row['min_price'] : 0;
                        $f_img = !empty($f_row['image']) ? 'assets/uploads/' . $f_row['image'] : 'https://via.placeholder.com/300';
                        $fake_discount = rand(15, 40);
                        $f_old = $f_price / (1 - ($fake_discount/100));
                ?>
                <a href="detail.php?id=<?= $f_row['id'] ?>" class="flash-card" style="position: relative; text-decoration: none;">
                    <div class="flash-badge">GIẢM <?= $fake_discount ?>%</div>
                    <div class="product-image"><img src="<?= htmlspecialchars($f_img) ?>" alt="<?= htmlspecialchars($f_row['name']) ?>"></div>
                    <h3 class="product-name" style="font-size: 13px;"><?= htmlspecialchars($f_row['name']) ?></h3>
                    <div style="color: #d70018; font-weight: 800; font-size: 18px; margin-bottom: 5px;">
                        <?= $f_price != 0 ? number_format($f_price, 0, ',', '.') . "đ" : "Liên hệ" ?>
                    </div>
                    <?php if($f_price != 0): ?>
                        <div style="color: #999; text-decoration: line-through; font-size: 12px;"><?= number_format($f_old, 0, ',', '.') ?>đ</div>
                    <?php endif; ?>
                    
                    <div style="margin-top: 10px; background: #ffeaec; border-radius: 10px; height: 16px; position: relative; overflow: hidden;">
                        <div style="background: linear-gradient(90deg, #ff4d4f, #d70018); width: <?= rand(40, 90) ?>%; height: 100%; border-radius: 10px;"></div>
                        <span style="position: absolute; top: 1px; left: 0; width: 100%; text-align: center; color: #fff; font-size: 10px; font-weight: bold; line-height: 15px;">Đang bán chạy</span>
                    </div>
                </a>
                <?php endwhile; endif; ?>
            </div>
        </div>

        <section class="product-section" style="margin-top: 20px;">
            <div class="section-header">
                <h2>Điện thoại nổi bật</h2>
                <div class="filter-tags" id="product-start">
                    <?php
                    $current_cat_id = isset($_GET['category_id']) ? (int)$_GET['category_id'] : 0;
                    $active_all = ($current_cat_id == 0) ? 'active' : '';
                    echo '<a href="index.php" class="' . $active_all . '">Tất cả</a>';

                    if ($result_cats && mysqli_num_rows($result_cats) > 0) {
                        while ($cat = mysqli_fetch_assoc($result_cats)) {
                            $is_active = ($current_cat_id == $cat['id']) ? 'active' : '';
                            echo '<a href="?category_id=' . $cat['id'] . '#product-start" class="' . $is_active . '">' . htmlspecialchars($cat['name']) . '</a>';
                        }
                    }
                    ?>
                </div>
                <a href="all_products.php" class="view-all">Xem tất cả ></a>
            </div>

            <div class="product-layout">
                <div class="product-promo-banner">
                    <img src="assets/image/honor-promo-banner.webp" alt="Promo Banner" style="margin-bottom: 15px;">
                    <img src="assets/image/iph17-promo-banner.webp" alt="Promo Banner">
                </div>
                <div class="product-grid">
                    <?php
                    if ($result && mysqli_num_rows($result) > 0) {
                        while ($row = mysqli_fetch_assoc($result)) { 
                            include("includes/product_card.php");
                        }
                    } else {
                        echo "<p style='grid-column: 1 / -1; text-align: center; padding: 50px;'>Chưa có sản phẩm nào phù hợp.</p>";
                    }
                    ?>
                </div>
            </div>
        </section>

        <section class="accessory-section">
            <div class="section-header" style="margin-bottom: 10px;">
                <h2>Sắm thêm phụ kiện chất lượng</h2>
                <a href="all_accessories.php" class="view-all">Xem tất cả phụ kiện ></a>
            </div>
            
            <div class="acc-icon-list">
                <?php
                // Mảng chứa các URL icon tương ứng
                $acc_icons = [
                    'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTEtGyKsvgqvIVaX2H7XhJmkwgnnDUkKUMqHA&s', // Tai nghe
                    'https://cdn-icons-png.flaticon.com/512/16954/16954223.png',     // Củ sạc
                    'https://cdn-icons-png.flaticon.com/512/1670/1670683.png', // Pin dự phòng
                    'https://cdn-icons-png.flaticon.com/512/394/394252.png',// Cáp sạc
                    'https://cdn-icons-png.flaticon.com/512/5780/5780358.png', // Ốp lưng
                    'https://static.thenounproject.com/png/5000323-200.png'    // Tản nhiệt
                ];
                
                $i = 0;
                if ($res_acc_cats && mysqli_num_rows($res_acc_cats) > 0) {
                    while ($cat = mysqli_fetch_assoc($res_acc_cats)) {
                        $icon_url = isset($acc_icons[$i]) ? $acc_icons[$i] : $acc_icons[0];
                        // TUYỆT CHIÊU: Bọc vào thẻ <a> và truyền type ID động vào URL
                        echo '<a href="category_accessory.php?type=' . $cat['id'] . '" class="accessory-item" style="text-decoration: none; color: inherit;">';
                        echo '<img src="' . $icon_url . '" alt="' . htmlspecialchars($cat['name']) . '">';
                        echo '<span>' . htmlspecialchars($cat['name']) . '</span>';
                        echo '</a>';
                        $i++;
                    }
                }
                ?>
            </div>

            <div class="accessory-grid-products">
                <?php
                if ($res_acc_prods && mysqli_num_rows($res_acc_prods) > 0) {
                    while ($row = mysqli_fetch_assoc($res_acc_prods)) {
                        include("includes/product_card.php");
                    }
                } else {
                    echo "<p style='grid-column: 1 / -1; text-align: center; color: #999;'>Các sản phẩm phụ kiện đang được cập nhật...</p>";
                }
                ?>
            </div>
        </section>
        <br>
    </main>

    <?php include "includes/footer.php"; ?>
    <script src="assets/js/slideshow.js"></script>
    <script src="assets/js/flashsale.js"></script>
</body>
</html>
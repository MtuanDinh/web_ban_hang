<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require_once("includes/connect_db.php");

// 1. Kiểm tra xem có ID sản phẩm trên URL không
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: index.php");
    exit();
}
$product_id = (int)$_GET['id'];

// ==========================================
// XỬ LÝ ĐĂNG BÌNH LUẬN & ĐÁNH GIÁ SAO
// ==========================================
if (isset($_POST['submit_review']) && isset($_SESSION['user_client'])) {
    $rating = (int)$_POST['rating'];
    $comment = mysqli_real_escape_string($conn, trim($_POST['comment']));
    $u_id = (int)$_SESSION['user_client']['id'];
    
    // Lưu vào Database
    mysqli_query($conn, "INSERT INTO reviews (product_id, user_id, rating, comment) VALUES ($product_id, $u_id, $rating, '$comment')");
    
    // Tải lại trang để tránh lỗi gửi lại form khi bấm F5
    header("Location: detail.php?id=$product_id");
    exit();
}

// 2. Lấy thông tin Sản phẩm gốc
$sql_product = "SELECT * FROM products WHERE id = $product_id";
$res_product = mysqli_query($conn, $sql_product);
if (mysqli_num_rows($res_product) == 0) {
    echo "<h2 style='text-align:center; margin-top:50px;'>Sản phẩm không tồn tại hoặc đã bị xóa!</h2>";
    exit();
}
$product = mysqli_fetch_assoc($res_product);

// ==========================================
// KIỂM TRA SẢN PHẨM LÀ ĐIỆN THOẠI HAY PHỤ KIỆN
// ==========================================
$cat_id = (int)$product['category_id'];
$sql_check_cat = "SELECT parent_id FROM categories WHERE id = $cat_id";
$res_check_cat = mysqli_query($conn, $sql_check_cat);

$is_accessory = false; // Mặc định giả sử là Điện thoại
if ($res_check_cat && mysqli_num_rows($res_check_cat) > 0) {
    $cat_data = mysqli_fetch_assoc($res_check_cat);
    // Nếu danh mục hiện tại là Phụ kiện (ID = 2) hoặc nằm trong nhóm Phụ kiện (parent_id = 2)
    if ($cat_id == 2 || $cat_data['parent_id'] == 2) {
        $is_accessory = true;
    }
}

// 3. Lấy tất cả các Phiên bản (Variants)
$sql_variants = "SELECT * FROM product_variants WHERE product_id = $product_id";
$res_variants = mysqli_query($conn, $sql_variants);
$variants = [];
while ($row = mysqli_fetch_assoc($res_variants)) {
    $variants[] = $row;
}

// 4. Giải mã chuỗi JSON Cấu hình kỹ thuật
$specs = json_decode($product['description'], true);

// 5. Gom toàn bộ hình ảnh (Ảnh bìa + Gallery)
$all_images = [];
if (!empty($product['image'])) $all_images[] = $product['image'];
if (!empty($product['gallery'])) {
    $gallery_arr = json_decode($product['gallery'], true);
    if (is_array($gallery_arr)) {
        $all_images = array_merge($all_images, $gallery_arr);
    }
}

// ==========================================
// TRUY VẤN LẤY DANH SÁCH BÌNH LUẬN & ĐẾM SAO
// ==========================================
$filter_star = isset($_GET['star']) ? (int)$_GET['star'] : 0;

// 1. Truy vấn gom nhóm để đếm số lượng từng loại sao
$sql_stats = "SELECT rating, COUNT(*) as count FROM reviews WHERE product_id = $product_id GROUP BY rating";
$res_stats = mysqli_query($conn, $sql_stats);

// Khởi tạo mảng đếm mặc định (từ 1 đến 5 sao đều bằng 0)
$star_counts = [1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0];
$total_stars = 0;
$total_reviews = 0;

if ($res_stats) {
    while ($row = mysqli_fetch_assoc($res_stats)) {
        $star_counts[$row['rating']] = $row['count'];
        $total_reviews += $row['count'];
        $total_stars += ($row['rating'] * $row['count']);
    }
}

$avg_rating = $total_reviews > 0 ? round($total_stars / $total_reviews, 1) : 0;
$avg_stars_html = $total_reviews > 0 ? str_repeat('⭐', round($avg_rating)) : 'Chưa có đánh giá';

// 2. Truy vấn lấy danh sách bình luận (Có áp dụng điều kiện LỌC)
$sql_reviews = "SELECT r.*, u.username FROM reviews r JOIN users u ON r.user_id = u.id WHERE r.product_id = $product_id";
if ($filter_star > 0 && $filter_star <= 5) {
    $sql_reviews .= " AND r.rating = $filter_star";
}
$sql_reviews .= " ORDER BY r.created_at DESC";

$res_reviews = mysqli_query($conn, $sql_reviews);
$reviews = [];
if ($res_reviews) {
    while($row = mysqli_fetch_assoc($res_reviews)) {
        $reviews[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($product['name']) ?> - PhoneStore</title>
    <script src="https://kit.fontawesome.com/da1a483940.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="assets/css/style_client.css">
    <link rel="stylesheet" href="assets/css/style_detail.css">
</head>
<body>
    <?php include "includes/header.php"; include "includes/nav.php"; ?>

    <main class="main-content">
        <div class="detail-container">
            <div class="detail-gallery">
                <?php $first_img = !empty($all_images) ? 'assets/uploads/' . $all_images[0] : 'https://via.placeholder.com/400x400?text=No+Image'; ?>
                <div class="main-image-box">
                    <img id="main-img" src="<?= htmlspecialchars($first_img) ?>" alt="<?= htmlspecialchars($product['name']) ?>">
                </div>

                <?php if (count($all_images) > 1): ?>
                <div class="thumbnail-bar">
                    <?php foreach ($all_images as $index => $img_name): 
                        $thumb_src = 'assets/uploads/' . htmlspecialchars($img_name);
                        $active_class = ($index == 0) ? 'active' : ''; 
                    ?>
                        <div class="thumb-item <?= $active_class ?>" onclick="changeMainImage('<?= $thumb_src ?>', this)">
                            <img src="<?= $thumb_src ?>" alt="thumbnail">
                        </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>

            <div class="detail-info">
                <div class="product-title-area">
                    <h1><?= htmlspecialchars($product['name']) ?></h1>
                    <div class="top-rating">
                        <span><?= $avg_stars_html ?></span> 
                        <span>(<?= $avg_rating ?>/5)</span>
                        <a href="#reviews-section">| Xem <?= $total_reviews ?> đánh giá</a>
                    </div>
                </div>
                
                <div class="price-box" style="margin-bottom: 20px;">
                    <span class="badge-discount" id="detail_badge" style="display: none; background: #d70018; color: white; padding: 4px 10px; border-radius: 5px; font-size: 13px; font-weight: bold; margin-bottom: 10px; display: inline-block;">Giảm 0%</span>
                    <br>
                    <span class="price-current" id="display_price" style="color: #d70018; font-weight: bold; font-size: 26px;">Đang cập nhật giá...</span>
                    <span class="price-old" id="display_old_price" style="color: #707070; text-decoration: line-through; font-size: 16px; margin-left: 10px; display: none;"></span>
                </div>

                <form action="add_to_cart.php" method="POST">
                    <div class="variant-group">
                        <label>Chọn phiên bản & Màu sắc:</label>
                        <select name="variant_id" id="variant_select" class="variant-select" onchange="updatePrice()" required>
                            <?php if (!empty($variants)): ?>
                                <?php foreach ($variants as $index => $v): ?>
                                    <option value="<?= $v['id'] ?>" data-price="<?= $v['price'] ?>" <?= $v['stock'] <= 0 ? 'disabled' : '' ?>>
                                        <?= htmlspecialchars($v['color']) ?> - <?= htmlspecialchars($v['version']) ?> 
                                        <?= $v['stock'] > 0 ? '(Kho: ' . $v['stock'] . ')' : '- TẠM HẾT HÀNG' ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <option value="">Sản phẩm đang tạm hết hàng</option>
                            <?php endif; ?>
                        </select>
                    </div>

                    <div class="qty-group">
                        <label style="font-weight: 600; color: #444;">Số lượng:</label>
                        <input type="number" name="quantity" value="1" min="1" required>
                    </div>

                    <?php if (!empty($variants)): ?>
                        <div style="display: flex; gap: 15px; width: 100%;">
                            <button type="submit" name="btn_add_cart" class="btn-add-cart" style="flex: 1; background: #fff; color: #d70018; border: 2px solid #d70018;">
                                <i class="fa-solid fa-cart-plus"></i> Thêm Vào Giỏ
                            </button>
                            <button type="submit" name="btn_buy_now" class="btn-add-cart" style="flex: 1;">
                                Mua Ngay
                            </button>
                        </div>
                    <?php else: ?>
                        <button type="button" class="btn-add-cart" style="background: #ccc; cursor: not-allowed; width: 100%;" disabled>Tạm Hết Hàng</button>
                    <?php endif; ?>
                </form>

                <div class="store-policies">
                    <h4><i class="fa-solid fa-medal"></i> Yên tâm mua sắm tại PhoneStore</h4>
                    <ul>
                        <?php if ($is_accessory): ?>
                            <li><i class="fa-solid fa-box-open"></i> Bộ sản phẩm gồm: Hộp, Sách hướng dẫn, Sản phẩm chính.</li>
                            <li><i class="fa-solid fa-shield-halved"></i> Bảo hành chính hãng từ 3 - 12 tháng (tùy loại phụ kiện).</li>
                        <?php else: ?>
                            <li><i class="fa-solid fa-box-open"></i> Bộ sản phẩm gồm: Hộp, Sách hướng dẫn, Cây lấy sim, Cáp sạc.</li>
                            <li><i class="fa-solid fa-shield-halved"></i> Bảo hành chính hãng 12 tháng tại trung tâm bảo hành.</li>
                        <?php endif; ?>
                        
                        <li><i class="fa-solid fa-rotate-left"></i> 1 ĐỔI 1 trong 30 ngày đầu tiên nếu phát sinh lỗi phần cứng.</li>
                        <li><i class="fa-solid fa-truck-fast"></i> Giao hàng tận nhà siêu tốc (áp dụng với các khu vực trung tâm).</li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="bottom-layout" id="reviews-section">
            
            <div class="reviews-section">
                <h3 class="review-header-title">⭐ Đánh Giá & Bình Luận (<?= $total_reviews ?> lượt)</h3>
                <div class="review-filter">
                    <a href="detail.php?id=<?= $product_id ?>#reviews-section" class="btn-filter-star <?= $filter_star == 0 ? 'active' : '' ?>">Tất cả</a>
                    <a href="detail.php?id=<?= $product_id ?>&star=5#reviews-section" class="btn-filter-star <?= $filter_star == 5 ? 'active' : '' ?>">5 Sao (<?= $star_counts[5] ?>)</a>
                    <a href="detail.php?id=<?= $product_id ?>&star=4#reviews-section" class="btn-filter-star <?= $filter_star == 4 ? 'active' : '' ?>">4 Sao (<?= $star_counts[4] ?>)</a>
                    <a href="detail.php?id=<?= $product_id ?>&star=3#reviews-section" class="btn-filter-star <?= $filter_star == 3 ? 'active' : '' ?>">3 Sao (<?= $star_counts[3] ?>)</a>
                    <a href="detail.php?id=<?= $product_id ?>&star=2#reviews-section" class="btn-filter-star <?= $filter_star == 2 ? 'active' : '' ?>">2 Sao (<?= $star_counts[2] ?>)</a>
                    <a href="detail.php?id=<?= $product_id ?>&star=1#reviews-section" class="btn-filter-star <?= $filter_star == 1 ? 'active' : '' ?>">1 Sao (<?= $star_counts[1] ?>)</a>
                </div>
                <?php if (isset($_SESSION['user_client'])): ?>
                    <form action="" method="POST" class="review-form">
                        <div>
                            <label style="font-weight: 600; color: #333;">Mức độ hài lòng của bạn:</label>
                            <select name="rating" class="rating-select" required>
                                <option value="5">⭐⭐⭐⭐⭐ - Tuyệt vời</option>
                                <option value="4">⭐⭐⭐⭐ - Rất tốt</option>
                                <option value="3">⭐⭐⭐ - Bình thường</option>
                                <option value="2">⭐⭐ - Tạm được</option>
                                <option value="1">⭐ - Rất tệ</option>
                            </select>
                        </div>
                        <textarea name="comment" class="review-textarea" rows="3" placeholder="Xin mời để lại đánh giá, nhận xét của bạn về sản phẩm này..." required></textarea>
                        <button type="submit" name="submit_review" class="btn-submit-review"><i class="fa-solid fa-paper-plane"></i> Gửi Đánh Giá</button>
                    </form>
                <?php else: ?>
                    <div class="login-to-review">
                        Vui lòng <a href="login.php" style="color: #d70018; font-weight: bold; text-decoration: underline;">Đăng nhập</a> để tham gia bình luận và đánh giá sản phẩm.
                    </div>
                <?php endif; ?>

                <div class="reviews-list">
                    <?php if ($total_reviews > 0): ?>
                        <?php foreach ($reviews as $rev): ?>
                            <div class="review-item">
                                <div class="rev-avatar">
                                    <i class="fa-solid fa-circle-user"></i>
                                </div>
                                <div>
                                    <div style="margin-bottom: 5px;">
                                        <span class="rev-name"><?= htmlspecialchars($rev['username']) ?></span>
                                        <span class="rev-date"><i class="fa-regular fa-clock"></i> <?= date('d/m/Y H:i', strtotime($rev['created_at'])) ?></span>
                                    </div>
                                    <div class="rev-stars">
                                        <?= str_repeat('⭐', $rev['rating']) ?>
                                    </div>
                                    <p class="rev-text">
                                        <?= nl2br(htmlspecialchars($rev['comment'])) ?>
                                    </p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div style="text-align: center; padding: 30px; color: #999;">
                            <i class="fa-regular fa-comments" style="font-size: 40px; margin-bottom: 15px; color: #ddd;"></i>
                            <p>Chưa có đánh giá nào cho sản phẩm này.<br>Hãy là người đầu tiên để lại nhận xét nhé!</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="specs-section">
                <h3 class="review-header-title">⚙️ Thông Số Kỹ Thuật</h3>
                <table class="specs-table" style="width: 100%; border-collapse: collapse;">
                    <tbody>
                        <?php 
                        if (is_array($specs) && !empty($specs)) {
                            $translate = [
                                'os' => 'Hệ điều hành',
                                'cpu' => 'Vi xử lý (CPU)',
                                'ram' => 'Bộ nhớ RAM',
                                'storage' => 'Bộ nhớ trong (ROM)',
                                'battery' => 'Dung lượng pin',
                                'screen' => 'Màn hình',
                                'camera' => 'Camera'
                            ];

                            foreach ($specs as $key => $value) {
                                $display_key = isset($translate[$key]) ? $translate[$key] : ucfirst($key);
                                echo "<tr style='border-bottom: 1px solid #eee;'>";
                                echo "<td style='padding: 12px 0; color: #555;'><strong>" . htmlspecialchars($display_key) . "</strong></td>";
                                echo "<td style='padding: 12px 0; text-align: right; color: #333;'>" . htmlspecialchars($value) . "</td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='2' style='color: #777;'>Chưa có thông số chi tiết.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>

        </div>
    </main>

    <?php include "includes/footer.php"; ?>

    <script src="assets/js/detail.js"></script>
    <script src="assets/js/script.js"></script>
</body>
</html>
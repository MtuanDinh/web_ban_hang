<?php
require_once("includes/connect_db.php");

// --- 1. LẤY DANH MỤC LÀM LOẠI PHỤ KIỆN (TYPES) ---
// CHỈ lấy các danh mục con có danh mục cha là Phụ kiện (parent_id = 2)
$sql_types = "SELECT * FROM categories WHERE parent_id = 2 ORDER BY id ASC";
$res_types = mysqli_query($conn, $sql_types);

$types = [];
$allowed_cat_ids = [2]; // Đưa sẵn ID 2 vào mảng

while ($row = mysqli_fetch_assoc($res_types)) {
    $types[] = $row;
    $allowed_cat_ids[] = $row['id']; 
}

$allowed_cats_string = implode(',', $allowed_cat_ids);

// --- 2. XỬ LÝ LỌC VÀ SẮP XẾP (Đã đổi 'brand' thành 'type') ---
$type_filter = isset($_GET['type']) ? (int)$_GET['type'] : 0;
$price_filter = isset($_GET['price']) ? $_GET['price'] : '';
$sort = isset($_GET['sort']) ? $_GET['sort'] : '';

// Xây dựng điều kiện WHERE 
$where_arr = []; 

if ($type_filter > 0) {
    // Nếu khách hàng truyền ?type=... từ trang chủ hoặc bấm chọn
    $where_arr[] = "p.category_id = $type_filter";
} else {
    // Nếu bấm "Tất cả", giới hạn trong nhóm Phụ kiện
    $where_arr[] = "p.category_id IN ($allowed_cats_string)";
}

$where_clause = "WHERE " . implode(" AND ", $where_arr);

// TUYỆT CHIÊU: Xây dựng điều kiện HAVING (Khoảng giá chuẩn cho Phụ Kiện)
$having_clause = "";
if ($price_filter == 'duoi-200') $having_clause = "HAVING min_price > 0 AND min_price < 200000";
elseif ($price_filter == '200-500') $having_clause = "HAVING min_price >= 200000 AND min_price <= 500000";
elseif ($price_filter == '500-1t') $having_clause = "HAVING min_price > 500000 AND min_price <= 1000000";
elseif ($price_filter == 'tren-1t') $having_clause = "HAVING min_price > 1000000";

// Xây dựng điều kiện ORDER BY
$order_by = "";
if ($sort == 'asc') $order_by = "ORDER BY min_price ASC";
elseif ($sort == 'desc') $order_by = "ORDER BY min_price DESC";

// --- 3. PHÂN TRANG (PAGINATION) ---
$limit = 24; 
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page <= 0) $page = 1;
$offset = ($page - 1) * $limit;

$sql_count = "SELECT COUNT(*) as total FROM (
                SELECT p.id, IFNULL(MIN(pv.price), 0) as min_price 
                FROM products p 
                LEFT JOIN product_variants pv ON p.id = pv.product_id 
                $where_clause 
                GROUP BY p.id 
                $having_clause
              ) as subquery";
$res_count = mysqli_query($conn, $sql_count);
$total_records = mysqli_fetch_assoc($res_count)['total'];
$total_pages = ceil($total_records / $limit);

// --- 4. TRUY VẤN DỮ LIỆU CHÍNH ---
$sql = "SELECT p.*, IFNULL(MIN(pv.price), 0) as min_price 
        FROM products p 
        LEFT JOIN product_variants pv ON p.id = pv.product_id 
        $where_clause 
        GROUP BY p.id 
        $having_clause 
        $order_by 
        LIMIT $limit OFFSET $offset";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Phụ kiện Chính Hãng - PhoneStore</title>
    <script src="https://kit.fontawesome.com/da1a483940.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="assets/css/style_client.css">
    <link rel="stylesheet" href="assets/css/style_category.css">
</head>
<body>
    <?php include "includes/header.php"; include "includes/nav.php"; ?>

    

    <main class="main-content category-container">
        
        <div class="breadcrumb">
            <a href="index.php"><i class="fa-solid fa-house-chimney"></i> Trang chủ</a> 
            <span style="color: #ccc; margin: 0 8px;">/</span> 
            <span style="color: #555; font-weight: 600;">Phụ kiện</span>
        </div>

        <div class="category-promo-banners">
            <div class="promo-main">
                <img src="assets/image/samsung-galaxy-slide.webp" alt="Samsung Promo" class="promo-img">
            </div>
            <div class="promo-sub">
                <img src="assets/image/iphonebannersmall.jpg" alt="iPhone Promo" class="promo-img">
                <img src="assets/image/anhbannerxiaomismall.jpg" alt="Xiaomi Promo" class="promo-img">
            </div>
        </div>

        <div style="display: flex; justify-content: space-between; align-items: flex-end; border-bottom: 2px solid #d70018; padding-bottom: 10px; margin-bottom: 20px;">
            <h2 class="page-title" style="margin: 0; font-size: 24px; text-transform: uppercase; color: #333; font-weight: 800;">Phụ kiện chính hãng</h2>
        </div>

        <div class="filter-box-wrapper">
            <form action="category_accessory.php" method="GET" id="filterForm">
                
                <div class="filter-row">
                    <div class="filter-label"><i class="fa-solid fa-filter"></i> Loại phụ kiện:</div>
                    <div class="filter-options">
                        <label>
                            <input type="radio" name="type" value="0" onchange="document.getElementById('filterForm').submit();" <?= ($type_filter == 0) ? 'checked' : '' ?>>
                            <span class="btn-filter">Tất cả</span>
                        </label>
                        <?php foreach ($types as $t): ?>
                            <label>
                                <input type="radio" name="type" value="<?= $t['id'] ?>" onchange="document.getElementById('filterForm').submit();" <?= ($type_filter == $t['id']) ? 'checked' : '' ?>>
                                <span class="btn-filter"><?= htmlspecialchars($t['name']) ?></span>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="filter-row">
                    <div class="filter-label"><i class="fa-solid fa-money-bill-wave"></i> Mức giá:</div>
                    <div class="filter-options">
                        <label>
                            <input type="radio" name="price" value="" onchange="document.getElementById('filterForm').submit();" <?= ($price_filter == '') ? 'checked' : '' ?>>
                            <span class="btn-filter">Tất cả khoảng giá</span>
                        </label>
                        <label>
                            <input type="radio" name="price" value="duoi-200" onchange="document.getElementById('filterForm').submit();" <?= ($price_filter == 'duoi-200') ? 'checked' : '' ?>>
                            <span class="btn-filter">Dưới 200.000đ</span>
                        </label>
                        <label>
                            <input type="radio" name="price" value="200-500" onchange="document.getElementById('filterForm').submit();" <?= ($price_filter == '200-500') ? 'checked' : '' ?>>
                            <span class="btn-filter">Từ 200k - 500k</span>
                        </label>
                        <label>
                            <input type="radio" name="price" value="500-1t" onchange="document.getElementById('filterForm').submit();" <?= ($price_filter == '500-1t') ? 'checked' : '' ?>>
                            <span class="btn-filter">Từ 500k - 1 triệu</span>
                        </label>
                        <label>
                            <input type="radio" name="price" value="tren-1t" onchange="document.getElementById('filterForm').submit();" <?= ($price_filter == 'tren-1t') ? 'checked' : '' ?>>
                            <span class="btn-filter">Trên 1 triệu</span>
                        </label>
                    </div>
                </div>

                <div class="filter-row" style="margin-bottom: 0; padding-bottom: 0; border-bottom: none;">
                    <div class="filter-label"><i class="fa-solid fa-arrow-down-a-z"></i> Sắp xếp theo:</div>
                    <div class="filter-options">
                        <label>
                            <input type="radio" name="sort" value="" onchange="document.getElementById('filterForm').submit();" <?= ($sort == '') ? 'checked' : '' ?>>
                            <span class="btn-filter">Nổi bật nhất</span>
                        </label>
                        <label>
                            <input type="radio" name="sort" value="asc" onchange="document.getElementById('filterForm').submit();" <?= ($sort == 'asc') ? 'checked' : '' ?>>
                            <span class="btn-filter">Giá: Thấp đến Cao</span>
                        </label>
                        <label>
                            <input type="radio" name="sort" value="desc" onchange="document.getElementById('filterForm').submit();" <?= ($sort == 'desc') ? 'checked' : '' ?>>
                            <span class="btn-filter">Giá: Cao xuống Thấp</span>
                        </label>
                    </div>
                </div>

            </form>
        </div>

        <?php if ($total_records > 0): ?>
            <div style="margin-bottom: 15px; color: #555; font-size: 15px;">
                <i class="fa-solid fa-check-double" style="color: #10b981;"></i> Hiển thị <b><?= $total_records ?></b> sản phẩm phù hợp.
            </div>
            
            <div class="product-grid">
                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                    <?php include("includes/product_card.php") ?>
                <?php endwhile; ?>
            </div>

            <?php if ($total_pages > 1): ?>
                <div class="pagination">
                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                        <a href="?type=<?= $type_filter ?>&price=<?= $price_filter ?>&sort=<?= $sort ?>&page=<?= $i ?>" 
                           class="page-link <?= ($i == $page) ? 'active' : '' ?>">
                           <?= $i ?>
                        </a>
                    <?php endfor; ?>
                </div>
            <?php endif; ?>

        <?php else: ?>
            <div class="empty-result" style="text-align: center; padding: 50px 20px; background: #fff; border-radius: 12px; border: 1px dashed #ccc; margin-bottom: 30px;">
                <i class="fa-solid fa-box-open" style="font-size: 60px; color: #cbd5e1; margin-bottom: 20px;"></i>
                <h3 style="color: #555;">Không tìm thấy sản phẩm nào phù hợp với bộ lọc!</h3>
                <p style="color: #777;">Vui lòng thử bỏ bớt các tiêu chí lọc để xem thêm sản phẩm.</p>
                <a href="category_accessory.php" style="margin-top: 15px; display: inline-block; padding: 10px 25px; border: 1px solid #d70018; color: #d70018; border-radius: 8px; text-decoration: none; font-weight: bold; transition: 0.3s;">Xóa bộ lọc</a>
            </div>
        <?php endif; ?>

    </main>

    <?php include "includes/footer.php"; ?>
</body>
</html>
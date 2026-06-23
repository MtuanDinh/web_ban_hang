<?php
require_once("includes/connect_db.php");

// --- 1. LẤY DANH MỤC LÀM NHÃN HIỆU (BRANDS) ---
// CHỈ lấy các danh mục con có danh mục cha là Phụ kiện (parent_id = 2)
$sql_brands = "SELECT * FROM categories WHERE parent_id = 2 ORDER BY id ASC";
$res_brands = mysqli_query($conn, $sql_brands);

$brands = [];
$allowed_cat_ids = [2]; // Đưa sẵn ID 2 vào mảng phòng trường hợp có sp gắn thẳng vào thẻ cha

while ($row = mysqli_fetch_assoc($res_brands)) {
    $brands[] = $row;
    $allowed_cat_ids[] = $row['id']; // Gom các ID con lại (VD: ID của Apple, Samsung)
}

// Biến danh sách ID thành chuỗi để dùng cho lệnh SQL IN (VD: "1,2,5,7")
$allowed_cats_string = implode(',', $allowed_cat_ids);

// --- 2. XỬ LÝ LỌC VÀ SẮP XẾP ---
$brand_filter = isset($_GET['brand']) ? (int)$_GET['brand'] : 0;
$price_filter = isset($_GET['price']) ? $_GET['price'] : '';
$sort = isset($_GET['sort']) ? $_GET['sort'] : '';

// Xây dựng điều kiện WHERE (Cho Nhãn hàng và Loại sản phẩm)
$where_arr = []; 

if ($brand_filter > 0) {
    // Nếu khách hàng chọn đích danh 1 hãng (VD: Apple)
    $where_arr[] = "p.category_id = $brand_filter";
} else {
    // RÀNG BUỘC THÉP: Nếu khách bấm "Tất cả", vẫn CHỈ hiển thị sản phẩm thuộc nhóm Điện thoại
    $where_arr[] = "p.category_id IN ($allowed_cats_string)";
}

$where_clause = "WHERE " . implode(" AND ", $where_arr);

// Xây dựng điều kiện HAVING (Cho Khoảng giá, vì giá là cột tổng hợp MIN)
$having_clause = "";
if ($price_filter == 'duoi-5') $having_clause = "HAVING min_price > 0 AND min_price < 5000000";
elseif ($price_filter == '5-15') $having_clause = "HAVING min_price >= 5000000 AND min_price <= 15000000";
elseif ($price_filter == '15-25') $having_clause = "HAVING min_price > 15000000 AND min_price <= 25000000";
elseif ($price_filter == 'tren-25') $having_clause = "HAVING min_price > 25000000";

// Xây dựng điều kiện ORDER BY (Cho Sắp xếp)
$order_by = "";
if ($sort == 'asc') $order_by = "ORDER BY min_price ASC";
elseif ($sort == 'desc') $order_by = "ORDER BY min_price DESC";

// --- 3. PHÂN TRANG (PAGINATION) ---
$limit = 24; 
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page <= 0) $page = 1;
$offset = ($page - 1) * $limit;

// Đếm tổng số lượng (Phải bọc trong Subquery vì có mệnh đề HAVING)
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
    <title>Điện thoại, Tablet Chính Hãng - PhoneStore</title>
    <script src="https://kit.fontawesome.com/da1a483940.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="assets/css/style_client.css">
    <link rel="stylesheet" href="assets/css/style_category.css">
    
</head>
<body>
    <?php include "includes/header.php"; include "includes/nav.php"; ?>

    <main class="main-content category-container">
        <h2 class="page-title">Phụ kiện</h2>

        <div class="filter-section">
            <form action="category_accessory.php" method="GET" id="filterForm">
                
                <div class="filter-row">
                    <div class="filter-label">Hãng sản xuất:</div>
                    <div class="filter-options">
                        <label>
                            <input type="radio" name="brand" value="0" onchange="document.getElementById('filterForm').submit();" <?= ($brand_filter == 0) ? 'checked' : '' ?>>
                            <span class="btn-filter">Tất cả</span>
                        </label>
                        <?php foreach ($brands as $b): ?>
                            <label>
                                <input type="radio" name="brand" value="<?= $b['id'] ?>" onchange="document.getElementById('filterForm').submit();" <?= ($brand_filter == $b['id']) ? 'checked' : '' ?>>
                                <span class="btn-filter"><?= htmlspecialchars($b['name']) ?></span>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="filter-row">
                    <div class="filter-label">Mức giá:</div>
                    <div class="filter-options">
                        <label>
                            <input type="radio" name="price" value="" onchange="document.getElementById('filterForm').submit();" <?= ($price_filter == '') ? 'checked' : '' ?>>
                            <span class="btn-filter">Tất cả</span>
                        </label>
                        <label>
                            <input type="radio" name="price" value="duoi-5" onchange="document.getElementById('filterForm').submit();" <?= ($price_filter == 'duoi-5') ? 'checked' : '' ?>>
                            <span class="btn-filter">Dưới 5 triệu</span>
                        </label>
                        <label>
                            <input type="radio" name="price" value="5-15" onchange="document.getElementById('filterForm').submit();" <?= ($price_filter == '5-15') ? 'checked' : '' ?>>
                            <span class="btn-filter">Từ 5 - 15 triệu</span>
                        </label>
                        <label>
                            <input type="radio" name="price" value="15-25" onchange="document.getElementById('filterForm').submit();" <?= ($price_filter == '15-25') ? 'checked' : '' ?>>
                            <span class="btn-filter">Từ 15 - 25 triệu</span>
                        </label>
                        <label>
                            <input type="radio" name="price" value="tren-25" onchange="document.getElementById('filterForm').submit();" <?= ($price_filter == 'tren-25') ? 'checked' : '' ?>>
                            <span class="btn-filter">Trên 25 triệu</span>
                        </label>
                    </div>
                </div>

                <div class="filter-row">
                    <div class="filter-label">Sắp xếp theo:</div>
                    <div class="filter-options">
                        <label>
                            <input type="radio" name="sort" value="" onchange="document.getElementById('filterForm').submit();" <?= ($sort == '') ? 'checked' : '' ?>>
                            <span class="btn-filter">Nổi bật</span>
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
            <div style="margin-bottom: 15px; color: #555;">Hiển thị <b><?= $total_records ?></b> sản phẩm.</div>
            
            <div class="product-grid">
                <?php while ($row = mysqli_fetch_assoc($result)): 
                    $current_price = $row['min_price'] ? $row['min_price'] : 0;
                    $old_price = $current_price * 1.1; 
                    $img_src = !empty($row['image']) ? 'assets/uploads/' . $row['image'] : 'https://via.placeholder.com/300x300?text=No+Image';
                ?>
                    <a href="detail.php?id=<?= $row['id'] ?>" class="product-card" style="text-decoration: none; color: inherit;">
                        <div class="product-image">
                            <img src="<?= htmlspecialchars($img_src) ?>" alt="<?= htmlspecialchars($row['name']) ?>">
                        </div>
                        <h3 class="product-name"><?= htmlspecialchars($row['name']) ?></h3>
                        <div class="product-price">
                            <span class="price-current"><?= $current_price > 0 ? number_format($current_price, 0, ',', '.') . 'đ' : 'Liên hệ' ?></span>
                            <?php if ($current_price > 0): ?>
                                <span class="price-old"><?= number_format($old_price, 0, ',', '.') ?>đ</span>
                            <?php endif; ?>
                        </div>
                    </a>
                <?php endwhile; ?>
            </div>

            <?php if ($total_pages > 1): ?>
                <div class="pagination">
                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                        <a href="?brand=<?= $brand_filter ?>&price=<?= $price_filter ?>&sort=<?= $sort ?>&page=<?= $i ?>" 
                           class="page-link <?= ($i == $page) ? 'active' : '' ?>">
                           <?= $i ?>
                        </a>
                    <?php endfor; ?>
                </div>
            <?php endif; ?>

        <?php else: ?>
            <div class="empty-result">
                <i class="fa-solid fa-box-open" style="font-size: 50px; color: #ccc; margin-bottom: 15px;"></i>
                <h3 style="color: #666;">Không tìm thấy sản phẩm nào phù hợp với bộ lọc!</h3>
                <p style="color: #888;">Vui lòng thử bỏ bớt các tiêu chí lọc để xem thêm sản phẩm.</p>
                <a href="category_phone.php" class="btn-filter" style="margin-top: 15px; border-color: #d70018; color: #d70018;">Xóa bộ lọc</a>
            </div>
        <?php endif; ?>

    </main>

    <?php include "includes/footer.php"; ?>
</body>
</html>
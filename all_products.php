<?php
require_once("includes/connect_db.php"); // (Hoặc require_once("connect_db.php"); tùy vị trí file của bạn)

// 1. Nhận các tham số lọc từ URL (nếu có)
$filter_category = isset($_GET['category']) ? (int)$_GET['category'] : 0;
$filter_min_price = isset($_GET['min_price']) && $_GET['min_price'] !== '' ? (int)$_GET['min_price'] : 0;
$filter_max_price = isset($_GET['max_price']) && $_GET['max_price'] !== '' ? (int)$_GET['max_price'] : 0;
$filter_sort = isset($_GET['sort']) ? $_GET['sort'] : 'newest';

// 2. Xây dựng câu lệnh SQL tự động lấy Điện thoại (ID = 1)
$sql = "SELECT p.id, p.name, p.image, MIN(pv.price) as min_price 
        FROM products p 
        LEFT JOIN product_variants pv ON p.id = pv.product_id 
        WHERE (p.category_id = 1 OR p.category_id IN (SELECT id FROM categories WHERE parent_id = 1)) ";

// Lọc thêm theo danh mục con nếu người dùng chọn
if ($filter_category > 0) {
    $sql .= " AND p.category_id = $filter_category ";
}

// Gom nhóm (Lưu ý phải có dấu cách trước chữ GROUP)
$sql .= " GROUP BY p.id ";

// Lọc theo giá (Dùng HAVING vì giá min_price là giá trị gộp)
$having_clauses = [];
if ($filter_min_price > 0) {
    $having_clauses[] = "min_price >= $filter_min_price";
}
if ($filter_max_price > 0) {
    $having_clauses[] = "min_price <= $filter_max_price";
}

if (!empty($having_clauses)) {
    $sql .= " HAVING " . implode(" AND ", $having_clauses);
}

// Lọc sắp xếp
if ($filter_sort == 'price_asc') {
    $sql .= " ORDER BY min_price ASC ";
} elseif ($filter_sort == 'price_desc') {
    $sql .= " ORDER BY min_price DESC ";
} else {
    $sql .= " ORDER BY p.id DESC ";
}

// Chạy truy vấn lấy sản phẩm
$result = mysqli_query($conn, $sql);

// Truy vấn lấy danh sách Category cho Cột bộ lọc (Chỉ lấy con của Điện thoại)
$sql_cats = "SELECT id, name FROM categories WHERE parent_id = 1 ORDER BY id ASC";
$result_cats = mysqli_query($conn, $sql_cats);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tất cả sản phẩm</title>
    <script src="https://kit.fontawesome.com/da1a483940.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="assets/css/style_client.css">
    <style>
        /* ================= CSS CHO TRANG ALL PRODUCTS ================= */
        .all-products-page .filter-sidebar {
        flex: 0 0 250px; /* Độ rộng cột trái 250px */
        background: #fff;
        border-radius: 10px;
        padding: 20px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        height: fit-content;
        }

        .filter-sidebar h3 {
        margin-top: 0;
        font-size: 18px;
        color: #d70018;
        border-bottom: 1px solid #f0f0f0;
        padding-bottom: 10px;
        margin-bottom: 20px;
        }

        .filter-group {
        margin-bottom: 20px;
        }

        .filter-group label {
        display: block;
        font-weight: 600;
        font-size: 14px;
        margin-bottom: 8px;
        color: #333;
        }

        .filter-group select {
        width: 100%;
        padding: 8px;
        border: 1px solid #ccc;
        border-radius: 5px;
        outline: none;
        }

        .price-inputs {
        display: flex;
        align-items: center;
        gap: 10px;
        }

        .price-inputs input {
        width: 100%;
        padding: 8px;
        border: 1px solid #ccc;
        border-radius: 5px;
        outline: none;
        }

        .btn-filter-submit {
        width: 100%;
        background-color: #d70018;
        color: #fff;
        border: none;
        padding: 10px;
        border-radius: 5px;
        font-weight: bold;
        cursor: pointer;
        margin-bottom: 10px;
        transition: 0.2s;
        }

        .btn-filter-submit:hover {
        background-color: #c00015;
        }

        .btn-filter-clear {
        display: block;
        text-align: center;
        width: 100%;
        background-color: #f3f4f6;
        color: #333;
        text-decoration: none;
        padding: 10px;
        border-radius: 5px;
        font-size: 14px;
        transition: 0.2s;
        }

        .btn-filter-clear:hover {
        background-color: #e5e7eb;
        }

        .all-products-page .products-area {
        flex: 1; /* Cột phải tự động giãn hết phần còn lại */
        }
    </style>
</head>

<body>
    <?php
    include "includes/header.php";
    include "includes/nav.php";
    ?>

    <main class="main-content all-products-page">
        <div class="container-fluid" style="max-width: 1200px; margin: 20px auto; padding: 0 15px; display: flex; gap: 20px;">

            <aside class="filter-sidebar">
                <h3><i class="fa-solid fa-filter"></i> Bộ Lọc</h3>
                <form action="all_products.php" method="GET">

                    <div class="filter-group">
                        <label>Hãng sản xuất</label>
                        <select name="category">
                            <option value="0">Tất cả hãng</option>
                            <?php
                            if ($result_cats && mysqli_num_rows($result_cats) > 0) {
                                while ($cat = mysqli_fetch_assoc($result_cats)) {
                                    $selected = ($filter_category == $cat['id']) ? 'selected' : '';
                                    echo "<option value='{$cat['id']}' $selected>{$cat['name']}</option>";
                                }
                            }
                            ?>
                        </select>
                    </div>

                    <div class="filter-group">
                        <label>Mức giá (VNĐ)</label>
                        <div class="price-inputs">
                            <input type="number" name="min_price" placeholder="Từ..." value="<?= $filter_min_price > 0 ? $filter_min_price : '' ?>">
                            <span>-</span>
                            <input type="number" name="max_price" placeholder="Đến..." value="<?= $filter_max_price > 0 ? $filter_max_price : '' ?>">
                        </div>
                    </div>

                    <div class="filter-group">
                        <label>Sắp xếp theo</label>
                        <select name="sort">
                            <option value="newest" <?= $filter_sort == 'newest' ? 'selected' : '' ?>>Mới nhất</option>
                            <option value="price_asc" <?= $filter_sort == 'price_asc' ? 'selected' : '' ?>>Giá tăng dần</option>
                            <option value="price_desc" <?= $filter_sort == 'price_desc' ? 'selected' : '' ?>>Giá giảm dần</option>
                        </select>
                    </div>

                    <button type="submit" class="btn-filter-submit">Áp dụng lọc</button>
                    <a href="all_products.php" class="btn-filter-clear">Xóa lọc</a>
                </form>
            </aside>

            <section class="products-area">
                <div class="section-header" style="margin-bottom: 15px;">
                    <h2>TẤT CẢ ĐIỆN THOẠI</h2>
                </div>

                <div class="product-grid">
                    <?php
                    if ($result && mysqli_num_rows($result) > 0) {
                        while ($row = mysqli_fetch_assoc($result)) {
                    ?>
                           <?php include("includes/product_card.php") ?>
                    <?php
                        }
                    } else {
                        echo "<p style='grid-column: 1 / -1; text-align: center; padding: 50px; font-size: 16px;'>Không tìm thấy sản phẩm nào phù hợp với bộ lọc.</p>";
                    }
                    ?>
                </div>
            </section>
        </div>
    </main>

    <?php include "includes/footer.php"; ?>
    <script src="assets/js/script.js"></script>
</body>

</html>
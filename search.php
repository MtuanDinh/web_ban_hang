<?php
require_once("includes/connect_db.php");

$keyword = "";
$sort = "";
$result = null;
$total_records = 0;
$total_pages = 0;

// THIẾT LẬP SỐ LƯỢNG SẢN PHẨM TRÊN 1 TRANG
$limit = 24; 

// Lấy trang hiện tại (Mặc định là trang 1 nếu không có tham số)
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page <= 0) $page = 1;
$offset = ($page - 1) * $limit;

if (isset($_GET['keyword']) && trim($_GET['keyword']) != "") {
    $keyword = trim($_GET['keyword']);
    $safe_keyword = mysqli_real_escape_string($conn, $keyword);
    
    // 1. KIỂM TRA YÊU CẦU SẮP XẾP
    $order_by = ""; 
    if (isset($_GET['sort'])) {
        $sort = $_GET['sort'];
        if ($sort == 'asc') $order_by = "ORDER BY min_price ASC";
        elseif ($sort == 'desc') $order_by = "ORDER BY min_price DESC";
    }
    
    // 2. ĐẾM TỔNG SỐ SẢN PHẨM (Để chia số trang)
    // Dùng COUNT(DISTINCT p.id) để tránh đếm trùng nếu 1 sản phẩm có nhiều phiên bản
    $sql_count = "SELECT COUNT(DISTINCT p.id) as total 
                  FROM products p 
                  LEFT JOIN product_variants pv ON p.id = pv.product_id 
                  WHERE p.name LIKE '%$safe_keyword%'";
    $res_count = mysqli_query($conn, $sql_count);
    $row_count = mysqli_fetch_assoc($res_count);
    $total_records = $row_count['total'];
    
    // Tính tổng số trang (Dùng hàm ceil để làm tròn lên)
    $total_pages = ceil($total_records / $limit);

    // 3. TRUY VẤN DỮ LIỆU CÓ GIỚI HẠN (LIMIT & OFFSET)
    $sql = "SELECT p.*, MIN(pv.price) as min_price 
            FROM products p 
            LEFT JOIN product_variants pv ON p.id = pv.product_id 
            WHERE p.name LIKE '%$safe_keyword%' 
            GROUP BY p.id 
            $order_by 
            LIMIT $limit OFFSET $offset";
            
    $result = mysqli_query($conn, $sql);
} else {
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tìm kiếm: <?= htmlspecialchars($keyword) ?> - PhoneStore</title>
    <script src="https://kit.fontawesome.com/da1a483940.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="assets/css/style_client.css">
    <style>
        .search-header { max-width: 1200px; margin: 30px auto 20px auto; padding: 0 15px; }
        .search-title { font-size: 22px; color: #333; margin-bottom: 10px;}
        .search-keyword { color: #d70018; font-style: italic; }
        .filter-bar { display: flex; justify-content: space-between; align-items: center; background: #f8f9fa; padding: 15px 20px; border-radius: 8px; margin-bottom: 30px; border: 1px solid #eee;}
        .product-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(230px, 1fr)); gap: 20px; max-width: 1200px; margin: 0 auto 50px auto; padding: 0 15px; }
        .empty-search { text-align: center; padding: 80px 20px; background: #fff; border-radius: 8px; max-width: 1200px; margin: 0 auto; box-shadow: 0 2px 10px rgba(0,0,0,0.05); }
        .empty-search i { font-size: 60px; color: #ccc; margin-bottom: 20px; }
        
        /* CSS CHO THANH PHÂN TRANG */
        .pagination { display: flex; justify-content: center; align-items: center; gap: 8px; margin-bottom: 60px; }
        .page-link { display: flex; justify-content: center; align-items: center; width: 40px; height: 40px; border-radius: 6px; background: #fff; border: 1px solid #ddd; color: #333; text-decoration: none; font-weight: 600; transition: 0.3s; }
        .page-link:hover { border-color: #d70018; color: #d70018; }
        .page-link.active { background: #d70018; color: #fff; border-color: #d70018; pointer-events: none; }
    </style>
</head>
<body>
    <?php include "includes/header.php"; include "includes/nav.php"; ?>

    <main class="main-content">
        <div class="search-header">
            <h2 class="search-title">Kết quả tìm kiếm cho: <span class="search-keyword">"<?= htmlspecialchars($keyword) ?>"</span></h2>
            
            <?php if ($total_records > 0): ?>
                <div class="filter-bar">
                    <p style='color: #555; margin: 0;'>Tìm thấy <b><?= $total_records ?></b> sản phẩm phù hợp.</p>
                    
                    <form action="search.php" method="GET" style="display: flex; align-items: center; gap: 10px;">
                        <input type="hidden" name="keyword" value="<?= htmlspecialchars($keyword) ?>">
                        
                        <label for="sort" style="font-weight: 600; color: #444; font-size: 14px;">Sắp xếp theo:</label>
                        <select name="sort" id="sort" onchange="this.form.submit()" style="padding: 8px 12px; border: 1px solid #ddd; border-radius: 6px; outline: none; cursor: pointer; font-size: 14px;">
                            <option value="">-- Mặc định --</option>
                            <option value="asc" <?= ($sort == 'asc') ? 'selected' : '' ?>>Giá: Thấp đến Cao</option>
                            <option value="desc" <?= ($sort == 'desc') ? 'selected' : '' ?>>Giá: Cao xuống Thấp</option>
                        </select>
                    </form>
                </div>
            <?php endif; ?>
        </div>

        <?php if ($total_records > 0): ?>
            <div class="product-grid">
                <?php while ($row = mysqli_fetch_assoc($result)): 
                    $current_price = $row['min_price'] ? $row['min_price'] : 0;
                    $old_price = $current_price * 1.1; 
                    $discount_percent = 10;
                    $img_src = !empty($row['image']) ? 'assets/uploads/' . $row['image'] : 'https://via.placeholder.com/300x300?text=No+Image';
                ?>
                    <a href="detail.php?id=<?= $row['id'] ?>" class="product-card" style="text-decoration: none; color: inherit;">
                        <div class="card-badges">
                            <span class="badge-discount">Giảm <?= $discount_percent ?>%</span>
                        </div>
                        <div class="product-image">
                            <img src="<?= htmlspecialchars($img_src) ?>" alt="<?= htmlspecialchars($row['name']) ?>">
                        </div>
                        <h3 class="product-name"><?= htmlspecialchars($row['name']) ?></h3>
                        <div class="product-price">
                            <span class="price-current"><?= number_format($current_price, 0, ',', '.') ?>đ</span>
                            <span class="price-old"><?= number_format($old_price, 0, ',', '.') ?>đ</span>
                        </div>
                        <div class="product-shipping">
                            <i class="fa-solid fa-truck-fast"></i> Giao siêu tốc 2h
                        </div>
                    </a>
                <?php endwhile; ?>
            </div>

            <?php if ($total_pages > 1): ?>
                <div class="pagination">
                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                        <a href="?keyword=<?= urlencode($keyword) ?>&sort=<?= $sort ?>&page=<?= $i ?>" 
                           class="page-link <?= ($i == $page) ? 'active' : '' ?>">
                           <?= $i ?>
                        </a>
                    <?php endfor; ?>
                </div>
            <?php endif; ?>

        <?php else: ?>
            <div class="empty-search">
                <i class="fa-solid fa-face-frown-open"></i>
                <h3 style="color: #555;">Rất tiếc, chúng tôi không tìm thấy sản phẩm nào!</h3>
                <p style="color: #777; margin-bottom: 20px;">Vui lòng thử lại với từ khóa khác (Ví dụ: iPhone, Samsung, Sạc...)</p>
                <a href="index.php" class="btn-checkout" style="padding: 12px 30px; background: #d70018; color: #fff; text-decoration: none; border-radius: 5px; font-weight: bold; display: inline-block;">Quay về Trang Chủ</a>
            </div>
        <?php endif; ?>
    </main>

    <?php include "includes/footer.php"; ?>
</body>
</html>
<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require_once("includes/connect_db.php");

$keyword = "";
$sort = "";
$result = null;
$total_records = 0;
$total_pages = 0;

// THIẾT LẬP SỐ LƯỢNG SẢN PHẨM TRÊN 1 TRANG
$limit = 24; 
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
    
    // 2. ĐẾM TỔNG SỐ SẢN PHẨM
    $sql_count = "SELECT COUNT(DISTINCT p.id) as total 
                  FROM products p 
                  LEFT JOIN product_variants pv ON p.id = pv.product_id 
                  WHERE p.name LIKE '%$safe_keyword%'";
    $res_count = mysqli_query($conn, $sql_count);
    $row_count = mysqli_fetch_assoc($res_count);
    $total_records = $row_count['total'];
    $total_pages = ceil($total_records / $limit);

    // 3. TRUY VẤN DỮ LIỆU TÌM KIẾM
    $sql = "SELECT p.*, MIN(pv.price) as min_price 
            FROM products p 
            LEFT JOIN product_variants pv ON p.id = pv.product_id 
            WHERE p.name LIKE '%$safe_keyword%' 
            GROUP BY p.id 
            $order_by 
            LIMIT $limit OFFSET $offset";
    $result = mysqli_query($conn, $sql);

    // ========================================================
    // TUYỆT CHIÊU: NẾU TÌM KHÔNG RA, LẤY 4 SẢN PHẨM NGẪU NHIÊN
    // ========================================================
    if ($total_records == 0) {
        $sql_suggest = "SELECT p.*, MIN(pv.price) as min_price 
                        FROM products p LEFT JOIN product_variants pv ON p.id = pv.product_id 
                        GROUP BY p.id ORDER BY RAND() LIMIT 4";
        $res_suggest = mysqli_query($conn, $sql_suggest);
    }

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
    <link rel="stylesheet" href="assets/css/style_search.css">
</head>
<body>
    <?php include "includes/header.php"; include "includes/nav.php"; ?>

    <main class="main-content search-container">
        
        <div class="breadcrumb">
            <a href="index.php"><i class="fa-solid fa-house-chimney"></i> Trang chủ</a> 
            <span style="color: #ccc; margin: 0 8px;">/</span> 
            <span style="color: #555; font-weight: 600;">Tìm kiếm: "<?= htmlspecialchars($keyword) ?>"</span>
        </div>

        <img src="assets/image/samsung-galaxy-slide.webp" alt="Khuyến mãi" class="search-banner">

        <div>
            <h2 class="search-title"><i class="fa-solid fa-magnifying-glass" style="color: #ccc;"></i> Kết quả tìm kiếm cho: <span class="search-keyword">"<?= htmlspecialchars($keyword) ?>"</span></h2>
            
            <div class="trending-box">
                <span style="color: #888; font-weight: 600;"><i class="fa-solid fa-fire" style="color: #ff9800;"></i> Tìm kiếm phổ biến:</span>
                <a href="search.php?keyword=iPhone+16" class="trending-tag">iPhone 16</a>
                <a href="search.php?keyword=Samsung" class="trending-tag">Samsung Galaxy</a>
                <a href="search.php?keyword=Oppo" class="trending-tag">Oppo Reno</a>
                <a href="search.php?keyword=Sạc" class="trending-tag">Cáp sạc</a>
            </div>
        </div>

        <?php if ($total_records > 0): ?>
            
            <div class="filter-bar">
                <div style='color: #555; font-size: 15px;'>
                    <i class="fa-solid fa-check-double" style="color: #10b981;"></i> Tìm thấy <b><?= $total_records ?></b> sản phẩm phù hợp.
                </div>
                
                <form action="search.php" method="GET" style="display: flex; align-items: center; gap: 10px;">
                    <input type="hidden" name="keyword" value="<?= htmlspecialchars($keyword) ?>">
                    <label for="sort" style="font-weight: 600; color: #444; font-size: 14px;"><i class="fa-solid fa-arrow-down-a-z"></i> Sắp xếp:</label>
                    <select name="sort" id="sort" onchange="this.form.submit()" style="padding: 8px 12px; border: 1px solid #ddd; border-radius: 6px; outline: none; cursor: pointer; font-size: 14px; background: #f8f9fa;">
                        <option value="">-- Mặc định --</option>
                        <option value="asc" <?= ($sort == 'asc') ? 'selected' : '' ?>>Giá: Thấp đến Cao</option>
                        <option value="desc" <?= ($sort == 'desc') ? 'selected' : '' ?>>Giá: Cao xuống Thấp</option>
                    </select>
                </form>
            </div>

            <div class="product-grid">
                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                    <?php include("includes/product_card.php") ?>
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
                <p style="color: #777; margin-bottom: 20px;">Vui lòng kiểm tra lại lỗi chính tả hoặc thử các từ khóa phổ biến ở trên.</p>
                <a href="index.php" style="padding: 12px 30px; background: #d70018; color: #fff; text-decoration: none; border-radius: 8px; font-weight: bold; display: inline-block; box-shadow: 0 4px 10px rgba(215,0,24,0.2);">Quay về Trang Chủ</a>
            </div>

            <?php if (isset($res_suggest) && mysqli_num_rows($res_suggest) > 0): ?>
                <div style="margin-top: 40px; margin-bottom: 15px; display: flex; align-items: center; gap: 10px;">
                    <h3 style="font-size: 20px; color: #333; margin: 0; text-transform: uppercase;">✨ Gợi ý cho bạn</h3>
                </div>
                <div class="product-grid" style="margin-bottom: 40px;">
                    <?php while ($row = mysqli_fetch_assoc($res_suggest)): ?>
                        <?php include("includes/product_card.php") ?>
                    <?php endwhile; ?>
                </div>
            <?php endif; ?>

        <?php endif; ?>
    </main>

    <?php include "includes/footer.php"; ?>
</body>
</html>
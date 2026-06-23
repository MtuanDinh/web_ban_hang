<?php
require_once("includes/connect_db.php");

// 1. Kiểm tra xem có ID sản phẩm trên URL không
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$product_id = (int)$_GET['id'];

// 2. Lấy thông tin Sản phẩm gốc
$sql_product = "SELECT * FROM products WHERE id = $product_id";
$res_product = mysqli_query($conn, $sql_product);

if (mysqli_num_rows($res_product) == 0) {
    echo "<h2 style='text-align:center; margin-top:50px;'>Sản phẩm không tồn tại hoặc đã bị xóa!</h2>";
    exit();
}
$product = mysqli_fetch_assoc($res_product);

// 3. Lấy tất cả các Phiên bản (Variants) của sản phẩm này
$sql_variants = "SELECT * FROM product_variants WHERE product_id = $product_id";
$res_variants = mysqli_query($conn, $sql_variants);

$variants = [];
while ($row = mysqli_fetch_assoc($res_variants)) {
    $variants[] = $row;
}

// 4. Giải mã chuỗi JSON Cấu hình kỹ thuật
// Thêm tham số 'true' để biến JSON object thành Mảng (Array) trong PHP
$specs = json_decode($product['description'], true);

// 5. Gom toàn bộ hình ảnh (Ảnh bìa + Gallery) vào chung 1 mảng
$all_images = [];

// Đưa ảnh bìa vào đầu mảng
if (!empty($product['image'])) {
    $all_images[] = $product['image'];
}

// Giải mã JSON của cột gallery và gộp vào mảng nếu có ảnh phụ
if (!empty($product['gallery'])) {
    $gallery_arr = json_decode($product['gallery'], true);
    if (is_array($gallery_arr)) {
        $all_images = array_merge($all_images, $gallery_arr);
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
                <?php 
                // Lấy ảnh đầu tiên làm ảnh chính mặc định
                $first_img = !empty($all_images) ? 'assets/uploads/' . $all_images[0] : 'https://via.placeholder.com/400x400?text=No+Image';
                ?>
                
                <div class="main-image-box">
                    <img id="main-img" src="<?= htmlspecialchars($first_img) ?>" alt="<?= htmlspecialchars($product['name']) ?>">
                </div>

                <?php if (count($all_images) > 1): ?>
                <div class="thumbnail-bar">
                    <?php foreach ($all_images as $index => $img_name): 
                        $thumb_src = 'assets/uploads/' . htmlspecialchars($img_name);
                        // Nút ảnh đầu tiên sẽ sáng lên (active) mặc định
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
                <h1><?= htmlspecialchars($product['name']) ?></h1>
                
                <div class="price-box">
                    <span class="current-price" id="display_price">Đang cập nhật giá...</span>
                </div>

                <form action="add_to_cart.php" method="POST">
                    <div class="variant-group">
                        <label>Chọn phiên bản & Màu sắc:</label>
                        <select name="variant_id" id="variant_select" class="variant-select" onchange="updatePrice()" required>
                            <?php if (!empty($variants)): ?>
                                <?php foreach ($variants as $index => $v): ?>
                                    <option value="<?= $v['id'] ?>" data-price="<?= $v['price'] ?>">
                                        <?= htmlspecialchars($v['color']) ?> - <?= htmlspecialchars($v['version']) ?> 
                                        (Kho: <?= $v['stock'] ?>)
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
                        <button type="submit" name="btn_add_cart" class="btn-add-cart">
                            <i class="fa-solid fa-cart-plus"></i> Thêm Vào Giỏ Hàng
                        </button>
                    <?php else: ?>
                        <button type="button" class="btn-add-cart" style="background: #ccc; cursor: not-allowed;" disabled>
                            Tạm Hết Hàng
                        </button>
                    <?php endif; ?>
                </form>
            </div>
        </div>

        <div class="specs-container">
            <div class="specs-box">
                <h3>⚙️ Thông Số Kỹ Thuật</h3>
                <table class="specs-table">
                    <tbody>
                        <?php 
                        // Kiểm tra xem mảng $specs có tồn tại và có dữ liệu không
                        if (is_array($specs) && !empty($specs)) {
                            // Từ điển dịch thuật Key (từ tiếng Anh sang tiếng Việt cho đẹp)
                            $translate = [
                                'os' => 'Hệ điều hành',
                                'cpu' => 'Vi xử lý (CPU)',
                                'ram' => 'Bộ nhớ RAM',
                                'storage' => 'Bộ nhớ trong (ROM)',
                                'battery' => 'Dung lượng pin',
                                'screen' => 'Màn hình',
                                'camera' => 'Camera'
                            ];

                            // Vòng lặp in ra các thông số
                            foreach ($specs as $key => $value) {
                                // Nếu có trong từ điển thì dùng từ điển, không thì in hoa chữ cái đầu của Key
                                $display_key = isset($translate[$key]) ? $translate[$key] : ucfirst($key);
                                echo "<tr>";
                                echo "<td>" . htmlspecialchars($display_key) . "</td>";
                                echo "<td>" . htmlspecialchars($value) . "</td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='2'>Chưa có thông số kỹ thuật chi tiết cho sản phẩm này.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <?php include "includes/footer.php"; ?>

    <script src="assets/js/detail.js"></script>
</body>
</html>
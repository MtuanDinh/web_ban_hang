<?php
require_once("includes/connect_db.php");

// 1. XỬ LÝ CÁC THAO TÁC CẬP NHẬT/XÓA TRONG GIỎ HÀNG
if (isset($_GET['action']) && $_GET['action'] == 'remove' && isset($_GET['id'])) {
    $remove_id = (int)$_GET['id'];
    if (isset($_SESSION['cart'][$remove_id])) {
        unset($_SESSION['cart'][$remove_id]);
    }
    header("Location: cart.php");
    exit();
}

if (isset($_POST['btn_update_cart']) && isset($_POST['qty'])) {
    foreach ($_POST['qty'] as $variant_id => $new_qty) {
        $new_qty = (int)$new_qty;
        if ($new_qty <= 0) unset($_SESSION['cart'][$variant_id]); 
        else $_SESSION['cart'][$variant_id] = $new_qty;
    }
    header("Location: cart.php");
    exit();
}

// 2. ĐỌC COOKIE TỪ JAVASCRIPT ĐỂ XÁC ĐỊNH % GIẢM GIÁ
$discount_rate = isset($_COOKIE['user_discount']) ? (int)$_COOKIE['user_discount'] : 0;

// 3. LẤY DỮ LIỆU ĐỂ HIỂN THỊ
$cart_items = [];
$grand_total = 0;

if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
    $variant_ids = array_keys($_SESSION['cart']);
    $id_list = implode(',', $variant_ids);

    $sql = "SELECT pv.id as variant_id, pv.color, pv.version, pv.price, pv.stock, 
                   p.id as product_id, p.name, p.image 
            FROM product_variants pv 
            INNER JOIN products p ON pv.product_id = p.id 
            WHERE pv.id IN ($id_list)";
            
    $result = mysqli_query($conn, $sql);

    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $v_id = $row['variant_id'];
            $qty_in_cart = $_SESSION['cart'][$v_id];
            
            // --- TÍNH TOÁN GIÁ SAU KHI ÁP DỤNG MÃ KHU VỰC ---
            $base_price = $row['price'];
            $final_price = $base_price;
            
            if ($discount_rate > 0) {
                $final_price = $base_price - ($base_price * $discount_rate / 100);
            }
            
            $subtotal = $qty_in_cart * $final_price;
            $grand_total += $subtotal;
            
            // Lưu dữ liệu vào mảng
            $row['cart_qty'] = $qty_in_cart;
            $row['base_price'] = $base_price;
            $row['final_price'] = $final_price;
            $row['subtotal'] = $subtotal;
            
            $cart_items[] = $row;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Giỏ hàng của bạn</title>
    <script src="https://kit.fontawesome.com/da1a483940.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="assets/css/style_client.css">
    <link rel="stylesheet" href="assets/css/style_cart.css">
    
</head>
<body>
    <?php include "includes/header.php"; include "includes/nav.php"; ?>

    <main class="main-content">
        <div class="cart-container">
            <h2 class="cart-title">🛒 Giỏ Hàng Của Bạn</h2>

            <?php if (isset($_SESSION['flash_msg'])): ?>
                <div style="background: #d1e7dd; color: #0f5132; padding: 15px; border-radius: 5px; margin-bottom: 20px; text-align: center; font-weight: bold;">
                    <?= $_SESSION['flash_msg'] ?>
                    <?php unset($_SESSION['flash_msg']); ?>
                </div>
            <?php endif; ?>

            <?php if (empty($cart_items)): ?>
                <div class="empty-cart">
                    <i class="fa-solid fa-cart-shopping"></i>
                    <h3 style="color: #555;">Giỏ hàng của bạn đang trống!</h3>
                    <p style="color: #777; margin-bottom: 20px;">Hãy quay lại trang chủ để chọn cho mình những sản phẩm ưng ý nhé.</p>
                    <a href="index.php" class="btn-checkout" style="display: inline-block;">Tiếp Tục Mua Sắm</a>
                </div>
            <?php else: ?>
                <form action="" method="post">
                    <table class="cart-table">
                        <thead>
                            <tr>
                                <th>Sản phẩm</th>
                                <th>Đơn giá</th>
                                <th>Số lượng</th>
                                <th>Thành tiền</th>
                                <th>Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($cart_items as $item): ?>
                                <tr>
                                    <td>
                                        <div class="cart-item-info">
                                            <?php $img_src = !empty($item['image']) ? 'assets/uploads/' . $item['image'] : 'https://via.placeholder.com/100'; ?>
                                            <img src="<?= htmlspecialchars($img_src) ?>" alt="img">
                                            <div>
                                                <a href="detail.php?id=<?= $item['product_id'] ?>" class="cart-item-name">
                                                    <?= htmlspecialchars($item['name']) ?>
                                                </a>
                                                <div class="cart-item-variant">
                                                    Màu sắc: <?= htmlspecialchars($item['color']) ?> <br>
                                                    Tùy chọn: <?= htmlspecialchars($item['version']) ?>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    
                                    <td style="font-weight: 600;">
                                        <?php if ($discount_rate > 0): ?>
                                            <span style="text-decoration: line-through; color: #999; font-size: 13px; font-weight: normal;"><?= number_format($item['base_price'], 0, ',', '.') ?> đ</span><br>
                                            <span style="color: #d70018;"><?= number_format($item['final_price'], 0, ',', '.') ?> đ</span>
                                        <?php else: ?>
                                            <?= number_format($item['final_price'], 0, ',', '.') ?> đ
                                        <?php endif; ?>
                                    </td>
                                    
                                    <td>
                                        <input type="number" name="qty[<?= $item['variant_id'] ?>]" value="<?= $item['cart_qty'] ?>" min="1" max="<?= $item['stock'] ?>" class="qty-input">
                                    </td>
                                    
                                    <td style="color: #d70018; font-weight: bold; font-size: 16px;">
                                        <?= number_format($item['subtotal'], 0, ',', '.') ?> đ
                                    </td>
                                    
                                    <td>
                                        <a href="cart.php?action=remove&id=<?= $item['variant_id'] ?>" class="btn-remove" onclick="return confirm('Bạn có chắc muốn xóa sản phẩm này khỏi giỏ hàng?');">
                                            <i class="fa-solid fa-trash-can"></i> Xóa
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>

                    <div class="cart-actions">
                        <a href="index.php" class="btn-outline">← Tiếp tục mua sắm</a>
                        <button type="submit" name="btn_update_cart" class="btn-outline" style="background: #f8f9fa; border-color: #ccc;">
                            <i class="fa-solid fa-rotate"></i> Cập nhật số lượng
                        </button>
                    </div>
                </form>

                <div class="cart-summary">
                    <?php if ($discount_rate > 0): ?>
                        <div class="discount-badge">
                            <i class="fa-solid fa-tags"></i> Đã áp dụng giảm giá <?= $discount_rate ?>% theo khu vực nhận hàng!
                        </div>
                    <?php endif; ?>
                    
                    <h3>Tổng thanh toán</h3>
                    <div class="total-price"><?= number_format($grand_total, 0, ',', '.') ?> đ</div>
                    <p style="color: #777; font-size: 14px; margin-top: 10px;">(Đã bao gồm VAT nếu có)</p>
                    
                    <div style="margin-top: 20px;">
                        <a href="checkout.php" class="btn-checkout">Tiến Hành Đặt Hàng <i class="fa-solid fa-arrow-right"></i></a>
                    </div>
                </div>
            <?php endif; ?>

        </div>
    </main>

    <?php include "includes/footer.php"; ?>
</body>
</html>
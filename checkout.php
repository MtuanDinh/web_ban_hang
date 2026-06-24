<?php
require_once("includes/connect_db.php");

if (!isset($_SESSION['user_client'])) {
    $_SESSION['flash_msg'] = "Vui lòng đăng nhập để tiến hành thanh toán!";
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_client']['id'];
$discount_rate = isset($_COOKIE['user_discount']) ? (int)$_COOKIE['user_discount'] : 0;
$cart_items = [];
$grand_total = 0;

// Truy vấn Giỏ hàng trực tiếp từ CSDL
$sql_cart = "SELECT c.quantity as cart_qty, pv.id as variant_id, pv.color, pv.version, pv.price, pv.stock, 
                    p.id as product_id, p.name, p.image 
             FROM cart c 
             JOIN product_variants pv ON c.variant_id = pv.id 
             JOIN products p ON pv.product_id = p.id 
             WHERE c.user_id = $user_id";
$result_cart = mysqli_query($conn, $sql_cart);

if ($result_cart && mysqli_num_rows($result_cart) > 0) {
    while ($row = mysqli_fetch_assoc($result_cart)) {
        $base_price = $row['price'];
        $final_price = $base_price;
        if ($discount_rate > 0) {
            $final_price = $base_price - ($base_price * $discount_rate / 100);
        }
        
        $row['base_price'] = $base_price;
        $row['final_price'] = $final_price;
        $row['subtotal'] = $row['cart_qty'] * $final_price;
        
        $grand_total += $row['subtotal'];
        $cart_items[] = $row;
    }
} else {
    header("Location: cart.php");
    exit();
}

$error = "";
$show_modal = false;
$success_message = ""; 

if (isset($_POST['btn_place_order'])) {
    $customer_name = trim($_POST['customer_name']);
    $customer_phone = trim($_POST['customer_phone']);
    $shipping_address = trim($_POST['shipping_address']);

    if(empty($customer_name) || empty($customer_phone) || empty($shipping_address)) {
        $error = "Vui lòng điền đầy đủ thông tin giao hàng!";
    } else {
        $sql_order = "INSERT INTO orders (user_id, shipping_address, customer_phone, customer_name, total, status) 
                      VALUES ($user_id, '$shipping_address', '$customer_phone', '$customer_name', $grand_total, 0)";
        
        if (mysqli_query($conn, $sql_order)) {
            $new_order_id = mysqli_insert_id($conn);

            foreach ($cart_items as $item) {
                $p_id = $item['product_id']; 
                $qty = $item['cart_qty'];
                $price_to_save = $item['final_price'];     
                $variant_string = mysqli_real_escape_string($conn, $item['color'] . ' - ' . $item['version']);
                
                $sql_detail = "INSERT INTO order_details (order_id, product_id, variant_name, quantity, unit_price) 
                               VALUES ($new_order_id, $p_id, '$variant_string', $qty, $price_to_save)";
                mysqli_query($conn, $sql_detail);
                
                $v_id = $item['variant_id'];
                $new_stock = $item['stock'] - $qty;
                if ($new_stock < 0) $new_stock = 0;
                mysqli_query($conn, "UPDATE product_variants SET stock = $new_stock WHERE id = $v_id");
            }

            // XÓA SẠCH GIỎ HÀNG TRONG DATABASE SAU KHI MUA THÀNH CÔNG
            mysqli_query($conn, "DELETE FROM cart WHERE user_id = $user_id");
            
            $show_modal = true;
            $success_message = "Đơn hàng <b>#$new_order_id</b> của bạn đã được ghi nhận. Cảm ơn bạn đã mua sắm!";
            
        } else {
            $error = "Hệ thống đang bận, vui lòng thử lại sau!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thanh Toán - PhoneStore</title>
    <script src="https://kit.fontawesome.com/da1a483940.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="assets/css/style_client.css">
    <link rel="stylesheet" href="assets/css/style_checkout.css">
</head>
<body <?= $show_modal ? 'style="overflow: hidden;"' : '' ?>>
    <?php include "includes/header.php"; include "includes/nav.php"; ?>

    <main class="main-content checkout-page-wrapper">
        <form action="" method="post" class="checkout-container">
            
            <div class="checkout-left">
                <?php if($error != "") echo "<div class='msg-error' style='background: #ffe8ea; color: #d70018; padding: 15px; border-radius: 8px; margin-bottom: 20px; font-weight: 600;'><i class='fa-solid fa-triangle-exclamation'></i> $error</div>"; ?>
                
                <div class="checkout-card">
                    <h2 class="checkout-card-title"><i class="fa-solid fa-address-card"></i> 1. Thông tin người nhận</h2>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Họ và Tên</label>
                            <input type="text" name="customer_name" required value="<?= htmlspecialchars($_SESSION['user_client']['name']) ?>" placeholder="Nhập họ tên">
                        </div>
                        <div class="form-group">
                            <label>Số điện thoại</label>
                            <input type="text" name="customer_phone" required placeholder="Nhập số điện thoại liên hệ">
                        </div>
                    </div>
                </div>

                <div class="checkout-card">
                    <h2 class="checkout-card-title"><i class="fa-solid fa-map-location-dot"></i> 2. Địa chỉ nhận hàng</h2>
                    
                    <?php if ($discount_rate > 0): ?>
                        <div class="discount-alert">
                            <i class="fa-solid fa-tags"></i> Khu vực bạn chọn đang được áp dụng mã giảm giá <?= $discount_rate ?>%!
                        </div>
                    <?php endif; ?>

                    <div class="form-group" style="margin-bottom: 0;">
                        <textarea name="shipping_address" rows="3" required placeholder="Ghi rõ Số nhà, Tên đường, Phường/Xã, Quận/Huyện, Tỉnh/Thành phố..."></textarea>
                    </div>
                </div>

                <div class="checkout-card">
                    <h2 class="checkout-card-title"><i class="fa-solid fa-wallet"></i> 3. Phương thức thanh toán</h2>
                    <label class="payment-method-card">
                        <input type="radio" name="payment_method" checked style="width: 18px; height: 18px; margin: 0;">
                        <img src="https://cdn-icons-png.flaticon.com/128/2897/2897785.png" alt="COD">
                        <div>
                            <strong style="display: block; color: #333; font-size: 15px; margin-bottom: 3px;">Thanh toán khi nhận hàng (COD)</strong>
                            <span style="color: #777; font-size: 12px;">Thanh toán bằng tiền mặt khi shipper giao hàng đến tận nhà.</span>
                        </div>
                    </label>
                </div>
            </div>

            <div class="checkout-right order-summary-sticky">
                <div class="checkout-card" style="margin-bottom: 0;">
                    <h2 class="checkout-card-title"><i class="fa-solid fa-bag-shopping"></i> Tổng kết đơn hàng</h2>
                    
                    <div style="max-height: 350px; overflow-y: auto; padding-right: 5px; margin-bottom: 15px;">
                        <?php foreach ($cart_items as $item): ?>
                            <div class="summary-item">
                                <?php $img_src = !empty($item['image']) ? 'assets/uploads/' . $item['image'] : 'https://via.placeholder.com/60'; ?>
                                <img src="<?= htmlspecialchars($img_src) ?>" alt="img">
                                <div style="flex: 1;">
                                    <div class="summary-name"><?= htmlspecialchars($item['name']) ?></div>
                                    <div class="summary-meta">
                                        <?= htmlspecialchars($item['color']) ?> - <?= htmlspecialchars($item['version']) ?>
                                    </div>
                                    <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 5px;">
                                        <span style="font-size: 12px; color: #777;">SL: x<?= $item['cart_qty'] ?></span>
                                        <span class="summary-price"><?= number_format($item['final_price'], 0, ',', '.') ?> đ</span>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <div style="background: #fafafa; padding: 15px; border-radius: 8px; margin-top: 15px;">
                        <div class="price-row">
                            <span>Tạm tính:</span>
                            <span style="font-weight: 500;"><?= number_format($grand_total, 0, ',', '.') ?> đ</span>
                        </div>
                        
                        <?php if ($discount_rate > 0): ?>
                            <div class="price-row" style="color: #10b981;">
                                <span>Giảm giá khu vực:</span>
                                <span>- <?= $discount_rate ?>%</span>
                            </div>
                        <?php endif; ?>

                        <div class="price-row">
                            <span>Phí vận chuyển:</span>
                            <span style="color: #10b981;">Miễn phí</span>
                        </div>
                        
                        <div class="total-row">
                            <span>TỔNG CỘNG:</span>
                            <span class="total-amount"><?= number_format($grand_total, 0, ',', '.') ?> đ</span>
                        </div>
                    </div>

                    <button type="submit" name="btn_place_order" class="btn-place-order">Xác Nhận Đặt Hàng</button>
                    <p style="text-align: center; font-size: 12px; color: #888; margin-top: 15px;">Nhấn "Xác nhận đặt hàng" đồng nghĩa với việc bạn đồng ý tuân theo Điều khoản PhoneStore.</p>
                </div>
            </div>
            
        </form>
    </main>

    <?php include "includes/footer.php"; ?>

    <?php if ($show_modal): ?>
    <div class="modal-overlay">
        <div class="modal-content">
            <i class="fa-solid fa-circle-check modal-icon"></i>
            <h2>Đặt Hàng Thành Công!</h2>
            <p><?= $success_message ?></p>
            <a href="my_orders.php" class="btn-modal-ok">Kiểm Tra Đơn Hàng</a>
        </div>
    </div>
    <?php endif; ?>

</body>
</html>
<?php
require_once("includes/connect_db.php");

// 1. Kiểm tra Đăng nhập
if (!isset($_SESSION['user_client'])) {
    $_SESSION['flash_msg'] = "Vui lòng đăng nhập để tiến hành thanh toán!";
    header("Location: login.php");
    exit();
}

// 2. Kiểm tra Giỏ hàng
if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    header("Location: cart.php");
    exit();
}

$user_id = $_SESSION['user_client']['id'];
$cart_items = [];
$grand_total = 0;

// 3. Truy vấn lấy dữ liệu Giỏ hàng
$variant_ids = array_keys($_SESSION['cart']);
$id_list = implode(',', $variant_ids);

$sql_cart = "SELECT pv.id as variant_id, pv.color, pv.version, pv.price, pv.stock, 
                    p.id as product_id, p.name, p.image 
             FROM product_variants pv 
             INNER JOIN products p ON pv.product_id = p.id 
             WHERE pv.id IN ($id_list)";
$result_cart = mysqli_query($conn, $sql_cart);

if ($result_cart && mysqli_num_rows($result_cart) > 0) {
    while ($row = mysqli_fetch_assoc($result_cart)) {
        $qty = $_SESSION['cart'][$row['variant_id']];
        $row['cart_qty'] = $qty;
        $row['subtotal'] = $qty * $row['price'];
        $grand_total += $row['subtotal'];
        $cart_items[] = $row;
    }
} else {
    header("Location: cart.php");
    exit();
}

// 4. XỬ LÝ KHI KHÁCH HÀNG BẤM "ĐẶT HÀNG"
$error = "";
$show_modal = false; // Biến cờ quyết định có hiện Modal hay không
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
                $price = $item['price'];     
                
                $variant_string = mysqli_real_escape_string($conn, $item['color'] . ' - ' . $item['version']);
                
                $sql_detail = "INSERT INTO order_details (order_id, product_id, variant_name, quantity, unit_price) 
                               VALUES ($new_order_id, $p_id, '$variant_string', $qty, $price)";
                mysqli_query($conn, $sql_detail);
                
                $v_id = $item['variant_id'];
                $new_stock = $item['stock'] - $qty;
                if ($new_stock < 0) $new_stock = 0;
                mysqli_query($conn, "UPDATE product_variants SET stock = $new_stock WHERE id = $v_id");
            }

            // XÓA sạch giỏ hàng
            unset($_SESSION['cart']);
            
            // KÍCH HOẠT MODAL thay vì dùng header("Location...")
            $show_modal = true;
            $success_message = "Đơn hàng <b>#$new_order_id</b> của bạn đã được ghi nhận và đang chờ cửa hàng xử lý. Cảm ơn bạn đã mua sắm!";
            
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

    <main class="main-content">
        <form action="" method="post" class="checkout-container">
            
            <div class="checkout-box">
                <h2 class="checkout-title">📍 Thông Tin Nhận Hàng</h2>
                <?php if($error != "") echo "<div class='msg-error'>$error</div>"; ?>
                <div class="form-group">
                    <label>Họ và Tên người nhận</label>
                    <input type="text" name="customer_name" required value="<?= htmlspecialchars($_SESSION['user_client']['name']) ?>">
                </div>
                <div class="form-group">
                    <label>Số điện thoại liên hệ</label>
                    <input type="text" name="customer_phone" required>
                </div>
                <div class="form-group">
                    <label>Địa chỉ giao hàng chi tiết</label>
                    <textarea name="shipping_address" rows="4" required></textarea>
                </div>
                <div class="form-group">
                    <label>Phương thức thanh toán</label>
                    <div style="padding: 15px; border: 1px solid #d70018; border-radius: 6px; background: #fff9fa; color: #d70018; font-weight: bold;">
                        <i class="fa-solid fa-money-bill-wave"></i> Thanh toán tiền mặt khi nhận hàng (COD)
                    </div>
                </div>
            </div>

            <div class="checkout-box" style="background: #f8f9fa;">
                <h2 class="checkout-title">🧾 Đơn Hàng Của Bạn</h2>
                <div style="max-height: 400px; overflow-y: auto; padding-right: 10px;">
                    <?php foreach ($cart_items as $item): ?>
                        <div class="summary-item">
                            <?php $img_src = !empty($item['image']) ? 'assets/uploads/' . $item['image'] : 'https://via.placeholder.com/60'; ?>
                            <img src="<?= htmlspecialchars($img_src) ?>" alt="img">
                            <div style="flex: 1;">
                                <div class="summary-name"><?= htmlspecialchars($item['name']) ?></div>
                                <div class="summary-meta">
                                    Màu: <?= htmlspecialchars($item['color']) ?> | Bản: <?= htmlspecialchars($item['version']) ?><br>
                                    SL: x<?= $item['cart_qty'] ?>
                                </div>
                                <div class="summary-price"><?= number_format($item['price'], 0, ',', '.') ?> đ</div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div style="border-top: 2px solid #e0e0e0; margin-top: 15px; padding-top: 15px;">
                    <div style="display: flex; justify-content: space-between; margin-bottom: 10px; color: #555;">
                        <span>Tạm tính:</span>
                        <span><?= number_format($grand_total, 0, ',', '.') ?> đ</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; margin-bottom: 10px; color: #555;">
                        <span>Phí vận chuyển:</span>
                        <span>Miễn phí</span>
                    </div>
                    
                    <div class="total-row">
                        <span>Tổng cộng:</span>
                        <span class="total-amount"><?= number_format($grand_total, 0, ',', '.') ?> đ</span>
                    </div>
                </div>
                <button type="submit" name="btn_place_order" class="btn-place-order">Xác Nhận Đặt Hàng</button>
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
            
            <a href="cart.php" class="btn-modal-ok">Quay về Giỏ hàng</a>
        </div>
    </div>
    <?php endif; ?>

</body>
</html>
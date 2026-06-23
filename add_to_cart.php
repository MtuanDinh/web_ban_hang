<?php
// Bật tính năng Session để sử dụng bộ nhớ Giỏ hàng
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Kiểm tra xem khách hàng có bấm 1 trong 2 nút (Thêm giỏ / Mua ngay) hay không
if (isset($_POST['btn_add_cart']) || isset($_POST['btn_buy_now'])) {
    
    // Lấy ID phiên bản và số lượng người dùng vừa chọn
    $variant_id = (int)$_POST['variant_id'];
    $quantity = (int)$_POST['quantity'];

    // 1. Nếu hệ thống chưa có giỏ hàng, hãy tạo một chiếc giỏ trống
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    // 2. Logic thêm hàng vào giỏ (Cộng dồn nếu đã có, tạo mới nếu chưa có)
    if (isset($_SESSION['cart'][$variant_id])) {
        $_SESSION['cart'][$variant_id] += $quantity;
    } else {
        $_SESSION['cart'][$variant_id] = $quantity;
    }

    // 3. Xử lý điều hướng thông minh dựa vào nút bấm
    if (isset($_POST['btn_buy_now'])) {
        // Nếu bấm "Mua Ngay" -> Chuyển thẳng tới trang Thanh toán
        header("Location: checkout.php");
        exit();
    } else {
        // Nếu bấm "Thêm Vào Giỏ" -> Chuyển về trang Giỏ hàng kèm thông báo
        $_SESSION['flash_msg'] = "Đã thêm sản phẩm vào giỏ hàng thành công!";
        header("Location: cart.php");
        exit();
    }
    
} else {
    // Nếu có ai đó cố tình gõ thẳng đường dẫn add_to_cart.php lên URL thì đá về trang chủ
    header("Location: index.php");
    exit();
}
?>
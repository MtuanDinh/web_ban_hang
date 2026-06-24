<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require_once("includes/connect_db.php");

if (!isset($_SESSION['user_client'])) {
    $_SESSION['flash_msg'] = "Bạn cần đăng nhập để có thể thêm sản phẩm vào giỏ!";
    header("Location: login.php");
    exit();
}

if (isset($_POST['btn_add_cart']) || isset($_POST['btn_buy_now'])) {
    $user_id = $_SESSION['user_client']['id'];
    $variant_id = (int)$_POST['variant_id']; // Đã chuyển sang dùng variant_id
    $quantity = (int)$_POST['quantity'];

    // Kiểm tra xem sản phẩm này đã có trong giỏ hàng của user chưa
    $sql_check = "SELECT id, quantity FROM cart WHERE user_id = $user_id AND variant_id = $variant_id";
    $res_check = mysqli_query($conn, $sql_check);

    if ($res_check && mysqli_num_rows($res_check) > 0) {
        // Nếu đã có -> CỘNG DỒN số lượng
        $row = mysqli_fetch_assoc($res_check);
        $new_qty = $row['quantity'] + $quantity;
        $cart_id = $row['id'];
        mysqli_query($conn, "UPDATE cart SET quantity = $new_qty WHERE id = $cart_id");
    } else {
        // Nếu chưa có -> THÊM MỚI
        mysqli_query($conn, "INSERT INTO cart (user_id, variant_id, quantity) VALUES ($user_id, $variant_id, $quantity)");
    }

    if (isset($_POST['btn_buy_now'])) {
        header("Location: checkout.php");
        exit();
    } else {
        $_SESSION['flash_msg'] = "Đã thêm sản phẩm vào giỏ hàng thành công!";
        header("Location: cart.php");
        exit();
    }
} else {
    header("Location: index.php");
    exit();
}
?>
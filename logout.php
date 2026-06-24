<?php
// 1. Khởi động bộ nhớ Session để có thể can thiệp vào nó
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 2. Xóa toàn bộ các biến trong Session (Bao gồm user_client, cart, flash_msg...)
session_unset();

// 3. Phá hủy hoàn toàn phiên làm việc hiện tại trên máy chủ
session_destroy();

// 4. (Tùy chọn) Xóa luôn cả Cookie lưu % giảm giá khu vực để làm mới hoàn toàn
if (isset($_COOKIE['user_discount'])) {
    setcookie('user_discount', '', time() - 3600, '/');
}
if (isset($_COOKIE['user_location'])) {
    setcookie('user_location', '', time() - 3600, '/');
}

// Thêm khối lệnh này vào trong file logout.php của bạn
if (isset($_COOKIE['remember_user'])) {
    setcookie('remember_user', '', time() - 3600, '/');
}

// 5. Đưa người dùng về lại trang chủ sau khi đã dọn dẹp sạch sẽ
header("Location: index.php");
exit();
?>
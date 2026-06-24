<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require_once("includes/connect_db.php");

// Nếu đã đăng nhập thì không cho vào trang này
if (isset($_SESSION['user_client'])) {
    header("Location: index.php");
    exit();
}

$error = "";
$success = "";

// =========================================================
// BƯỚC 1: XỬ LÝ XÁC MINH EMAIL & SỐ ĐIỆN THOẠI
// =========================================================
if (isset($_POST['btn_verify'])) {
    $email = mysqli_real_escape_string($conn, trim($_POST['email']));
    $phone = mysqli_real_escape_string($conn, trim($_POST['phone']));

    // Tìm xem có User nào khớp CẢ 2 thông tin này không (Và tài khoản không bị khóa)
    $sql = "SELECT id FROM users WHERE email = '$email' AND phone = '$phone' AND role = 0 AND status != 0";
    $result = mysqli_query($conn, $sql);

    if ($result && mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);
        
        // Lưu ID người dùng vào Session tạm thời để làm bằng chứng cho Bước 2
        $_SESSION['reset_user_id'] = $user['id'];
        $success = "Xác minh danh tính thành công! Mời bạn nhập mật khẩu mới.";
    } else {
        $error = "Thông tin Email hoặc Số điện thoại không chính xác!";
    }
}

// =========================================================
// BƯỚC 2: XỬ LÝ ĐỔI MẬT KHẨU MỚI
// =========================================================
if (isset($_POST['btn_reset_password'])) {
    $new_password = $_POST['new_password'];
    $re_password = $_POST['re_password'];

    if (strlen($new_password) < 6) {
        $error = "Mật khẩu quá ngắn. Vui lòng đặt tối thiểu 6 ký tự!";
    } elseif ($new_password !== $re_password) {
        $error = "Mật khẩu xác nhận không khớp!";
    } else {
        // Băm mật khẩu mới
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $user_id_to_reset = (int)$_SESSION['reset_user_id'];

        // Cập nhật Database
        $sql_update = "UPDATE users SET password = '$hashed_password' WHERE id = $user_id_to_reset";
        
        if (mysqli_query($conn, $sql_update)) {
            // Đổi xong thì Xóa luôn Session tạm này đi để bảo mật
            unset($_SESSION['reset_user_id']);
            
            // Chuyển hướng về trang Đăng nhập kèm Flash Message chúc mừng
            $_SESSION['flash_msg'] = "🎉 Đổi mật khẩu thành công! Xin mời bạn đăng nhập bằng mật khẩu mới.";
            header("Location: login.php");
            exit();
        } else {
            $error = "Có lỗi hệ thống xảy ra, vui lòng thử lại sau!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quên mật khẩu - PhoneStore</title>
    <script src="https://kit.fontawesome.com/da1a483940.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="assets/css/style_client.css">
    <link rel="stylesheet" href="assets/css/style_register.css">
</head>
<body>
    <?php include "includes/header.php"; include "includes/nav.php"; ?>

    <main class="main-content">
        <div class="auth-container">
            <h2 class="auth-title">Khôi Phục Mật Khẩu</h2>
            
            <?php if($error != "") echo "<div class='msg msg-error' style='background: #ffe8ea; color: #d70018; border: 1px solid #f5c2c7; padding: 15px; border-radius: 5px; margin-bottom: 20px; text-align: center; font-weight: 500;'>$error</div>"; ?>
            <?php if($success != "") echo "<div class='msg msg-success' style='background: #d1e7dd; color: #0f5132; border: 1px solid #badbcc; padding: 15px; border-radius: 5px; margin-bottom: 20px; text-align: center; font-weight: 500;'>$success</div>"; ?>

            <?php if (!isset($_SESSION['reset_user_id'])): ?>
                
                <p style="text-align: center; color: #555; margin-bottom: 20px; font-size: 14px;">Vui lòng nhập Email và Số điện thoại bạn đã dùng để đăng ký tài khoản.</p>
                <form action="" method="post">
                    <div class="form-group">
                        <label>Địa chỉ Email</label>
                        <input type="email" name="email" required placeholder="Ví dụ: nguyenvan@gmail.com">
                    </div>
                    <div class="form-group">
                        <label>Số điện thoại</label>
                        <input type="tel" name="phone" required placeholder="Nhập số điện thoại bảo mật">
                    </div>
                    <button type="submit" name="btn_verify" class="btn-auth">Xác Minh Tài Khoản</button>
                </form>

            <?php else: ?>
                
                <p style="text-align: center; color: #555; margin-bottom: 20px; font-size: 14px;">Tài khoản hợp lệ. Vui lòng thiết lập lại mật khẩu của bạn.</p>
                <form action="" method="post">
                    <div class="form-group">
                        <label>Mật khẩu mới</label>
                        <div class="password-wrapper" style="position: relative;">
                            <input type="password" name="new_password" id="pwd" required minlength="6" placeholder="Tối thiểu 6 ký tự" style="width: 100%; padding-right: 40px;">
                            <i class="fa-regular fa-eye toggle-password" onclick="toggleVisibility('pwd', this)" style="position: absolute; right: 15px; top: 50%; transform: translateY(-50%); cursor: pointer; color: #777;"></i>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Xác nhận mật khẩu mới</label>
                        <div class="password-wrapper" style="position: relative;">
                            <input type="password" name="re_password" id="re_pwd" required placeholder="Nhập lại mật khẩu" style="width: 100%; padding-right: 40px;">
                            <i class="fa-regular fa-eye toggle-password" onclick="toggleVisibility('re_pwd', this)" style="position: absolute; right: 15px; top: 50%; transform: translateY(-50%); cursor: pointer; color: #777;"></i>
                        </div>
                        <div id="match-msg" style="font-size: 12px; margin-top: 6px; font-weight: 500;"></div>
                    </div>

                    <button type="submit" name="btn_reset_password" id="btn-submit" class="btn-auth" style="background: #28a745;">Cập Nhật Mật Khẩu</button>
                    
                    <div style="text-align: center; margin-top: 15px;">
                        <a href="logout.php" style="color: #888; font-size: 13px; text-decoration: underline;">Hủy thao tác</a>
                    </div>
                </form>

            <?php endif; ?>
            
            <?php if (!isset($_SESSION['reset_user_id'])): ?>
                <div class="auth-links" style="margin-top: 25px;">
                    <a href="login.php" style="color: #555; font-weight: 600;"><i class="fa-solid fa-arrow-left"></i> Quay lại đăng nhập</a>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <?php include "includes/footer.php"; ?>

    <script src="assets/js/forgot_pass.js"></script>
</body>
</html>
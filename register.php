<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require_once("includes/connect_db.php");

// Nếu đã đăng nhập rồi thì không cho vào trang đăng ký nữa
if (isset($_SESSION['user_client'])) {
    header("Location: index.php");
    exit();
}

$error = "";

if (isset($_POST['btn_register'])) {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password_raw = $_POST['password'];
    $re_password = $_POST['re_password'];
    $phone = trim($_POST['phone']);

    // 1. Kiểm tra độ dài và độ khớp của mật khẩu
    if (strlen($password_raw) < 6) {
        $error = "Mật khẩu quá ngắn. Vui lòng đặt tối thiểu 6 ký tự!";
    } elseif ($password_raw !== $re_password) {
        $error = "Mật khẩu xác nhận không khớp. Vui lòng nhập lại!";
    } else {
        // 2. Kiểm tra xem Email đã tồn tại trong hệ thống chưa
        $check_email = mysqli_query($conn, "SELECT id FROM users WHERE email = '$email'");
        
        if (mysqli_num_rows($check_email) > 0) {
            $error = "Email này đã được sử dụng. Vui lòng dùng email khác hoặc Đăng nhập!";
        } else {
            // 3. BĂM MẬT KHẨU (Bảo mật tuyệt đối)
            $hashed_password = password_hash($password_raw, PASSWORD_DEFAULT);

            // 4. Thêm vào CSDL
            $sql_insert = "INSERT INTO users (username, email, password, phone, role, status) 
                           VALUES ('$username', '$email', '$hashed_password', '$phone', 0, 1)";
            
            if (mysqli_query($conn, $sql_insert)) {
                // TUYỆT CHIÊU: Đăng ký xong đá thẳng sang trang Login kèm thông báo xanh
                $_SESSION['flash_msg'] = "🎉 Đăng ký tài khoản thành công! Xin mời bạn đăng nhập.";
                header("Location: login.php");
                exit();
            } else {
                $error = "Hệ thống đang bận, vui lòng thử lại sau!";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng ký tài khoản - PhoneStore</title>
    <script src="https://kit.fontawesome.com/da1a483940.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="assets/css/style_client.css">
    <link rel="stylesheet" href="assets/css/style_register.css">
</head>
<body>
    <?php include "includes/header.php"; include "includes/nav.php"; ?>

    <main class="main-content">
        <div class="auth-container">
            <h2 class="auth-title">Tạo Tài Khoản</h2>
            
            <?php if($error != "") echo "<div class='msg msg-error' style='background: #ffe8ea; color: #d70018; border: 1px solid #f5c2c7; padding: 15px; border-radius: 5px; margin-bottom: 20px; text-align: center; font-weight: 500;'>$error</div>"; ?>

            <form action="" method="post" id="registerForm">
                <div class="form-group">
                    <label>Họ và Tên</label>
                    <input type="text" name="username" required placeholder="Nhập tên hiển thị của bạn">
                </div>
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" required placeholder="Nhập địa chỉ email hợp lệ">
                </div>
                <div class="form-group">
                    <label>Số điện thoại</label>
                    <input type="tel" name="phone" required placeholder="Nhập số điện thoại liên hệ" pattern="[0-9]{9,11}" title="Vui lòng nhập số điện thoại hợp lệ (9-11 số)">
                </div>
                
                <div class="form-group">
                    <label>Mật khẩu</label>
                    <div class="password-wrapper" style="position: relative;">
                        <input type="password" name="password" id="pwd" required minlength="6" placeholder="Nhập mật khẩu (tối thiểu 6 ký tự)" style="width: 100%; padding-right: 40px;">
                        <i class="fa-regular fa-eye toggle-password" onclick="toggleVisibility('pwd', this)" style="position: absolute; right: 15px; top: 50%; transform: translateY(-50%); cursor: pointer; color: #777;"></i>
                    </div>
                </div>

                <div class="form-group">
                    <label>Xác nhận mật khẩu</label>
                    <div class="password-wrapper" style="position: relative;">
                        <input type="password" name="re_password" id="re_pwd" required placeholder="Nhập lại mật khẩu phía trên" style="width: 100%; padding-right: 40px;">
                        <i class="fa-regular fa-eye toggle-password" onclick="toggleVisibility('re_pwd', this)" style="position: absolute; right: 15px; top: 50%; transform: translateY(-50%); cursor: pointer; color: #777;"></i>
                    </div>
                    <div id="match-msg" style="font-size: 12px; margin-top: 6px; font-weight: 500;"></div>
                </div>

                <div class="form-group" style="display: flex; gap: 10px; align-items: flex-start; margin-bottom: 25px;">
                    <input type="checkbox" id="terms" required style="width: auto; margin-top: 4px; cursor: pointer;">
                    <label for="terms" style="margin: 0; font-size: 13px; font-weight: 400; color: #555; cursor: pointer; line-height: 1.5;">
                        Tôi đã đọc và đồng ý với <a href="#" style="color: #d70018; text-decoration: none; font-weight: bold;">Điều khoản sử dụng</a> và <a href="#" style="color: #d70018; text-decoration: none; font-weight: bold;">Chính sách bảo mật</a> của PhoneStore.
                    </label>
                </div>

                <button type="submit" name="btn_register" id="btn-submit" class="btn-auth">Đăng Ký Ngay</button>
            </form>
            
            <div class="auth-links">
                Đã có tài khoản? <a href="login.php" style="color: #2f80ed; font-weight: bold;">Đăng nhập tại đây</a>
            </div>
        </div>
    </main>

    <?php include "includes/footer.php"; ?>

    <script src="assets/js/register.js"></script>
</body>
</html>
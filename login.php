<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require_once("includes/connect_db.php");

// Nếu đã đăng nhập rồi thì đá về trang chủ[cite: 15]
if (isset($_SESSION['user_client'])) {
    header("Location: index.php");
    exit();
}

$error = "";

if (isset($_POST['btn_login'])) {
    $email = trim($_POST['email']);
    $password_raw = $_POST['password'];

    // 1. Tìm người dùng dựa trên Email[cite: 15]
    $sql = "SELECT id, username, email, password, status FROM users WHERE email = '$email' AND role = 0";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);

        // 2. Kiểm tra xem tài khoản có đang bị khóa không?[cite: 15]
        if ($user['status'] == 0) {
            $error = "Tài khoản của bạn đã bị khóa. Vui lòng liên hệ CSKH!";
        } else {
            // 3. Giải mã và so sánh mật khẩu[cite: 15]
            if (password_verify($password_raw, $user['password'])) {
                
                // Đăng nhập thành công -> Lưu thông tin vào Session[cite: 15]
                $_SESSION['user_client'] = [
                    'id' => $user['id'],
                    'name' => $user['username'],
                    'email' => $user['email']
                ];
                
                // ===================================================
                // XỬ LÝ LƯU ĐĂNG NHẬP BẰNG COOKIE (HẠN DÙNG 30 NGÀY)
                // ===================================================
                if (isset($_POST['remember'])) {
                    // Lưu ID của user vào Cookie (2592000 giây = 30 ngày)
                    setcookie('remember_user', $user['id'], time() + 2592000, "/");
                }
                
                header("Location: index.php");
                exit();
            } else {
                $error = "Mật khẩu không chính xác!";
            }
        }
    } else {
        $error = "Email không tồn tại trong hệ thống hoặc không hợp lệ!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập hệ thống</title>
    <script src="https://kit.fontawesome.com/da1a483940.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="assets/css/style_client.css">
    <link rel="stylesheet" href="assets/css/style_register.css">
</head>
<body>
    <?php include "includes/header.php"; include "includes/nav.php"; ?>

    <main class="main-content">
        <div class="auth-container">
            <h2 class="auth-title">Đăng Nhập</h2>
            
            <?php if (isset($_SESSION['flash_msg'])): ?>
                <div style="background: #fff3cd; color: #856404; border: 1px solid #ffeeba; padding: 15px; border-radius: 5px; margin-bottom: 20px; text-align: center; font-weight: 500;">
                    <i class="fa-solid fa-circle-exclamation"></i> <?= $_SESSION['flash_msg'] ?>
                </div>
                <?php unset($_SESSION['flash_msg']); ?>
            <?php endif; ?>

            <?php if($error != "") echo "<div class='msg msg-error' style='background: #ffe8ea; color: #d70018; border: 1px solid #f5c2c7; padding: 15px; border-radius: 5px; margin-bottom: 20px; text-align: center; font-weight: 500;'>$error</div>"; ?>

            <form action="" method="post">
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" required placeholder="Nhập địa chỉ email của bạn">
                </div>
                <div class="form-group">
                    <label>Mật khẩu</label>
                    <input type="password" name="password" required placeholder="Nhập mật khẩu">
                </div>

                <div class="form-group" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
                    <div style="display: flex; align-items: center; gap: 8px;">
                        <input type="checkbox" name="remember" id="remember" style="width: auto; margin: 0; cursor: pointer; width: 16px; height: 16px;">
                        <label for="remember" style="margin: 0; font-weight: 500; cursor: pointer; font-size: 14px; color: #555;">Ghi nhớ đăng nhập</label>
                    </div>
                    
                    <a href="forgot_password.php" style="color: #2f80ed; font-size: 14px; font-weight: 600; text-decoration: none; transition: 0.2s;" onmouseover="this.style.color='#d70018'" onmouseout="this.style.color='#2f80ed'">Quên mật khẩu?</a>
                </div>

                <button type="submit" name="btn_login" class="btn-auth">Đăng Nhập</button>
            </form>
            
            <div class="auth-links">
                Chưa có tài khoản? <a href="register.php">Đăng ký ngay</a>
            </div>
        </div>
    </main>

    <?php include "includes/footer.php"; ?>
</body>
</html>
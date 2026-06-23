<?php
require_once("includes/connect_db.php");

// Nếu đã đăng nhập rồi thì đá về trang chủ, không cho vào lại trang đăng nhập
if (isset($_SESSION['user_client'])) {
    header("Location: index.php");
    exit();
}

$error = "";

if (isset($_POST['btn_login'])) {
    $email = trim($_POST['email']);
    $password_raw = $_POST['password'];

    // 1. Tìm người dùng dựa trên Email và phải là Khách hàng (role = 0)
    $sql = "SELECT id, username, email, password, status FROM users WHERE email = '$email' AND role = 0";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);

        // 2. Kiểm tra xem tài khoản có đang bị khóa bởi Admin không?
        if ($user['status'] == 0) {
            $error = "Tài khoản của bạn đã bị khóa. Vui lòng liên hệ CSKH!";
        } else {
            // 3. Giải mã và so sánh mật khẩu
            if (password_verify($password_raw, $user['password'])) {
                
                // Đăng nhập thành công -> Lưu thông tin vào Session
                // Lưu ý: Mình đặt tên biến là 'user_client' để tránh xung đột với Admin
                $_SESSION['user_client'] = [
                    'id' => $user['id'],
                    'name' => $user['username'],
                    'email' => $user['email']
                ];
                
                // Chuyển hướng về trang chủ
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
            
            <?php if($error != "") echo "<div class='msg msg-error'>$error</div>"; ?>

            <form action="" method="post">
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" required placeholder="Nhập địa chỉ email của bạn">
                </div>
                <div class="form-group">
                    <label>Mật khẩu</label>
                    <input type="password" name="password" required placeholder="Nhập mật khẩu">
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
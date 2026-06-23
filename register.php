<?php
require_once("includes/connect_db.php");

$error = "";
$success = "";

if (isset($_POST['btn_register'])) {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password_raw = $_POST['password'];
    $re_password = $_POST['re_password'];
    $phone = trim($_POST['phone']);

    // 1. Kiểm tra 2 mật khẩu có khớp nhau không trước khi làm việc khác
    if ($password_raw !== $re_password) {
        $error = "Mật khẩu xác nhận không khớp. Vui lòng nhập lại!";
    } else {
        // 2. Kiểm tra xem Email đã tồn tại trong hệ thống chưa
        $check_email = mysqli_query($conn, "SELECT id FROM users WHERE email = '$email'");
        
        if (mysqli_num_rows($check_email) > 0) {
            $error = "Email này đã được đăng ký. Vui lòng sử dụng email khác!";
        } else {
            // 3. BĂM MẬT KHẨU (Bảo mật tuyệt đối)
            $hashed_password = password_hash($password_raw, PASSWORD_DEFAULT);

            // 4. Thêm vào CSDL
            $sql_insert = "INSERT INTO users (username, email, password, phone, role, status) 
                           VALUES ('$username', '$email', '$hashed_password', '$phone', 0, 1)";
            
            if (mysqli_query($conn, $sql_insert)) {
                $success = "Đăng ký thành công! Bạn có thể đăng nhập ngay bây giờ.";
            } else {
                $error = "Có lỗi xảy ra, vui lòng thử lại sau!";
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
    <title>Đăng ký tài khoản</title>
    <script src="https://kit.fontawesome.com/da1a483940.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="assets/css/style_client.css">
    <link rel="stylesheet" href="assets/css/style_register.css">
    
</head>
<body>
    <?php include "includes/header.php"; include "includes/nav.php"; ?>

    <main class="main-content">
        <div class="auth-container">
            <h2 class="auth-title">Tạo Tài Khoản</h2>
            
            <?php if($error != "") echo "<div class='msg msg-error'>$error</div>"; ?>
            <?php if($success != "") echo "<div class='msg msg-success'>$success</div>"; ?>

            <form action="" method="post">
                <div class="form-group">
                    <label>Họ và Tên</label>
                    <input type="text" name="username" required placeholder="Nhập tên hiển thị">
                </div>
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" required placeholder="Nhập địa chỉ email">
                </div>
                <div class="form-group">
                    <label>Số điện thoại</label>
                    <input type="text" name="phone" required placeholder="Nhập số điện thoại">
                </div>
                
                <div class="form-group">
                    <label>Mật khẩu</label>
                    <div class="password-wrapper">
                        <input type="password" name="password" id="pwd" required placeholder="Nhập mật khẩu (tối thiểu 6 ký tự)">
                        <i class="fa-regular fa-eye toggle-password" onclick="toggleVisibility('pwd', this)"></i>
                    </div>
                </div>

                <div class="form-group">
                    <label>Xác nhận mật khẩu</label>
                    <div class="password-wrapper">
                        <input type="password" name="re_password" id="re_pwd" required placeholder="Nhập lại mật khẩu phía trên">
                        <i class="fa-regular fa-eye toggle-password" onclick="toggleVisibility('re_pwd', this)"></i>
                    </div>
                </div>

                <button type="submit" name="btn_register" class="btn-auth">Đăng Ký Ngay</button>
            </form>
            
            <div class="auth-links">
                Đã có tài khoản? <a href="login.php">Đăng nhập tại đây</a>
            </div>
        </div>
    </main>

    <?php include "includes/footer.php"; ?>

    <script>
        function toggleVisibility(inputId, iconElement) {
            // Lấy ra thẻ input dựa vào ID được truyền vào
            var input = document.getElementById(inputId);
            
            // Nếu đang là mật khẩu (dấu chấm đen) thì đổi thành text (hiện chữ)
            if (input.type === "password") {
                input.type = "text";
                // Đổi icon con mắt đang mở thành con mắt bị gạch chéo
                iconElement.classList.remove("fa-eye");
                iconElement.classList.add("fa-eye-slash");
            } else {
                // Ngược lại, đổi lại thành dấu chấm đen
                input.type = "password";
                // Đổi icon về lại con mắt mở
                iconElement.classList.remove("fa-eye-slash");
                iconElement.classList.add("fa-eye");
            }
        }
    </script>
</body>
</html>
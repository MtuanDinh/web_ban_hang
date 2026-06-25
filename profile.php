<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require_once("includes/connect_db.php");

// Bắt buộc đăng nhập
if (!isset($_SESSION['user_client'])) {
    header("Location: login.php");
    exit();
}

$user_id = (int)$_SESSION['user_client']['id'];

// ==========================================
// 1. XỬ LÝ CẬP NHẬT THÔNG TIN CÁ NHÂN
// ==========================================
if (isset($_POST['btn_update_info'])) {
    // Database của bạn không có cột name, ta sẽ cập nhật email, phone, address
    $email = mysqli_real_escape_string($conn, trim($_POST['email']));
    $phone = mysqli_real_escape_string($conn, trim($_POST['phone']));
    $address = mysqli_real_escape_string($conn, trim($_POST['address']));
    
    $sql_update = "UPDATE users SET email = '$email', phone = '$phone', address = '$address' WHERE id = $user_id";
    if (mysqli_query($conn, $sql_update)) {
        $_SESSION['flash_msg'] = "Cập nhật thông tin thành công!";
    } else {
        $_SESSION['flash_msg_error'] = "Có lỗi xảy ra trong quá trình cập nhật!";
    }
    header("Location: profile.php");
    exit();
}

// ==========================================
// 2. XỬ LÝ ĐỔI MẬT KHẨU (THÔNG MINH)
// ==========================================
if (isset($_POST['btn_change_pass'])) {
    $old_pass = mysqli_real_escape_string($conn, $_POST['old_password']);
    $new_pass = mysqli_real_escape_string($conn, $_POST['new_password']);
    $confirm_pass = mysqli_real_escape_string($conn, $_POST['confirm_password']);

    $res_check = mysqli_query($conn, "SELECT password FROM users WHERE id = $user_id");
    $user_row = mysqli_fetch_assoc($res_check);
    $current_pass_db = $user_row['password'];

    $is_password_correct = false;
    $hash_type = 'plain';

    if ($current_pass_db === $old_pass) {
        $is_password_correct = true;
        $hash_type = 'plain';
    } elseif ($current_pass_db === md5($old_pass)) {
        $is_password_correct = true;
        $hash_type = 'md5';
    } elseif (password_verify($old_pass, $current_pass_db)) {
        $is_password_correct = true;
        $hash_type = 'bcrypt';
    }

    if (!$is_password_correct) {
         $_SESSION['flash_msg_error'] = "Mật khẩu hiện tại không chính xác!";
    } elseif ($new_pass !== $confirm_pass) {
         $_SESSION['flash_msg_error'] = "Mật khẩu xác nhận không khớp!";
    } else {
         $save_pass = $new_pass;
         if ($hash_type == 'md5') $save_pass = md5($new_pass);
         if ($hash_type == 'bcrypt') $save_pass = password_hash($new_pass, PASSWORD_DEFAULT);
         
         mysqli_query($conn, "UPDATE users SET password = '$save_pass' WHERE id = $user_id");
         $_SESSION['flash_msg'] = "Đổi mật khẩu thành công!";
    }
    header("Location: profile.php");
    exit();
}

// 3. LẤY DỮ LIỆU HIỂN THỊ LÊN FORM
$res_user = mysqli_query($conn, "SELECT * FROM users WHERE id = $user_id");
$user_info = mysqli_fetch_assoc($res_user);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý tài khoản - PhoneStore</title>
    <script src="https://kit.fontawesome.com/da1a483940.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="assets/css/style_client.css">
    <link rel="stylesheet" href="assets/css/style_profile.css">
    
</head>
<body>
    <?php include "includes/header.php"; include "includes/nav.php"; ?>

    <main class="main-content">
        <div class="profile-container">
            
            <div class="profile-sidebar">
                <div class="user-brief">
                    <div class="user-avatar">
                        <?= mb_substr(htmlspecialchars($user_info['username'] ?? 'U'), 0, 1, "UTF-8") ?>
                    </div>
                    <div>
                        <div style="font-size: 12px; color: #888;">Tài khoản của</div>
                        <div class="user-name-brief"><?= htmlspecialchars($user_info['username'] ?? 'Người dùng') ?></div>
                    </div>
                </div>

                <ul class="profile-menu">
                    <li><a href="profile.php" class="active"><i class="fa-regular fa-id-badge"></i> Thông tin tài khoản</a></li>
                    <li><a href="my_orders.php"><i class="fa-solid fa-clipboard-list"></i> Quản lý đơn hàng</a></li>
                    <li><a href="logout.php" style="color: #d93025;"><i class="fa-solid fa-arrow-right-from-bracket"></i> Đăng xuất</a></li>
                </ul>
            </div>

            <div class="profile-content">
                
                <?php if (isset($_SESSION['flash_msg'])): ?>
                    <div class="toast-msg toast-success"><i class="fa-solid fa-circle-check"></i> <?= $_SESSION['flash_msg'] ?></div>
                    <?php unset($_SESSION['flash_msg']); ?>
                <?php endif; ?>
                
                <?php if (isset($_SESSION['flash_msg_error'])): ?>
                    <div class="toast-msg toast-error"><i class="fa-solid fa-triangle-exclamation"></i> <?= $_SESSION['flash_msg_error'] ?></div>
                    <?php unset($_SESSION['flash_msg_error']); ?>
                <?php endif; ?>

                <div class="profile-card">
                    <h3><i class="fa-solid fa-user-pen" style="color: #2f80ed;"></i> Cập nhật thông tin cá nhân</h3>
                    <form action="" method="POST">
                        <div class="form-group">
                            <label>Tên hiển thị (Username)</label>
                            <input type="text" value="<?= htmlspecialchars($user_info['username'] ?? '') ?>" class="form-control" disabled>
                            <small style="color: #999; margin-top: 5px; display: block;">* Tên đăng nhập không thể thay đổi</small>
                        </div>
                        
                        <div class="form-row-2">
                            <div class="form-group">
                                <label>Địa chỉ Email</label>
                                <input type="email" name="email" value="<?= htmlspecialchars($user_info['email'] ?? '') ?>" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label>Số điện thoại</label>
                                <input type="text" name="phone" value="<?= htmlspecialchars($user_info['phone'] ?? '') ?>" class="form-control" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Địa chỉ mặc định</label>
                            <input type="text" name="address" value="<?= htmlspecialchars($user_info['address'] ?? '') ?>" class="form-control" placeholder="Nhập địa chỉ chi tiết để giao hàng...">
                        </div>

                        <button type="submit" name="btn_update_info" class="btn-save"><i class="fa-solid fa-floppy-disk"></i> Lưu Thay Đổi</button>
                    </form>
                </div>

                <div class="profile-card">
                    <h3><i class="fa-solid fa-shield-halved" style="color: #10b981;"></i> Đổi mật khẩu</h3>
                    <form action="" method="POST">
                        <div class="form-row-2">
                            <div class="form-group"><label>Mật khẩu hiện tại</label><input type="password" name="old_password" class="form-control" required></div>
                            <div class="form-group"></div>
                        </div>
                        <div class="form-row-2">
                            <div class="form-group"><label>Mật khẩu MỚI</label><input type="password" name="new_password" class="form-control" required></div>
                            <div class="form-group"><label>Nhập lại mật khẩu MỚI</label><input type="password" name="confirm_password" class="form-control" required></div>
                        </div>
                        <button type="submit" name="btn_change_pass" class="btn-save"><i class="fa-solid fa-key"></i> Đổi Mật Khẩu</button>
                    </form>
                </div>

            </div>
        </div>
    </main>

    <?php include "includes/footer.php"; ?>
</body>
</html>
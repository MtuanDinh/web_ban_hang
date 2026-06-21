<?php
    require_once("../includes/check_admin_alive.php");
    require_once("../../includes/connect_db.php");
    require_once("../../includes/db_helper.php");

    if (isset($_POST['btn_add'])) {
        
        $username = trim($_POST['username']);
        $email = trim($_POST['email']);
        $password_raw = $_POST['password'];
        $phone = trim($_POST['phone']);
        $address = trim($_POST['address']);

        $check_email = selectDb($conn, 'users', 'id', ['email' => $email]);
        if (!empty($check_email)) {
            $_SESSION['flash_msg'] = "Lỗi: Email này đã được sử dụng!";
            $_SESSION['msg_type'] = "error";
            header("Location: add.php");
            exit();
        }

        $hashed_password = password_hash($password_raw, PASSWORD_DEFAULT);

        $user_data = [
            'username' => $username,
            'email' => $email,
            'password' => $hashed_password, 
            'phone' => $phone,
            'address' => $address
        ];

        insertDb($conn, 'users', $user_data);

        $_SESSION['flash_msg'] = "Đã tạo tài khoản khách hàng thành công!";
        $_SESSION['msg_type'] = "success";
        
        header("Location: list.php");
        exit();
    }
?>
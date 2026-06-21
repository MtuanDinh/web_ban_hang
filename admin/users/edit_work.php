<?php
require_once("../includes/check_admin_alive.php");
require_once("../../includes/connect_db.php");
require_once("../../includes/db_helper.php");

if (isset($_POST['btn_edit'])) {
    $id = (int)$_POST['id'];
    $phone = trim($_POST['phone']);
    $address = trim($_POST['address']);

    // Cập nhật các trường thông tin cho phép sửa
    updateDb($conn, 'users', [
        'phone' => $phone,
        'address' => $address
    ], ['id' => $id]);

    $_SESSION['flash_msg'] = "Cập nhật thông tin khách hàng thành công!";
    $_SESSION['msg_type'] = "success";
    header("Location: list.php");
    exit();
}
?>
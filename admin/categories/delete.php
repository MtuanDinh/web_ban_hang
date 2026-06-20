<?php
    require_once("../includes/check_admin_alive.php");
    require_once("../../includes/connect_db.php");
    require_once("../../includes/db_helper.php"); // Nhúng thêm thần binh helper vào đây

    if(isset($_GET['id'])){
        $del_id = (int)$_GET['id'];

        try {
            $condition = ['id' => $del_id];

            deleteDb($conn, 'categories', $condition);
            
            $_SESSION['flash_msg'] = "Đã xóa danh mục thành công!";
            $_SESSION['msg_type'] = "success"; 
            
        } catch (mysqli_sql_exception $e) {
            if ($e->getCode() == 1451) {
                $_SESSION['flash_msg'] = "Lỗi: Không thể xóa vì đang chứa danh mục con!";
            } else {
                $_SESSION['flash_msg'] = "Lỗi hệ thống: " . $e->getMessage();
            }
            $_SESSION['msg_type'] = "error";
        }
    }

    header("Location: list.php");
    exit();
?>
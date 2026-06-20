<?php
require_once("../includes/check_admin_alive.php");
require_once("../../includes/connect_db.php");
require_once("../../includes/db_helper.php");

if (isset($_POST['btn_edit'])) {
    $edit_id = (int)$_POST['edit_id']; 
    $cate_name = trim($_POST['cate_name']);
    
    $parent_id = ($_POST['parent_sel'] == "") ? null : (int)$_POST['parent_sel'];

    $update_data = [
        'name' => $cate_name,
        'parent_id' => $parent_id
    ];

    $where_condition = [
        'id' => $edit_id
    ];

    updateDb($conn, 'categories', $update_data, $where_condition);

    $_SESSION['flash_msg'] = "Cập nhật danh mục thành công!";
    $_SESSION['msg_type'] = "success";
    
    header("Location: list.php");
    exit();
}
?>
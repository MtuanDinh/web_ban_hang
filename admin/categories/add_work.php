<?php
    $res = selectDb($conn, 'categories', 'id, name', ['parent_id' => null]);
    
    if(isset($_POST['btn_add'])){
        $safe_cate_name = mysqli_real_escape_string($conn, trim($_POST['cate_name']));
        
        if(isset($_POST['btn_add'])){
            $cate_name = trim($_POST['cate_name']);

            $parent_id = ($_POST['parent_sel'] == "") ? null : (int)$_POST['parent_sel'];
        }
        
        $category_data = [
            'name' => $cate_name,
            'parent_id' => $parent_id
        ];
        
        insertDb($conn, 'categories', $category_data);

        $_SESSION['flash_msg'] = "Thêm danh mục thành công!";
        header("Location: list.php");
        exit();
    }
?>
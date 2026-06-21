<?php
    require_once("../includes/check_admin_alive.php");
    require_once("../../includes/connect_db.php");
    require_once("../../includes/db_helper.php"); 

    if (isset($_POST['btn_add'])) {
        
        // --- NHẬN DỮ LIỆU CƠ BẢN ---
        $name = trim($_POST['prod_name']);
        $category_id = (int)$_POST['category_id'];


        // --- ĐÓNG GÓI THÔNG SỐ CẤU HÌNH (JSON) ---
        $specs_array = $_POST['specs']; 
        $description_json = json_encode($specs_array, JSON_UNESCAPED_UNICODE);


        // --- XỬ LÝ UPLOAD ẢNH BÌA (1 ẢNH) ---
        $image_name_in_db = null; 
        if (isset($_FILES['prod_img']) && $_FILES['prod_img']['error'] == 0) {
            $file = $_FILES['prod_img'];
            $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
            $new_image_name = time() . '_' . rand(1000, 9999) . '.' . $ext;
            
            if (move_uploaded_file($file['tmp_name'], "../../assets/uploads/" . $new_image_name)) {
                $image_name_in_db = $new_image_name; 
            }
        }


        // --- XỬ LÝ UPLOAD BỘ ẢNH CHI TIẾT (NHIỀU ẢNH) ---
        $gallery_images = [];


        if (isset($_FILES['gallery']) && $_FILES['gallery']['name'][0] != "") {
            
            $file_count = count($_FILES['gallery']['name']);

            for ($i = 0; $i < $file_count; $i++) {
                
                if ($_FILES['gallery']['error'][$i] == 0) {
                    $ext = pathinfo($_FILES['gallery']['name'][$i], PATHINFO_EXTENSION);
                    
                    $new_gallery_name = time() . '_' . rand(1000, 9999) . '_' . $i . '.' . $ext;
                    $upload_destination = "../../assets/uploads/" . $new_gallery_name;
                    
                    if (move_uploaded_file($_FILES['gallery']['tmp_name'][$i], $upload_destination)) {
                        $gallery_images[] = $new_gallery_name; // Bỏ tên ảnh mới vào giỏ
                    }
                }
            }
        }


        $gallery_json = json_encode($gallery_images);

        $product_data = [
            'category_id' => $category_id,
            'name' => $name,
            'image' => $image_name_in_db, 
            'gallery' => $gallery_json,
            'description' => $description_json
        ];

        insertDb($conn, 'products', $product_data);

        $_SESSION['flash_msg'] = "Đã thêm sản phẩm thành công!";
        $_SESSION['msg_type'] = "success";
        
        header("Location: list.php");
        exit();
    }
?>
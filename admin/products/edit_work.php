<?php
    require_once("../includes/check_admin_alive.php");
    require_once("../../includes/connect_db.php");
    require_once("../../includes/db_helper.php");

    if (isset($_POST['btn_edit'])) {
        
        $edit_id = (int)$_POST['edit_id'];
        
        // --- 1. NHẬN DỮ LIỆU CƠ BẢN VÀ ĐÓNG GÓI JSON ---
        $name = trim($_POST['prod_name']);
        $category_id = (int)$_POST['category_id'];
        
        $specs_array = $_POST['specs']; 
        $description_json = json_encode($specs_array, JSON_UNESCAPED_UNICODE);

        // --- 2. TRUY VẤN LẤY THÔNG TIN ẢNH CŨ TỪ DATABASE ---
        $old_data = selectDb($conn, 'products', 'image, gallery', ['id' => $edit_id]);
        $old_image = $old_data[0]['image'];
        $old_gallery_json = $old_data[0]['gallery'];

        $upload_dir = "../../assets/uploads/";

        // --- 3. XỬ LÝ ẢNH BÌA (Cover Image) ---
        $final_image = $old_image; // Mặc định là giữ lại tên ảnh cũ
        
        // Nếu người dùng có chọn ảnh mới và không bị lỗi
        if (isset($_FILES['prod_img']) && $_FILES['prod_img']['error'] == 0) {
            $file = $_FILES['prod_img'];
            $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
            $new_image_name = time() . '_' . rand(1000, 9999) . '.' . $ext;
            
            // Dời ảnh mới vào thư mục
            if (move_uploaded_file($file['tmp_name'], $upload_dir . $new_image_name)) {
                $final_image = $new_image_name; // Cập nhật tên ảnh mới để lưu DB
                
                // XÓA ẢNH CŨ KHỎI Ổ CỨNG (Dọn rác)
                if ($old_image != "" && file_exists($upload_dir . $old_image)) {
                    unlink($upload_dir . $old_image);
                }
            }
        }

        // --- 4. XỬ LÝ BỘ ẢNH CHI TIẾT (Gallery) ---
        $final_gallery_json = $old_gallery_json; // Mặc định giữ bộ ảnh cũ
        
        // Kiểm tra nếu người dùng chọn bộ ảnh mới (phần tử name đầu tiên không rỗng)
        if (isset($_FILES['gallery']) && $_FILES['gallery']['name'][0] != "") {
            
            // a. XÓA TOÀN BỘ ẢNH CHI TIẾT CŨ TRONG Ổ CỨNG TRƯỚC
            if ($old_gallery_json != "" && $old_gallery_json != "null") {
                $old_gallery_arr = json_decode($old_gallery_json, true);
                if (is_array($old_gallery_arr)) {
                    foreach ($old_gallery_arr as $old_g_img) {
                        if ($old_g_img != "" && file_exists($upload_dir . $old_g_img)) {
                            unlink($upload_dir . $old_g_img);
                        }
                    }
                }
            }

            // b. TẢI LÊN BỘ ẢNH MỚI
            $new_gallery_images = [];
            $file_count = count($_FILES['gallery']['name']);

            for ($i = 0; $i < $file_count; $i++) {
                if ($_FILES['gallery']['error'][$i] == 0) {
                    $ext = pathinfo($_FILES['gallery']['name'][$i], PATHINFO_EXTENSION);
                    $new_gallery_name = time() . '_' . rand(1000, 9999) . '_' . $i . '.' . $ext;
                    
                    if (move_uploaded_file($_FILES['gallery']['tmp_name'][$i], $upload_dir . $new_gallery_name)) {
                        $new_gallery_images[] = $new_gallery_name;
                    }
                }
            }
            // Nén bộ ảnh mới thành JSON để chuẩn bị lưu DB
            $final_gallery_json = json_encode($new_gallery_images);
        }

        // --- 5. RÁP MẢNG VÀ GỌI HÀM UPDATE ---
        $update_data = [
            'category_id' => $category_id,
            'name' => $name,
            'image' => $final_image, 
            'gallery' => $final_gallery_json, 
            'description' => $description_json 
        ];

        $condition = [
            'id' => $edit_id
        ];

        // Tuyệt chiêu gọi DB để cập nhật
        updateDb($conn, 'products', $update_data, $condition);

        // --- 6. CHUYỂN HƯỚNG ---
        $_SESSION['flash_msg'] = "Đã cập nhật sản phẩm thành công!";
        $_SESSION['msg_type'] = "success";
        
        header("Location: list.php");
        exit();
    }
?>
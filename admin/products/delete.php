<?php
    require_once("../includes/check_admin_alive.php");
    require_once("../../includes/connect_db.php");
    require_once("../../includes/db_helper.php");

    if (isset($_GET['id'])) {
        $del_id = (int)$_GET['id'];

        // --- BƯỚC 1: TRUY VẤN LẤY TÊN CÁC FILE ẢNH ---
        // Gọi hàm selectDb để lấy ra đúng 2 cột image và gallery của sản phẩm này
        $product = selectDb($conn, 'products', 'image, gallery', ['id' => $del_id]);

        if (!empty($product)) {
            $img_cover = $product[0]['image'];
            $gallery_json = $product[0]['gallery'];

            // Khai báo đường dẫn đến bãi rác (thư mục chứa ảnh)
            $upload_dir = "../../assets/uploads/";

            // --- BƯỚC 2: TIÊU HỦY ẢNH BÌA VẬT LÝ ---
            if ($img_cover != "" && file_exists($upload_dir . $img_cover)) {
                unlink($upload_dir . $img_cover); // unlink chính là hàm xóa file của PHP
            }

            // --- BƯỚC 3: GIẢI NÉN JSON VÀ TIÊU HỦY BỘ ẢNH CHI TIẾT ---
            if ($gallery_json != "" && $gallery_json != "null") {
                // Giải mã JSON thành một mảng PHP
                $gallery_array = json_decode($gallery_json, true);
                
                // Nếu giải mã thành công và nó đúng là một mảng
                if (is_array($gallery_array)) {
                    // Chạy vòng lặp qua từng tên ảnh trong mảng để bắn tỉa từng file một
                    foreach ($gallery_array as $gal_img) {
                        if ($gal_img != "" && file_exists($upload_dir . $gal_img)) {
                            unlink($upload_dir . $gal_img);
                        }
                    }
                }
            }

            // --- BƯỚC 4: XÓA HỒ SƠ TRONG DATABASE ---
            // Nhờ bùa hộ mệnh "ON DELETE CASCADE" bạn cài đặt ở MySQL, 
            // khi xóa ID sản phẩm gốc ở đây, toàn bộ các phiên bản màu sắc ở bảng product_variants sẽ tự động tan biến theo!
            deleteDb($conn, 'products', ['id' => $del_id]);

            $_SESSION['flash_msg'] = "Đã xóa sản phẩm và dọn sạch ổ cứng!";
            $_SESSION['msg_type'] = "success";
            
        } else {
            $_SESSION['flash_msg'] = "Lỗi: Sản phẩm không tồn tại!";
            $_SESSION['msg_type'] = "error";
        }
    }

    // Quay về trang danh sách
    header("Location: list.php");
    exit();
?>
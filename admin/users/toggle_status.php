<?php
    require_once("../includes/check_admin_alive.php");
    require_once("../../includes/connect_db.php");
    require_once("../../includes/db_helper.php");

    // Kiểm tra xem URL có truyền đủ id và action không
    if (isset($_GET['id']) && isset($_GET['action'])) {
        $user_id = (int)$_GET['id'];
        $action = $_GET['action'];

        // --- BƯỚC 1: LÁ CHẮN BẢO MẬT ---
        // Truy vấn xem người dùng này có thực sự tồn tại và lấy ra quyền (role) của họ
        $check_user = selectDb($conn, 'users', 'id, role', ['id' => $user_id]);
        
        if (!empty($check_user)) {
            // TUYỆT ĐỐI QUAN TRỌNG: Chỉ thao tác nếu role = 0 (Khách hàng)
            if ($check_user[0]['role'] == 0) {
                
                // --- BƯỚC 2: XÁC ĐỊNH TRẠNG THÁI MỚI ---
                // Nếu action trên URL là 'lock' thì set status về 0, ngược lại thì về 1
                $new_status = ($action == 'lock') ? 0 : 1;
                
                // --- BƯỚC 3: CẬP NHẬT DATABASE ---
                updateDb($conn, 'users', ['status' => $new_status], ['id' => $user_id]);
                
                // Thông báo linh hoạt theo thao tác
                $msg = ($action == 'lock') ? "🔒 Đã khóa tài khoản khách hàng thành công!" : "✅ Đã mở khóa tài khoản!";
                $_SESSION['flash_msg'] = $msg;
                $_SESSION['msg_type'] = "success";
                
            } else {
                // Báo động đỏ nếu ai đó cố tình truyền ID của Admin vào URL để khóa
                $_SESSION['flash_msg'] = "LỖI BẢO MẬT: Bạn không có quyền khóa tài khoản Quản trị viên!";
                $_SESSION['msg_type'] = "error";
            }
        } else {
            $_SESSION['flash_msg'] = "Lỗi: Tài khoản không tồn tại!";
            $_SESSION['msg_type'] = "error";
        }
    }

    // Xử lý xong thì "đá" về lại trang danh sách ngay lập tức
    header("Location: list.php");
    exit();
?>
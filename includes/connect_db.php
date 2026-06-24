<?php 
    if (session_status() === PHP_SESSION_NONE) {
    session_start();
    }
    $db_server = "localhost";
    $db_user = "root";
    $db_pass = "";
    $db_name = "phone_store";
    $conn = "";
    try{
        $conn = mysqli_connect($db_server, $db_user, $db_pass, $db_name);
        mysqli_set_charset($conn, "utf8mb4");
    }
    catch(mysqli_sql_exception $e){
        die("Can't connect to database: {$e->getMessage()}");
    }

    if (strpos($_SERVER['REQUEST_URI'], '/admin') === false) {
    
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    // Nếu người dùng chưa có Session (mới mở trình duyệt) NHƯNG lại có Cookie lưu ID từ trước
    if (!isset($_SESSION['user_client']) && isset($_COOKIE['remember_user'])) {
        
        $remember_id = (int)$_COOKIE['remember_user'];
        
        // Kiểm tra lại trong DB xem tài khoản có bị khóa không
        $sql_auto_login = "SELECT id, username, email FROM users WHERE id = $remember_id AND role = 0 AND status != 0";
        $res_auto_login = mysqli_query($conn, $sql_auto_login);
        
        if ($res_auto_login && mysqli_num_rows($res_auto_login) > 0) {
            $user_auto = mysqli_fetch_assoc($res_auto_login);
            
            // Tự động cấp lại Session
            $_SESSION['user_client'] = [
                'id' => $user_auto['id'],
                'name' => $user_auto['username'],
                'email' => $user_auto['email']
            ];
        } else {
            setcookie('remember_user', '', time() - 3600, "/");
        }
    }
}
?>


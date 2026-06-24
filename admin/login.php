<?php 
    if(session_status() === PHP_SESSION_NONE){
        session_start();
    }
    
    if(isset($_SESSION['admin_id'])){
        header("Location: index.php");
        exit();
    }

    require_once("../includes/connect_db.php");

    $error_msg = "";
    if(isset($_SESSION['login_error'])){
        $error_msg = $_SESSION['login_error'];
        unset($_SESSION['login_error']);
    }

    if($_SERVER["REQUEST_METHOD"] == "POST"){
        $username = trim($_POST["username"]);
        $password = trim($_POST["password"]);

        if(empty($username)){
            $_SESSION['login_error'] = "Vui lòng nhập tên";
        }
        elseif(empty($password)){
            $_SESSION['login_error'] = "Vui lòng nhập mật khẩu";
        }
        else{
            if (!preg_match("/^[a-zA-Z0-9_]{3,20}$/", $username)) {
                $_SESSION['login_error'] = "Tên đăng nhập không hợp lệ! Chỉ dùng chữ cái, số và dấu gạch dưới.";
            } 
            else {
                $username_verify = mysqli_real_escape_string($conn, $username);
                $sql_query = "SELECT * FROM users WHERE username = '$username_verify' AND role = 1";
                $result = mysqli_query($conn, $sql_query);
                if(mysqli_num_rows($result) > 0){
                    $row = mysqli_fetch_assoc($result);
                    if(password_verify($password, $row["password"])){
                        $_SESSION["admin_id"] = $row["id"];
                        $_SESSION["admin_name"] = $row["username"];

                        header("Location: index.php");
                        mysqli_close($conn);
                        exit();
                    }
                    else{
                        $_SESSION['login_error'] ="Sai mật khẩu!";
                    }
                }
                else{
                    $_SESSION['login_error'] ="Tài khoản không tồn tại hoặc không có quyền admin";
                }
            }
        }
    }

    if(isset($_SESSION['login_error'])){
        header("Location: login.php");
        exit();
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - PhoneStore</title>
    <script src="https://kit.fontawesome.com/da1a483940.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="assets/css/style_login.css">
</head>
<body>
    
    <div class="login-wrapper">
        <div class="login-card">
            
            <div class="login-header">
                <h2><i class="fa-solid fa-shield-halved" style="color: var(--primary-color); margin-right: 8px;"></i>System Admin</h2>
                <p>Đăng nhập để vào bảng điều khiển</p>
            </div>

            <form action="login.php" method="post">
                <div class="form-group">
                    <label for="username">Tên đăng nhập</label>
                    <div class="input-wrapper">
                        <input type="text" name="username" id="username" placeholder="Nhập admin username..." autocomplete="off">
                        <i class="fa-solid fa-user"></i>
                    </div>
                </div>

                <div class="form-group">
                    <label for="password">Mật khẩu</label>
                    <div class="input-wrapper">
                        <input type="password" name="password" id="password" placeholder="Nhập mật khẩu...">
                        <i class="fa-solid fa-lock"></i>
                    </div>
                </div>

                <button type="submit" id="login_btn">Đăng Nhập <i class="fa-solid fa-arrow-right-to-bracket" style="margin-left: 5px;"></i></button>
            </form>

            <?php if(!empty($error_msg)): ?>
                <div class="error_message">
                    <i class="fa-solid fa-triangle-exclamation"></i> <?php echo htmlspecialchars($error_msg); ?>
                </div>
            <?php endif; ?>

        </div>
    </div>
    
</body>
</html>
<?php 
    if(isset($conn)){
        mysqli_close($conn);
    }
?>
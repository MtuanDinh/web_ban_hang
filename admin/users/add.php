<?php
    require_once("../includes/check_admin_alive.php");
    require_once("../../includes/connect_db.php");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://kit.fontawesome.com/da1a483940.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="/web_ban_hang/admin/assets/css/style_main.css">
    <title>Thêm Khách hàng mới</title>
</head>
<body>
    <?php include("../includes/topbar.php"); ?>
    
    <section>
        <?php include("../includes/sidebar.php") ?>
        
        <div id="content">
            <div class="page-header">
                <a href="list.php" class="btn-back"><i class="fa-solid fa-arrow-left"></i> Quay lại danh sách</a>
                <h3 class="page-title" style="margin-top: 15px;">Thêm Khách hàng mới</h3>
            </div>

            <div class="admin-form-container" style="max-width: 800px;">
                <form action="add_work.php" method="post">
                    
                    <h3 style="font-size: 16px; border-bottom: 2px solid var(--border-admin); color: var(--primary-color); padding-bottom: 10px; margin-bottom: 20px;">
                        <i class="fa-solid fa-id-card-clip"></i> Thông tin tài khoản
                    </h3>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="username">Tên hiển thị (Username) *</label>
                            <div style="position: relative;">
                                <i class="fa-solid fa-user" style="position: absolute; left: 15px; top: 50%; transform: translateY(-50%); color: var(--text-muted);"></i>
                                <input type="text" name="username" required id="username" placeholder="VD: nguyenvan_a" class="form-control" style="padding-left: 40px;">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="email">Địa chỉ Email *</label>
                            <div style="position: relative;">
                                <i class="fa-solid fa-envelope" style="position: absolute; left: 15px; top: 50%; transform: translateY(-50%); color: var(--text-muted);"></i>
                                <input type="email" name="email" required id="email" placeholder="VD: email@example.com" class="form-control" style="padding-left: 40px;">
                            </div>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="password">Mật khẩu *</label>
                            <div style="position: relative;">
                                <i class="fa-solid fa-key" style="position: absolute; left: 15px; top: 50%; transform: translateY(-50%); color: var(--text-muted);"></i>
                                <input type="password" name="password" required id="password" placeholder="Tối thiểu 6 ký tự" class="form-control" style="padding-left: 40px;">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="phone">Số điện thoại</label>
                            <div style="position: relative;">
                                <i class="fa-solid fa-phone" style="position: absolute; left: 15px; top: 50%; transform: translateY(-50%); color: var(--text-muted);"></i>
                                <input type="text" name="phone" id="phone" placeholder="VD: 0987654321" class="form-control" style="padding-left: 40px;">
                            </div>
                        </div>
                    </div>

                    <div class="form-group" style="margin-bottom: 30px;">
                        <label for="address">Địa chỉ giao hàng</label>
                        <textarea name="address" id="address" rows="3" placeholder="Số nhà, Tên đường, Quận/Huyện..." class="form-control" style="resize: vertical;"></textarea>
                    </div>

                    <button type="submit" name="btn_add" class="btn-add-new" style="width: 100%; justify-content: center; height: 50px; font-size: 16px;">
                        <i class="fa-solid fa-user-plus"></i> TẠO TÀI KHOẢN MỚI
                    </button>
                </form>
            </div>
            
            <?php include("../includes/footer.php") ?>
        </div>
    </section>
</body>
</html>
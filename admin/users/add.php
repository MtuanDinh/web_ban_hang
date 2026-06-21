<?php
    require_once("../includes/check_admin_alive.php");
    require_once("../../includes/connect_db.php");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/web_ban_hang/admin/assets/css/style_main.css">
    <title>Thêm Khách hàng mới</title>
</head>
<body>
    <?php include("../includes/topbar.php"); ?>
    
    <section>
        <?php include("../includes/sidebar.php") ?>
        
        <div id="content">
            <div id="product_head" style="margin-bottom: 20px;">
                <a href="list.php" style="text-decoration: none; color: var(--text-muted); font-size: 14px;">⬅ Quay lại danh sách Khách hàng</a>
                <h3 style="margin-top: 10px;">👤 Thêm Khách hàng mới</h3>
            </div>

            <div style="padding: 20px; border-radius: 8px; border: 1px solid var(--border-color, #444); background: var(--bg-color);">
                <form action="add_work.php" class="modal_form" method="post" style="max-width: 600px;">
                    
                    <div style="margin-bottom: 15px;">
                        <label for="username">Tên hiển thị (Username) *</label>
                        <input type="text" name="username" required id="username" placeholder="VD: nguyenvan_a" class="form_input" style="width: 100%;">
                    </div>

                    <div style="margin-bottom: 15px;">
                        <label for="email">Địa chỉ Email *</label>
                        <input type="email" name="email" required id="email" placeholder="VD: email@example.com" class="form_input" style="width: 100%;">
                    </div>

                    <div style="margin-bottom: 15px;">
                        <label for="password">Mật khẩu *</label>
                        <input type="password" name="password" required id="password" placeholder="Nhập mật khẩu (tối thiểu 6 ký tự)" class="form_input" style="width: 100%;">
                    </div>

                    <div style="margin-bottom: 15px;">
                        <label for="phone">Số điện thoại</label>
                        <input type="text" name="phone" id="phone" placeholder="VD: 0987654321" class="form_input" style="width: 100%;">
                    </div>

                    <div style="margin-bottom: 25px;">
                        <label for="address">Địa chỉ giao hàng</label>
                        <textarea name="address" id="address" rows="3" placeholder="Số nhà, Tên đường, Quận/Huyện..." class="form_input" style="width: 100%; resize: vertical; padding: 10px;"></textarea>
                    </div>

                    <input type="submit" name="btn_add" value="Tạo tài khoản" class="add_button" style="width: 100%; height: 45px; font-size: 16px;">
                </form>
            </div>
            
            <?php include("../includes/footer.php") ?>
        </div>
    </section>
</body>
</html>
<?php
require_once("../includes/check_admin_alive.php");
require_once("../../includes/connect_db.php");
require_once("../../includes/db_helper.php");

if (!isset($_GET['id'])) {
    header("Location: list.php");
    exit();
}

$edit_id = (int)$_GET['id'];
$user = selectDb($conn, 'users', '*', ['id' => $edit_id]);

if (empty($user)) {
    header("Location: list.php");
    exit();
}
$u = $user[0];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sửa thông tin khách hàng</title>
    <link rel="stylesheet" href="/web_ban_hang/admin/assets/css/style_main.css">
</head>
<body>
    <?php include("../includes/topbar.php"); ?>
    <section>
        <?php include("../includes/sidebar.php") ?>
        <div id="content">
            <h3>✏️ Sửa thông tin: <?php echo $u['username']; ?></h3>
            <form action="edit_work.php" method="post" style="max-width: 500px;">
                <input type="hidden" name="id" value="<?php echo $u['id']; ?>">
                
                <div style="margin-bottom: 15px;">
                    <label>Username (Không thể sửa)</label>
                    <input type="text" value="<?php echo $u['username']; ?>" class="form_input" disabled style="width: 100%; background: #eee;">
                </div>
                
                <div style="margin-bottom: 15px;">
                    <label>Số điện thoại</label>
                    <input type="text" name="phone" value="<?php echo $u['phone']; ?>" class="form_input" style="width: 100%;">
                </div>
                
                <div style="margin-bottom: 25px;">
                    <label>Địa chỉ giao hàng</label>
                    <textarea name="address" rows="3" class="form_input" style="width: 100%;"><?php echo $u['address']; ?></textarea>
                </div>
                
                <input type="submit" name="btn_edit" value="Lưu thay đổi" class="add_button" style="width: 100%;">
            </form>
        </div>
    </section>
</body>
</html>
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://kit.fontawesome.com/da1a483940.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="/web_ban_hang/admin/assets/css/style_main.css">
    <title>Sửa thông tin khách hàng</title>
</head>
<body>
    <?php include("../includes/topbar.php"); ?>
    <section>
        <?php include("../includes/sidebar.php") ?>
        <div id="content">
            <div class="page-header">
                <a href="list.php" class="btn-back"><i class="fa-solid fa-arrow-left"></i> Quay lại danh sách</a>
                <h3 class="page-title" style="margin-top: 15px;">Sửa Khách hàng: <span style="color: var(--primary-color);"><?php echo htmlspecialchars($u['username']); ?></span></h3>
            </div>

            <div class="admin-form-container" style="max-width: 800px;">
                <form action="edit_work.php" method="post">
                    <input type="hidden" name="id" value="<?php echo $u['id']; ?>">
                    
                    <h3 style="font-size: 16px; border-bottom: 2px solid var(--border-admin); color: var(--accent-success); padding-bottom: 10px; margin-bottom: 20px;">
                        <i class="fa-solid fa-user-pen"></i> Chỉnh sửa Thông tin liên hệ
                    </h3>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label>Username (Không thể sửa)</label>
                            <div style="position: relative;">
                                <i class="fa-solid fa-ban" style="position: absolute; left: 15px; top: 50%; transform: translateY(-50%); color: var(--accent-error);"></i>
                                <input type="text" value="<?php echo htmlspecialchars($u['username']); ?>" class="form-control" disabled style="background: rgba(255,255,255,0.05); color: var(--text-muted); cursor: not-allowed; padding-left: 40px; border-style: dashed;">
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label>Số điện thoại</label>
                            <div style="position: relative;">
                                <i class="fa-solid fa-phone" style="position: absolute; left: 15px; top: 50%; transform: translateY(-50%); color: var(--text-muted);"></i>
                                <input type="text" name="phone" value="<?php echo htmlspecialchars($u['phone']); ?>" class="form-control" style="padding-left: 40px;">
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group" style="margin-bottom: 30px;">
                        <label>Địa chỉ giao hàng</label>
                        <textarea name="address" rows="3" class="form-control"><?php echo htmlspecialchars($u['address']); ?></textarea>
                    </div>
                    
                    <button type="submit" name="btn_edit" class="btn-add-new" style="background: var(--accent-success); width: 100%; justify-content: center; height: 50px; font-size: 16px;">
                        <i class="fa-solid fa-floppy-disk"></i> LƯU THAY ĐỔI
                    </button>
                </form>
            </div>
            
            <?php include("../includes/footer.php") ?>
        </div>
    </section>
</body>
</html>
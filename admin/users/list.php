<?php
require_once("../includes/check_admin_alive.php");
require_once("../../includes/connect_db.php");
require_once("../../includes/db_helper.php");

// Lấy danh sách tài khoản, bỏ qua các tài khoản Admin (role = 1), chỉ lấy Khách hàng (role = 0)
// Sắp xếp người mới đăng ký lên đầu
$users = selectDb($conn, 'users', '*', ['role' => 0], 'ORDER BY id DESC');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/web_ban_hang/admin/assets/css/style_main.css">
    <title>Quản lý Khách hàng</title>
</head>
<body>
    <?php include("../includes/topbar.php"); ?>
    
    <section>
        <?php include("../includes/sidebar.php") ?>
        
        <div id="content">
            <div id="product_head">
                <h3>👥 Quản lý Khách hàng</h3>
                <a href="add.php" style="background-color: var(--primary-color); color: white; padding: 10px 15px; border-radius: 5px; text-decoration: none; font-weight: bold;">+ Thêm khách hàng</a>
            </div>

            <table id="product_table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Tên hiển thị (Username)</th>
                        <th>Email / Tài khoản</th>
                        <th>Số điện thoại</th>
                        <th>Trạng thái</th>
                        <th>Ngày đăng ký</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if (!empty($users)) {
                        foreach ($users as $row) {
                    ?>
                            <tr>
                                <td><?php echo $row['id']; ?></td>
                                <td><strong><?php echo $row['username']; ?></strong></td>
                                <td><?php echo $row['email']; ?></td>
                                <td><?php echo $row['phone'] ? $row['phone'] : '<span class="text_muted">Chưa cập nhật</span>'; ?></td>
                                
                                <td>
                                    <?php if ($row['status'] == 1): ?>
                                        <span style="color: #28a745; font-weight: bold;">Đang hoạt động</span>
                                    <?php else: ?>
                                        <span style="color: var(--accent-error); font-weight: bold;">Bị khóa 🔒</span>
                                    <?php endif; ?>
                                </td>
                                
                                <td><?php echo date('d/m/Y', strtotime($row['created_at'])); ?></td>

                                <td>
                                    <?php if ($row['status'] == 1): ?>
                                        <a href="toggle_status.php?id=<?php echo $row['id']; ?>&action=lock" 
                                           class="btn_action delete" 
                                           onclick="return confirm('Bạn muốn khóa tài khoản này? Khách hàng sẽ không thể đăng nhập.');"
                                           style="background-color: #ffc107; color: black; margin-right: 5px;">Khóa</a>
                                    <?php else: ?>
                                        <a href="toggle_status.php?id=<?php echo $row['id']; ?>&action=unlock" 
                                           class="btn_action edit" 
                                           style="background-color: #28a745; margin-right: 5px;">Mở khóa</a>
                                    <?php endif; ?>
                                    
                                    <a href="edit.php?id=<?php echo $row['id']; ?>" class="btn_action edit">Sửa</a>
                                </td>
                            </tr>
                    <?php
                        }
                    } else {
                        echo "<tr><td colspan='7' class='table_no_data'>Chưa có khách hàng nào trong hệ thống!</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
            
            <?php include("../includes/footer.php") ?>
        </div>
    </section>
</body>
</html>
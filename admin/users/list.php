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
    <script src="https://kit.fontawesome.com/da1a483940.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="/web_ban_hang/admin/assets/css/style_main.css">
    <title>Quản lý Khách hàng</title>
</head>
<body>
    <?php include("../includes/topbar.php"); ?>
    
    <section>
        <?php include("../includes/sidebar.php") ?>
        
        <div id="content">
            <div class="page-header" style="display: flex; justify-content: space-between; align-items: center;">
                <h3 class="page-title"><i class="fa-solid fa-users" style="color: #00E5FF; margin-right: 10px;"></i> Quản lý Khách hàng</h3>
                <a href="add.php" class="btn-add-new" style="text-decoration: none;"><i class="fa-solid fa-user-plus"></i> Thêm khách hàng</a>
            </div>

            <div class="table-container">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th style="width: 60px;">ID</th>
                            <th>Tên hiển thị (Username)</th>
                            <th>Email / Tài khoản</th>
                            <th>Số điện thoại</th>
                            <th style="text-align: center;">Trạng thái</th>
                            <th>Ngày đăng ký</th>
                            <th style="text-align: center;">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if (!empty($users)) {
                            foreach ($users as $row) {
                        ?>
                                <tr>
                                    <td><strong style="color: var(--text-muted);">#<?php echo $row['id']; ?></strong></td>
                                    
                                    <td><strong style="color: var(--primary-color); font-size: 15px;"><?php echo htmlspecialchars($row['username']); ?></strong></td>
                                    
                                    <td style="color: var(--text-admin);"><?php echo htmlspecialchars($row['email']); ?></td>
                                    
                                    <td><?php echo $row['phone'] ? htmlspecialchars($row['phone']) : '<span style="color: var(--text-muted);"><i class="fa-solid fa-minus"></i> Chưa cập nhật</span>'; ?></td>
                                    
                                    <td style="text-align: center;">
                                        <?php if ($row['status'] == 1): ?>
                                            <span class="status-badge badge-success" style="background: rgba(0, 230, 118, 0.1); color: var(--accent-success); border-color: rgba(0, 230, 118, 0.2);">
                                                <i class="fa-solid fa-user-check"></i> Hoạt động
                                            </span>
                                        <?php else: ?>
                                            <span class="status-badge badge-cancelled" style="background: rgba(255, 77, 79, 0.1); color: var(--accent-error); border-color: rgba(255, 77, 79, 0.2);">
                                                <i class="fa-solid fa-lock"></i> Bị khóa
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    
                                    <td style="color: var(--text-muted); font-size: 13px;">
                                        <i class="fa-regular fa-calendar" style="margin-right: 4px;"></i> 
                                        <?php echo date('d/m/Y', strtotime($row['created_at'])); ?>
                                    </td>

                                    <td style="text-align: center;">
                                        <?php if ($row['status'] == 1): ?>
                                            <a href="toggle_status.php?id=<?php echo $row['id']; ?>&action=lock" 
                                               class="action-btn btn-delete" 
                                               style="background: rgba(255, 183, 77, 0.1); color: #ffb74d; border-color: rgba(255, 183, 77, 0.2);"
                                               onclick="return confirm('Bạn muốn khóa tài khoản này? Khách hàng sẽ không thể đăng nhập.');">
                                               <i class="fa-solid fa-lock"></i> Khóa
                                            </a>
                                        <?php else: ?>
                                            <a href="toggle_status.php?id=<?php echo $row['id']; ?>&action=unlock" 
                                               class="action-btn btn-edit" 
                                               style="background: rgba(0, 230, 118, 0.1); color: var(--accent-success); border-color: rgba(0, 230, 118, 0.2);">
                                               <i class="fa-solid fa-unlock"></i> Mở khóa
                                            </a>
                                        <?php endif; ?>
                                        
                                        <a href="edit.php?id=<?php echo $row['id']; ?>" class="action-btn btn-edit"><i class="fa-solid fa-pen-to-square"></i> Sửa</a>
                                    </td>
                                </tr>
                        <?php
                            }
                        } else {
                            echo "<tr><td colspan='7' style='text-align: center; padding: 40px; color: var(--text-muted);'>
                                    <i class='fa-solid fa-users-slash' style='font-size: 40px; margin-bottom: 15px; opacity: 0.5; display: block;'></i>
                                    Chưa có khách hàng nào trong hệ thống!
                                  </td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
            
            <?php include("../includes/footer.php") ?>
        </div>
    </section>
</body>
</html>
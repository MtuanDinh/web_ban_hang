<?php
require_once("../includes/check_admin_alive.php");
require_once("../../includes/connect_db.php");
require_once("../../includes/db_helper.php");

// Lấy toàn bộ danh sách đơn hàng, sắp xếp mới nhất lên đầu
$orders = selectDb($conn, 'orders', '*', [], 'ORDER BY created_at DESC');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://kit.fontawesome.com/da1a483940.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="/web_ban_hang/admin/assets/css/style_main.css">
    <title>Quản lý Đơn hàng</title>
</head>
<body>
    <?php include("../includes/topbar.php"); ?>
    
    <section>
        <?php include("../includes/sidebar.php") ?>
        
        <div id="content">
            <div class="page-header">
                <h3 class="page-title"><i class="fa-solid fa-file-invoice-dollar" style="color: #ffb74d; margin-right: 10px;"></i> Quản lý Đơn hàng</h3>
            </div>

            <div class="table-container">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th style="width: 80px;">Mã ĐH</th>
                            <th>Khách hàng</th>
                            <th>Số điện thoại</th>
                            <th>Tổng tiền</th>
                            <th>Trạng thái</th>
                            <th>Ngày đặt</th>
                            <th style="text-align: center;">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if (!empty($orders)) {
                            foreach ($orders as $row) {
                        ?>
                                <tr>
                                    <td><strong style="color: var(--text-muted);">#<?php echo $row['id']; ?></strong></td>
                                    
                                    <td style="font-weight: 500; color: var(--text-admin);">
                                        <?php echo htmlspecialchars($row['customer_name']); ?>
                                    </td>
                                    
                                    <td><?php echo htmlspecialchars($row['customer_phone']); ?></td>
                                    
                                    <td style="color: var(--accent-error); font-weight: 700; font-size: 15px;">
                                        <?php echo number_format($row['total'], 0, ',', '.'); ?> đ
                                    </td>
                                    
                                    <td>
                                        <?php 
                                            // Tái sử dụng CSS Tem trạng thái (Status Badge) từ trang Dashboard
                                            $status = $row['status'];
                                            if ($status == 0) {
                                                echo '<span class="status-badge badge-pending"><i class="fa-solid fa-clock"></i> Chờ duyệt</span>';
                                            } elseif ($status == 1) {
                                                echo '<span class="status-badge badge-shipping"><i class="fa-solid fa-truck-fast"></i> Đang giao</span>';
                                            } elseif ($status == 2) {
                                                echo '<span class="status-badge badge-success"><i class="fa-solid fa-check"></i> Hoàn thành</span>';
                                            } elseif ($status == 3) {
                                                echo '<span class="status-badge badge-cancelled"><i class="fa-solid fa-xmark"></i> Đã hủy</span>';
                                            }
                                        ?>
                                    </td>
                                    
                                    <td style="color: var(--text-muted); font-size: 13px;">
                                        <i class="fa-regular fa-calendar" style="margin-right: 4px;"></i> 
                                        <?php echo date('H:i d/m/Y', strtotime($row['created_at'])); ?>
                                    </td>

                                    <td style="text-align: center;">
                                        <a href="detail.php?id=<?php echo $row['id']; ?>" class="action-btn btn-edit" style="background: rgba(0, 229, 255, 0.1); color: var(--primary-color); border-color: rgba(0, 229, 255, 0.2);">
                                            <i class="fa-solid fa-eye"></i> Chi tiết
                                        </a>
                                    </td>
                                </tr>
                        <?php
                            }
                        } else {
                            echo "<tr><td colspan='7' style='text-align: center; padding: 40px; color: var(--text-muted);'>
                                    <i class='fa-solid fa-file-invoice' style='font-size: 40px; margin-bottom: 15px; opacity: 0.5; display: block;'></i>
                                    Chưa có đơn hàng nào phát sinh!
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
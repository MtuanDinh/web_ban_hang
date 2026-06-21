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
    <link rel="stylesheet" href="/web_ban_hang/admin/assets/css/style_main.css">
    <title>Quản lý Đơn hàng</title>
</head>
<body>
    <?php include("../includes/topbar.php"); ?>
    
    <section>
        <?php include("../includes/sidebar.php") ?>
        
        <div id="content">
            <div id="product_head" style="margin-bottom: 20px;">
                <h3>📦 Quản lý Đơn hàng</h3>
            </div>

            <table id="product_table">
                <thead>
                    <tr>
                        <th>Mã ĐH</th>
                        <th>Khách hàng</th>
                        <th>Số điện thoại</th>
                        <th>Tổng tiền</th>
                        <th>Trạng thái</th>
                        <th>Ngày đặt</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if (!empty($orders)) {
                        foreach ($orders as $row) {
                    ?>
                            <tr>
                                <td><strong>#<?php echo $row['id']; ?></strong></td>
                                <td><?php echo $row['customer_name']; ?></td>
                                <td><?php echo $row['customer_phone']; ?></td>
                                
                                <td style="color: var(--accent-error); font-weight: bold;">
                                    <?php echo number_format($row['total'], 0, ',', '.'); ?> đ
                                </td>
                                
                                <td>
                                    <?php 
                                        // Xử lý huy hiệu Trạng thái (Status Badge)
                                        $status = $row['status'];
                                        if ($status == 0) {
                                            echo '<span style="background: #ffc107; color: #000; padding: 4px 8px; border-radius: 4px; font-weight: bold; font-size: 12px;">Chờ duyệt</span>';
                                        } elseif ($status == 1) {
                                            echo '<span style="background: #17a2b8; color: #fff; padding: 4px 8px; border-radius: 4px; font-weight: bold; font-size: 12px;">Đang giao</span>';
                                        } elseif ($status == 2) {
                                            echo '<span style="background: #28a745; color: #fff; padding: 4px 8px; border-radius: 4px; font-weight: bold; font-size: 12px;">Hoàn thành</span>';
                                        } elseif ($status == 3) {
                                            echo '<span style="background: #dc3545; color: #fff; padding: 4px 8px; border-radius: 4px; font-weight: bold; font-size: 12px;">Đã hủy</span>';
                                        }
                                    ?>
                                </td>
                                
                                <td><?php echo date('H:i d/m/Y', strtotime($row['created_at'])); ?></td>

                                <td>
                                    <a href="detail.php?id=<?php echo $row['id']; ?>" class="btn_action edit" style="background-color: var(--primary-color); color: var(--bg-admin);">👁️ Chi tiết & Xử lý</a>
                                </td>
                            </tr>
                    <?php
                        }
                    } else {
                        echo "<tr><td colspan='7' class='table_no_data'>Chưa có đơn hàng nào!</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
            
            <?php include("../includes/footer.php") ?>
        </div>
    </section>
</body>
</html>
<?php
require_once("includes/check_admin_alive.php");
require_once("../includes/connect_db.php");
require_once("../includes/db_helper.php");

// a. Đếm tổng khách hàng (chỉ đếm những người có role = 0)
$q_users = mysqli_query($conn, "SELECT COUNT(id) AS total_users FROM users WHERE role = 0");
$total_users = mysqli_fetch_assoc($q_users)['total_users'];

// b. Đếm tổng sản phẩm
$q_products = mysqli_query($conn, "SELECT COUNT(id) AS total_products FROM products");
$total_products = mysqli_fetch_assoc($q_products)['total_products'];

// c. Đếm tổng đơn hàng
$q_orders = mysqli_query($conn, "SELECT COUNT(id) AS total_orders FROM orders");
$total_orders = mysqli_fetch_assoc($q_orders)['total_orders'];

// d. Tính tổng doanh thu (Chỉ cộng dồn cột 'total' của những đơn hàng Đã Hoàn Thành - status = 2)
$q_revenue = mysqli_query($conn, "SELECT SUM(total) AS total_revenue FROM orders WHERE status = 2");
$total_revenue = mysqli_fetch_assoc($q_revenue)['total_revenue'];
// Nếu chưa bán được đơn nào hoàn thành, hàm SUM sẽ trả về NULL, ta cần gán nó về 0
if (!$total_revenue) $total_revenue = 0;


// --- 2. LẤY 5 ĐƠN HÀNG MỚI NHẤT ĐỂ HIỂN THỊ NHANH ---
$recent_orders = selectDb($conn, 'orders', '*', [], 'ORDER BY created_at DESC LIMIT 5');
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/web_ban_hang/admin/assets/css/style_main.css">
    <title>Trang chủ</title>
</head>


<body>
    <?php include("includes/topbar.php"); ?>
    
    <section>
        <?php include("includes/sidebar.php") ?>
        
        <div id="content">
            <div id="product_head" style="margin-bottom: 20px;">
                <h3>Quản lý hệ thống</h3>
            </div>

            <div class="dashboard_grid">
                <div class="metric_card" style="border-left: 4px solid #17a2b8;">
                    <div class="metric_info">
                        <h4>Tổng Khách Hàng</h4>
                        <h2><?php echo number_format($total_users); ?></h2>
                    </div>
                    <div class="metric_icon">👥</div>
                </div>

                <div class="metric_card" style="border-left: 4px solid #6f42c1;">
                    <div class="metric_info">
                        <h4>Sản Phẩm Đang Bán</h4>
                        <h2><?php echo number_format($total_products); ?></h2>
                    </div>
                    <div class="metric_icon">📱</div>
                </div>

                <div class="metric_card" style="border-left: 4px solid #ffc107;">
                    <div class="metric_info">
                        <h4>Tổng Đơn Hàng</h4>
                        <h2><?php echo number_format($total_orders); ?></h2>
                    </div>
                    <div class="metric_icon">📦</div>
                </div>

                <div class="metric_card" style="border-left: 4px solid #28a745;">
                    <div class="metric_info">
                        <h4>Doanh Thu Thực Tế</h4>
                        <h2 style="color: #28a745;"><?php echo number_format($total_revenue, 0, ',', '.'); ?> đ</h2>
                    </div>
                    <div class="metric_icon">💰</div>
                </div>
            </div>

            <div style="background: var(--bg-color, #222); padding: 20px; border-radius: 8px; border: 1px solid var(--border-color, #444);">
                <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--border-color); padding-bottom: 10px; margin-bottom: 15px;">
                    <h4 style="margin: 0;">⚡ Đơn hàng mới nhất</h4>
                    <a href="orders/list.php" style="color: var(--primary-color); text-decoration: none; font-size: 14px;">Xem tất cả &rarr;</a>
                </div>

                <table id="product_table">
                    <thead>
                        <tr>
                            <th>Mã ĐH</th>
                            <th>Khách hàng</th>
                            <th>Tổng tiền</th>
                            <th>Trạng thái</th>
                            <th>Thời gian</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if (!empty($recent_orders)) {
                            foreach ($recent_orders as $row) {
                        ?>
                                <tr>
                                    <td><strong>#<?php echo $row['id']; ?></strong></td>
                                    <td><?php echo $row['customer_name']; ?></td>
                                    <td style="color: var(--accent-error); font-weight: bold;">
                                        <?php echo number_format($row['total'], 0, ',', '.'); ?> đ
                                    </td>
                                    <td>
                                        <?php 
                                            $status = $row['status'];
                                            if ($status == 0) echo '<span style="color: #ffc107; font-weight: bold;">⏳ Chờ duyệt</span>';
                                            elseif ($status == 1) echo '<span style="color: #17a2b8; font-weight: bold;">🚚 Đang giao</span>';
                                            elseif ($status == 2) echo '<span style="color: #28a745; font-weight: bold;">✅ Hoàn thành</span>';
                                            elseif ($status == 3) echo '<span style="color: #dc3545; font-weight: bold;">❌ Đã hủy</span>';
                                        ?>
                                    </td>
                                    <td><?php echo date('H:i d/m', strtotime($row['created_at'])); ?></td>
                                </tr>
                        <?php
                            }
                        } else {
                            echo "<tr><td colspan='5' class='table_no_data'>Chưa có đơn hàng nào phát sinh!</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
            
            <?php include("includes/footer.php") ?>
        </div>
    </section>
</body>

</html>
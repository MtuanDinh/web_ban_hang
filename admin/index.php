<?php
require_once("includes/check_admin_alive.php");
require_once("../includes/connect_db.php");
require_once("../includes/db_helper.php");

// a. Đếm tổng khách hàng
$q_users = mysqli_query($conn, "SELECT COUNT(id) AS total_users FROM users WHERE role = 0");
$total_users = mysqli_fetch_assoc($q_users)['total_users'];

// b. Đếm tổng sản phẩm
$q_products = mysqli_query($conn, "SELECT COUNT(id) AS total_products FROM products");
$total_products = mysqli_fetch_assoc($q_products)['total_products'];

// c. Đếm tổng đơn hàng
$q_orders = mysqli_query($conn, "SELECT COUNT(id) AS total_orders FROM orders");
$total_orders = mysqli_fetch_assoc($q_orders)['total_orders'];

// d. Tính tổng doanh thu
$q_revenue = mysqli_query($conn, "SELECT SUM(total) AS total_revenue FROM orders WHERE status = 2");
$total_revenue = mysqli_fetch_assoc($q_revenue)['total_revenue'];
if (!$total_revenue) $total_revenue = 0;

// e. LẤY 5 ĐƠN HÀNG MỚI NHẤT
$recent_orders = selectDb($conn, 'orders', '*', [], 'ORDER BY created_at DESC LIMIT 5');

// ==========================================
// THU THẬP DỮ LIỆU CHO BIỂU ĐỒ (CHARTS DATA)
// ==========================================

// 1. Dữ liệu Biểu đồ Trạng thái đơn hàng (Doughnut Chart)
$q_status = mysqli_query($conn, "SELECT status, COUNT(*) as count FROM orders GROUP BY status");
$status_data = [0 => 0, 1 => 0, 2 => 0, 3 => 0]; // 0: Chờ duyệt, 1: Đang giao, 2: Hoàn thành, 3: Đã hủy
while($r = mysqli_fetch_assoc($q_status)) {
    $status_data[$r['status']] = $r['count'];
}
$status_json = json_encode(array_values($status_data));

// 2. Dữ liệu Biểu đồ Doanh thu 7 ngày gần nhất (Line Chart)
$q_revenue_7days = mysqli_query($conn, "
    SELECT DATE(created_at) as order_date, SUM(total) as daily_revenue 
    FROM orders 
    WHERE status = 2 AND created_at >= DATE(NOW()) - INTERVAL 7 DAY 
    GROUP BY DATE(created_at) 
    ORDER BY DATE(created_at) ASC
");

$dates = [];
$revenues = [];
// Lấp đầy 7 ngày gần nhất (kể cả những ngày không có doanh thu cũng hiện 0đ)
for ($i = 6; $i >= 0; $i--) {
    $date_str = date('Y-m-d', strtotime("-$i days"));
    $dates[$date_str] = date('d/m', strtotime("-$i days"));
    $revenues[$date_str] = 0;
}
// Đổ dữ liệu thực tế đè lên
while($r = mysqli_fetch_assoc($q_revenue_7days)) {
    $date_val = $r['order_date'];
    if(isset($revenues[$date_val])) {
        $revenues[$date_val] = $r['daily_revenue'];
    }
}
$labels_json = json_encode(array_values($dates));
$revenue_json = json_encode(array_values($revenues));
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Admin Panel</title>
    <script src="https://kit.fontawesome.com/da1a483940.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="/web_ban_hang/admin/assets/css/style_main.css">
</head>

<body>
    <?php include("includes/topbar.php"); ?>
    
    <section>
        <?php include("includes/sidebar.php") ?>
        
        <div id="content">
            <div class="page-header">
                <h3 class="page-title">Tổng Quan Hệ Thống</h3>
            </div>

            <div class="dashboard_grid">
                <div class="metric_card card-users" onclick="window.location.href='users/list.php'">
                    <div class="metric_info">
                        <h4>Khách Hàng</h4>
                        <h2><?php echo number_format($total_users); ?></h2>
                    </div>
                    <div class="metric_icon icon-users"><i class="fa-solid fa-users"></i></div>
                </div>

                <div class="metric_card card-products" onclick="window.location.href='products/list.php'">
                    <div class="metric_info">
                        <h4>Sản Phẩm</h4>
                        <h2><?php echo number_format($total_products); ?></h2>
                    </div>
                    <div class="metric_icon icon-products"><i class="fa-solid fa-box-open"></i></div>
                </div>

                <div class="metric_card card-orders" onclick="window.location.href='orders/list.php'">
                    <div class="metric_info">
                        <h4>Đơn Hàng</h4>
                        <h2><?php echo number_format($total_orders); ?></h2>
                    </div>
                    <div class="metric_icon icon-orders"><i class="fa-solid fa-file-invoice-dollar"></i></div>
                </div>

                <div class="metric_card card-revenue">
                    <div class="metric_info">
                        <h4>Doanh Thu</h4>
                        <h2 style="color: var(--accent-success);"><?php echo number_format($total_revenue, 0, ',', '.'); ?> đ</h2>
                    </div>
                    <div class="metric_icon icon-revenue"><i class="fa-solid fa-wallet"></i></div>
                </div>
            </div>

            <div class="charts-grid">
                <div class="chart-card">
                    <h4><i class="fa-solid fa-chart-line" style="color: var(--primary-color);"></i> Doanh Thu 7 Ngày Gần Nhất</h4>
                    <div class="chart-container">
                        <canvas id="revenueChart"></canvas>
                    </div>
                </div>

                <div class="chart-card">
                    <h4><i class="fa-solid fa-chart-pie" style="color: #b388ff;"></i> Tỷ Lệ Đơn Hàng</h4>
                    <div class="chart-container">
                        <canvas id="statusChart"></canvas>
                    </div>
                </div>
            </div>

            <div class="recent-orders-wrapper">
                <div class="table-header">
                    <h4><i class="fa-solid fa-bolt" style="color: #ffc107;"></i> Đơn Hàng Mới Cần Xử Lý</h4>
                    <a href="orders/list.php" class="view-all-btn">Xem tất cả <i class="fa-solid fa-arrow-right"></i></a>
                </div>

                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Mã ĐH</th>
                            <th>Khách hàng</th>
                            <th>Tổng tiền</th>
                            <th>Trạng thái</th>
                            <th>Thời gian đặt</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if (!empty($recent_orders)) {
                            foreach ($recent_orders as $row) {
                        ?>
                                <tr>
                                    <td><strong style="color: var(--text-muted);">#<?php echo $row['id']; ?></strong></td>
                                    <td style="font-weight: 500;"><?php echo htmlspecialchars($row['customer_name']); ?></td>
                                    <td style="color: var(--accent-error); font-weight: 700;">
                                        <?php echo number_format($row['total'], 0, ',', '.'); ?> đ
                                    </td>
                                    <td>
                                        <?php 
                                            $status = $row['status'];
                                            if ($status == 0) echo '<span class="status-badge badge-pending"><i class="fa-solid fa-clock"></i> Chờ duyệt</span>';
                                            elseif ($status == 1) echo '<span class="status-badge badge-shipping"><i class="fa-solid fa-truck-fast"></i> Đang giao</span>';
                                            elseif ($status == 2) echo '<span class="status-badge badge-success"><i class="fa-solid fa-check"></i> Hoàn thành</span>';
                                            elseif ($status == 3) echo '<span class="status-badge badge-cancelled"><i class="fa-solid fa-xmark"></i> Đã hủy</span>';
                                        ?>
                                    </td>
                                    <td style="color: var(--text-muted); font-size: 13px;">
                                        <i class="fa-regular fa-calendar" style="margin-right: 5px;"></i> <?php echo date('H:i d/m', strtotime($row['created_at'])); ?>
                                    </td>
                                </tr>
                        <?php
                            }
                        } else {
                            echo "<tr><td colspan='5' style='text-align: center; padding: 40px; color: var(--text-muted);'>Chưa có đơn hàng nào phát sinh!</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
            
            <?php include("includes/footer.php") ?>
        </div>
    </section>

    <script>
        const chartLabels = <?php echo $labels_json; ?>;
        const chartRevenue = <?php echo $revenue_json; ?>;
        const chartStatus = <?php echo $status_json; ?>;
    </script>
    <script src="/web_ban_hang/admin/assets/js/dashboard.js"></script>
</body>
</html>
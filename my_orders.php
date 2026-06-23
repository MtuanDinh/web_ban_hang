<?php
require_once("includes/connect_db.php");

// Bắt buộc phải đăng nhập mới xem được lịch sử mua hàng
if (!isset($_SESSION['user_client'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_client']['id'];

// Lấy danh sách tất cả các đơn hàng của user này, xếp đơn mới nhất lên đầu
$sql_orders = "SELECT * FROM orders WHERE user_id = $user_id ORDER BY id DESC";
$res_orders = mysqli_query($conn, $sql_orders);

$orders = [];
if ($res_orders && mysqli_num_rows($res_orders) > 0) {
    while ($row = mysqli_fetch_assoc($res_orders)) {
        $orders[] = $row;
    }
}

// Từ điển dịch trạng thái đơn hàng (Dựa vào cột status trong CSDL)
function getOrderStatus($status_code) {
    switch ($status_code) {
        case 0: return '<span class="status-badge status-pending">Chờ duyệt</span>';
        case 1: return '<span class="status-badge status-shipping">Đang giao hàng</span>';
        case 2: return '<span class="status-badge status-completed">Đã giao thành công</span>';
        case 3: return '<span class="status-badge status-canceled">Đã hủy</span>';
        default: return '<span class="status-badge">Không xác định</span>';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đơn hàng của tôi - PhoneStore</title>
    <script src="https://kit.fontawesome.com/da1a483940.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="assets/css/style_client.css">
    <link rel="stylesheet" href="assets/css/style_order.css">
    
</head>
<body>
    <?php include "includes/header.php"; include "includes/nav.php"; ?>

    <main class="main-content">
        <div class="orders-container">
            <h2 class="page-title">📦 Lịch Sử Đơn Hàng</h2>

            <?php if (empty($orders)): ?>
                <div class="empty-orders">
                    <i class="fa-solid fa-box-open"></i>
                    <h3 style="color: #555;">Bạn chưa có đơn hàng nào!</h3>
                    <p style="color: #777; margin-bottom: 20px;">Hãy tham quan cửa hàng và đặt mua những sản phẩm tuyệt vời nhé.</p>
                    <a href="index.php" class="btn-checkout" style="padding: 12px 30px; background: #d70018; color: #fff; text-decoration: none; border-radius: 5px; font-weight: bold;">Mua Sắm Ngay</a>
                </div>
            <?php else: ?>
                
                <?php foreach ($orders as $order): ?>
                    <div class="order-card">
                        <div class="order-header">
                            <div class="order-id">Mã đơn: #<?= $order['id'] ?></div>
                            <div class="order-status"><?= getOrderStatus($order['status']) ?></div>
                        </div>
                        
                        <div class="order-body">
                            <?php
                            // Truy vấn lấy các sản phẩm CHI TIẾT bên trong đơn hàng này
                            $o_id = $order['id'];
                            $sql_details = "SELECT od.*, p.name, p.image 
                                            FROM order_details od 
                                            JOIN products p ON od.product_id = p.id 
                                            WHERE od.order_id = $o_id";
                            $res_details = mysqli_query($conn, $sql_details);
                            
                            while ($item = mysqli_fetch_assoc($res_details)):
                            ?>
                                <div class="order-item">
                                    <?php $img_src = !empty($item['image']) ? 'assets/uploads/' . $item['image'] : 'https://via.placeholder.com/70'; ?>
                                    <img src="<?= htmlspecialchars($img_src) ?>" alt="img">
                                    <div>
                                        <div class="item-name"><?= htmlspecialchars($item['name']) ?></div>
                                        <div class="item-meta">Phân loại: <?= htmlspecialchars($item['variant_name'] ?? 'Mặc định') ?></div>
                                        <div class="item-meta">Số lượng: x<?= $item['quantity'] ?></div>
                                    </div>
                                    <div class="item-price-qty">
                                        <div class="item-price"><?= number_format($item['unit_price'], 0, ',', '.') ?> đ</div>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                        </div>
                        
                        <div class="order-footer">
                            <span class="order-total-label">Thành tiền:</span>
                            <span class="order-total-value"><?= number_format($order['total'], 0, ',', '.') ?> đ</span>
                        </div>
                    </div>
                <?php endforeach; ?>

            <?php endif; ?>
        </div>
    </main>

    <?php include "includes/footer.php"; ?>
</body>
</html>
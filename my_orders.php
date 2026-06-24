<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require_once("includes/connect_db.php");

// Bắt buộc phải đăng nhập mới xem được lịch sử mua hàng
if (!isset($_SESSION['user_client'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_client']['id'];

// ==========================================
// XỬ LÝ YÊU CẦU HỦY ĐƠN HÀNG
// ==========================================
if (isset($_GET['action']) && $_GET['action'] == 'cancel' && isset($_GET['id'])) {
    $cancel_id = (int)$_GET['id'];
    
    // LỚP BẢO MẬT: Kiểm tra xem đơn hàng này có đúng là của User đang đăng nhập không, 
    // và trạng thái có phải là 0 (Chờ duyệt) không. Tránh việc User truyền ID bậy bạ trên URL.
    $check_sql = "SELECT id FROM orders WHERE id = $cancel_id AND user_id = $user_id AND status = 0";
    $check_res = mysqli_query($conn, $check_sql);
    
    if (mysqli_num_rows($check_res) > 0) {
        // Hợp lệ -> Chuyển trạng thái đơn hàng thành 3 (Đã hủy)
        mysqli_query($conn, "UPDATE orders SET status = 3 WHERE id = $cancel_id");
        
        // Tạo thông báo thành công
        $_SESSION['flash_msg'] = "Đã hủy đơn hàng #$cancel_id thành công!";
        header("Location: my_orders.php");
        exit();
    } else {
        // Báo lỗi nếu cố tình hủy đơn đã duyệt hoặc đơn của người khác
        $_SESSION['flash_msg_error'] = "Không thể hủy đơn hàng này!";
        header("Location: my_orders.php");
        exit();
    }
}

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
        case 0: return '<span class="status-badge status-pending"><i class="fa-solid fa-clock"></i> Chờ duyệt</span>';
        case 1: return '<span class="status-badge status-shipping"><i class="fa-solid fa-truck-fast"></i> Đang giao hàng</span>';
        case 2: return '<span class="status-badge status-completed"><i class="fa-solid fa-check"></i> Đã giao thành công</span>';
        case 3: return '<span class="status-badge status-canceled"><i class="fa-solid fa-xmark"></i> Đã hủy</span>';
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
            <h2 class="page-title"><i class="fa-solid fa-clipboard-list"></i> Lịch Sử Đơn Hàng</h2>

            <!-- Khu vực hiển thị thông báo -->
            <?php if (isset($_SESSION['flash_msg'])): ?>
                <div class="toast-msg toast-success">
                    <i class="fa-solid fa-circle-check"></i> <?= $_SESSION['flash_msg'] ?>
                </div>
                <?php unset($_SESSION['flash_msg']); ?>
            <?php endif; ?>
            
            <?php if (isset($_SESSION['flash_msg_error'])): ?>
                <div class="toast-msg toast-error">
                    <i class="fa-solid fa-triangle-exclamation"></i> <?= $_SESSION['flash_msg_error'] ?>
                </div>
                <?php unset($_SESSION['flash_msg_error']); ?>
            <?php endif; ?>

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
                            <div class="order-id">Mã đơn: <strong>#<?= $order['id'] ?></strong></div>
                            <div class="order-status"><?= getOrderStatus($order['status']) ?></div>
                        </div>
                        
                        <div class="order-body">
                            <?php
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
                        
                        <div class="order-footer" style="display: flex; flex-direction: column; align-items: flex-end; gap: 15px;">
                            <div>
                                <span class="order-total-label">Thành tiền:</span>
                                <span class="order-total-value"><?= number_format($order['total'], 0, ',', '.') ?> đ</span>
                            </div>
                            
                            <!-- TUYỆT CHIÊU: Chỉ hiện nút Hủy nếu đơn hàng đang ở trạng thái 0 (Chờ duyệt) -->
                            <?php if ($order['status'] == 0): ?>
                                <div>
                                    <a href="my_orders.php?action=cancel&id=<?= $order['id'] ?>" 
                                       class="btn-cancel-order" 
                                       onclick="return confirm('Bạn có chắc chắn muốn hủy đơn hàng #<?= $order['id'] ?> này không?');">
                                        <i class="fa-solid fa-xmark"></i> Hủy đơn hàng
                                    </a>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>

            <?php endif; ?>
        </div>
    </main>

    <?php include "includes/footer.php"; ?>
</body>
</html>
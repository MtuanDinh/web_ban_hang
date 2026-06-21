<?php
require_once("../includes/check_admin_alive.php");
require_once("../../includes/connect_db.php");
require_once("../../includes/db_helper.php");

// 1. Kiểm tra ID đơn hàng trên URL
if (!isset($_GET['id'])) {
    header("Location: list.php");
    exit();
}
$order_id = (int)$_GET['id'];

// 2. Xử lý Cập nhật trạng thái (Nếu Admin bấm nút Lưu)
if (isset($_POST['btn_update_status'])) {
    $new_status = (int)$_POST['status'];
    updateDb($conn, 'orders', ['status' => $new_status], ['id' => $order_id]);
    
    $_SESSION['flash_msg'] = "Đã cập nhật trạng thái đơn hàng!";
    $_SESSION['msg_type'] = "success";
    header("Location: detail.php?id=" . $order_id); // Load lại trang để thấy thay đổi
    exit();
}

// 3. Lấy thông tin Hóa đơn gốc
$order_data = selectDb($conn, 'orders', '*', ['id' => $order_id]);
if (empty($order_data)) {
    echo "Lỗi: Đơn hàng không tồn tại!";
    exit();
}
$order = $order_data[0];

// 4. Lấy Chi tiết các món hàng (Dùng lệnh SQL JOIN trực tiếp để lấy Tên và Ảnh từ bảng products)
$sql_details = "SELECT od.*, p.name AS product_name, p.image AS product_image 
                FROM order_details od 
                LEFT JOIN products p ON od.product_id = p.id 
                WHERE od.order_id = $order_id";
$result_details = mysqli_query($conn, $sql_details);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/web_ban_hang/admin/assets/css/style_main.css">
    <title>Chi tiết Đơn hàng #<?php echo $order['id']; ?></title>
    <style>
        .invoice_box { background: var(--bg-color, #222); padding: 20px; border-radius: 8px; border: 1px solid var(--border-color, #444); margin-bottom: 20px; }
        .info_grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
        .info_group p { margin: 8px 0; color: var(--text-color, #ddd); }
        .info_group strong { color: var(--primary-color, #fff); }
    </style>
</head>
<body>
    <?php include("../includes/topbar.php"); ?>
    
    <section>
        <?php include("../includes/sidebar.php") ?>
        
        <div id="content">
            <div id="product_head" style="margin-bottom: 20px;">
                <a href="list.php" style="text-decoration: none; color: var(--text-muted); font-size: 14px;">⬅ Quay lại danh sách Đơn hàng</a>
                <h3 style="margin-top: 10px;">🧾 Chi tiết Đơn hàng: <span style="color: var(--primary-color);">#<?php echo $order['id']; ?></span></h3>
            </div>

            <div class="info_grid">
                <div class="invoice_box">
                    <h4 style="border-bottom: 1px solid var(--border-color); padding-bottom: 10px; margin-bottom: 15px;">📍 Thông tin Nhận hàng</h4>
                    <div class="info_group">
                        <p><strong>Người nhận:</strong> <?php echo $order['customer_name']; ?></p>
                        <p><strong>Số điện thoại:</strong> <?php echo $order['customer_phone']; ?></p>
                        <p><strong>Địa chỉ:</strong> <?php echo $order['shipping_address']; ?></p>
                        <p><strong>Ngày đặt:</strong> <?php echo date('H:i:s d/m/Y', strtotime($order['created_at'])); ?></p>
                    </div>
                </div>

                <div class="invoice_box">
                    <h4 style="border-bottom: 1px solid var(--border-color); padding-bottom: 10px; margin-bottom: 15px;">⚙️ Trạng thái Đơn hàng</h4>
                    
                    <form action="" method="post" style="display: flex; gap: 10px; align-items: flex-end;">
                        <div style="flex: 1;">
                            <label for="status">Chuyển trạng thái:</label>
                            <select name="status" id="status" class="form_input" style="width: 100%; height: 40px; margin-top: 5px;">
                                <option value="0" <?php if($order['status'] == 0) echo 'selected'; ?>>⏳ Chờ duyệt</option>
                                <option value="1" <?php if($order['status'] == 1) echo 'selected'; ?>>🚚 Đang giao hàng</option>
                                <option value="2" <?php if($order['status'] == 2) echo 'selected'; ?>>✅ Đã hoàn thành</option>
                                <option value="3" <?php if($order['status'] == 3) echo 'selected'; ?>>❌ Đã hủy</option>
                            </select>
                        </div>
                        <input type="submit" name="btn_update_status" value="Cập nhật" class="add_button" style="height: 40px;">
                    </form>

                    <div style="margin-top: 20px; font-size: 18px;">
                        Tổng thanh toán: <strong style="color: var(--accent-error); font-size: 24px;"><?php echo number_format($order['total'], 0, ',', '.'); ?> đ</strong>
                    </div>
                </div>
            </div>

            <h4 style="margin: 20px 0 10px 0;">🛒 Danh sách Sản phẩm</h4>
            <table id="product_table">
                <thead>
                    <tr>
                        <th>Hình ảnh</th>
                        <th>Tên sản phẩm</th>
                        <th>Đơn giá</th>
                        <th>Số lượng</th>
                        <th>Thành tiền</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($result_details && mysqli_num_rows($result_details) > 0) {
                        while ($item = mysqli_fetch_assoc($result_details)) {
                            // Tính thành tiền của từng dòng (Đơn giá * Số lượng)
                            $subtotal = $item['unit_price'] * $item['quantity'];
                    ?>
                            <tr>
                                <td>
                                    <?php if ($item['product_image']): ?>
                                        <img src="/web_ban_hang/assets/uploads/<?php echo $item['product_image']; ?>" alt="img" style="width: 50px; height: 50px; object-fit: cover; border-radius: 4px;">
                                    <?php else: ?>
                                        <span class="text_muted">No IMG</span>
                                    <?php endif; ?>
                                </td>
                                <td><strong><?php echo $item['product_name'] ? $item['product_name'] : 'Sản phẩm đã bị xóa'; ?></strong></td>
                                
                                <td><?php echo number_format($item['unit_price'], 0, ',', '.'); ?> đ</td>
                                
                                <td><strong>x <?php echo $item['quantity']; ?></strong></td>
                                
                                <td style="color: var(--primary-color); font-weight: bold;">
                                    <?php echo number_format($subtotal, 0, ',', '.'); ?> đ
                                </td>
                            </tr>
                    <?php
                        }
                    } else {
                        echo "<tr><td colspan='5' class='table_no_data'>Đơn hàng này bị lỗi hoặc không có sản phẩm!</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
            
            <?php include("../includes/footer.php") ?>
        </div>
    </section>
</body>
</html>
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
    <script src="https://kit.fontawesome.com/da1a483940.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="/web_ban_hang/admin/assets/css/style_main.css">
    <title>Chi tiết Đơn hàng #<?php echo $order['id']; ?></title>
</head>
<body>
    <?php include("../includes/topbar.php"); ?>
    
    <section>
        <?php include("../includes/sidebar.php") ?>
        
        <div id="content">
            <div class="page-header">
                <a href="list.php" class="btn-back"><i class="fa-solid fa-arrow-left"></i> Quay lại danh sách</a>
                <h3 class="page-title" style="margin-top: 15px;">Chi tiết Đơn hàng: <span style="color: var(--primary-color);">#<?php echo $order['id']; ?></span></h3>
            </div>

            <?php if (isset($_SESSION['flash_msg'])) : ?>
                <div id="toast_msg">
                    <span style="font-weight: 500; display: flex; align-items: center; gap: 10px;">
                        <i class="fa-solid fa-circle-check"></i> <?php echo $_SESSION['flash_msg']; ?>
                    </span>
                    <button onclick="this.parentElement.style.display='none'"
                        style="background: transparent; border: none; color: var(--text-muted); cursor: pointer; font-size: 18px; transition: 0.2s;" 
                        onmouseover="this.style.color='#fff'" onmouseout="this.style.color='var(--text-muted)'">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>
                <?php unset($_SESSION['flash_msg']); unset($_SESSION['msg_type']); ?>
            <?php endif; ?>

            <div class="form-row">
                
                <div class="admin-form-container" style="margin-bottom: 0;">
                    <h4 style="color: var(--primary-color); border-bottom: 1px solid var(--border-admin); padding-bottom: 10px; margin-bottom: 15px;">
                        <i class="fa-solid fa-location-dot"></i> Thông tin Nhận hàng
                    </h4>
                    <div style="line-height: 1.8; color: var(--text-admin); font-size: 14px;">
                        <p><strong style="color: var(--text-muted); display: inline-block; width: 100px;">Người nhận:</strong> <?php echo htmlspecialchars($order['customer_name']); ?></p>
                        <p><strong style="color: var(--text-muted); display: inline-block; width: 100px;">Điện thoại:</strong> <?php echo htmlspecialchars($order['customer_phone']); ?></p>
                        <p><strong style="color: var(--text-muted); display: inline-block; width: 100px;">Địa chỉ:</strong> <?php echo htmlspecialchars($order['shipping_address']); ?></p>
                        <p><strong style="color: var(--text-muted); display: inline-block; width: 100px;">Ngày đặt:</strong> <?php echo date('H:i:s d/m/Y', strtotime($order['created_at'])); ?></p>
                    </div>
                </div>

                <div class="admin-form-container" style="margin-bottom: 0;">
                    <h4 style="color: #ffb74d; border-bottom: 1px solid var(--border-admin); padding-bottom: 10px; margin-bottom: 15px;">
                        <i class="fa-solid fa-truck-fast"></i> Xử lý Đơn hàng
                    </h4>
                    
                    <form action="" method="post" style="display: flex; gap: 10px; align-items: flex-end;">
                        <div class="form-group" style="flex: 1; margin-bottom: 0;">
                            <label for="status">Chuyển trạng thái:</label>
                            <select name="status" id="status" class="form-control" style="cursor: pointer;">
                                <option value="0" <?php if($order['status'] == 0) echo 'selected'; ?>>⏳ Chờ duyệt</option>
                                <option value="1" <?php if($order['status'] == 1) echo 'selected'; ?>>🚚 Đang giao hàng</option>
                                <option value="2" <?php if($order['status'] == 2) echo 'selected'; ?>>✅ Đã hoàn thành</option>
                                <option value="3" <?php if($order['status'] == 3) echo 'selected'; ?>>❌ Đã hủy</option>
                            </select>
                        </div>
                        <button type="submit" name="btn_update_status" class="btn-add-new" style="background: #ffb74d; color: #000; flex: 0 0 120px; justify-content: center; height: 43px;">
                            <i class="fa-solid fa-floppy-disk"></i> Cập nhật
                        </button>
                    </form>

                    <div style="margin-top: 25px; padding-top: 15px; border-top: 1px dashed var(--border-admin); display: flex; justify-content: space-between; align-items: center;">
                        <span style="font-size: 15px; color: var(--text-muted);">Tổng thanh toán:</span>
                        <strong style="color: var(--accent-error); font-size: 24px;"><?php echo number_format($order['total'], 0, ',', '.'); ?> đ</strong>
                    </div>
                </div>
            </div>

            <h4 style="margin: 25px 0 15px 0; color: var(--text-admin); font-size: 18px;"><i class="fa-solid fa-cart-shopping" style="color: #b388ff; margin-right: 8px;"></i> Danh sách Sản phẩm</h4>
            
            <div class="table-container">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th style="width: 80px; text-align: center;">Hình ảnh</th>
                            <th>Tên sản phẩm</th>
                            <th>Đơn giá</th>
                            <th style="text-align: center;">Số lượng</th>
                            <th style="text-align: right;">Thành tiền</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if ($result_details && mysqli_num_rows($result_details) > 0) {
                            while ($item = mysqli_fetch_assoc($result_details)) {
                                $subtotal = $item['unit_price'] * $item['quantity'];
                        ?>
                                <tr>
                                    <td style="text-align: center;">
                                        <?php if ($item['product_image']): ?>
                                            <img src="/web_ban_hang/assets/uploads/<?php echo htmlspecialchars($item['product_image']); ?>" alt="img" class="prod_img_thumb">
                                        <?php else: ?>
                                            <div style="width: 60px; height: 60px; background: rgba(255,255,255,0.05); border-radius: 8px; display: flex; align-items: center; justify-content: center; color: var(--text-muted); font-size: 20px;">
                                                <i class="fa-solid fa-image"></i>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                    
                                    <td>
                                        <strong style="color: var(--primary-color); font-size: 14px;">
                                            <?php echo $item['product_name'] ? htmlspecialchars($item['product_name']) : '<span style="color: var(--accent-error);">Sản phẩm đã bị xóa</span>'; ?>
                                        </strong>
                                    </td>
                                    
                                    <td style="color: var(--text-admin);">
                                        <?php echo number_format($item['unit_price'], 0, ',', '.'); ?> đ
                                    </td>
                                    
                                    <td style="text-align: center;">
                                        <span style="background: rgba(255,255,255,0.05); padding: 4px 12px; border-radius: 6px; border: 1px solid var(--border-admin); font-weight: bold;">
                                            x <?php echo $item['quantity']; ?>
                                        </span>
                                    </td>
                                    
                                    <td style="color: var(--accent-error); font-weight: bold; text-align: right; font-size: 15px;">
                                        <?php echo number_format($subtotal, 0, ',', '.'); ?> đ
                                    </td>
                                </tr>
                        <?php
                            }
                        } else {
                            echo "<tr><td colspan='5' style='text-align: center; padding: 40px; color: var(--text-muted);'>Đơn hàng này bị lỗi hoặc không có sản phẩm!</td></tr>";
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
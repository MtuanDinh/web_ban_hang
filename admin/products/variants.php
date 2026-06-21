<?php
require_once("../includes/check_admin_alive.php");
require_once("../../includes/connect_db.php");
require_once("../../includes/db_helper.php");

// 1. KIỂM TRA BẢO MẬT: Bắt buộc phải có ID sản phẩm truyền vào URL
if (!isset($_GET['id'])) {
    header("Location: list.php");
    exit();
}

$product_id = (int)$_GET['id'];

// 2. LẤY TÊN SẢN PHẨM GỐC ĐỂ LÀM TIÊU ĐỀ
$product_info = selectDb($conn, 'products', 'name', ['id' => $product_id]);
if (empty($product_info)) {
    echo "Lỗi: Sản phẩm gốc không tồn tại!";
    exit();
}
$product_name = $product_info[0]['name'];

// 3. XỬ LÝ KHI BẤM NÚT "THÊM PHIÊN BẢN"
if (isset($_POST['btn_add_variant'])) {
    $color = trim($_POST['color']);
    $version = trim($_POST['version']);
    $price = (int)$_POST['price'];
    $stock = (int)$_POST['stock'];

    // Đóng gói mảng dữ liệu để đẩy vào bảng product_variants
    $variant_data = [
        'product_id' => $product_id,
        'color' => $color,
        'version' => $version,
        'price' => $price,
        'stock' => $stock
    ];

    insertDb($conn, 'product_variants', $variant_data);
    
    $_SESSION['flash_msg'] = "Thêm phiên bản thành công!";
    $_SESSION['msg_type'] = "success";
    
    // Thêm xong thì tải lại chính trang này để hiện dữ liệu mới
    header("Location: variants.php?id=" . $product_id);
    exit();
}

// 4. XỬ LÝ XÓA PHIÊN BẢN (Tích hợp luôn vào file này cho gọn)
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['var_id'])) {
    $del_id = (int)$_GET['var_id'];
    deleteDb($conn, 'product_variants', ['id' => $del_id]);
    
    $_SESSION['flash_msg'] = "Đã xóa phiên bản!";
    $_SESSION['msg_type'] = "success";
    header("Location: variants.php?id=" . $product_id);
    exit();
}

// 5. LẤY DANH SÁCH CÁC PHIÊN BẢN HIỆN CÓ CỦA SẢN PHẨM NÀY
$variants = selectDb($conn, 'product_variants', '*', ['product_id' => $product_id], 'ORDER BY id DESC');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/web_ban_hang/admin/assets/css/style_main.css">
    <title>Quản lý Phiên bản</title>
</head>
<body>
    <?php include("../includes/topbar.php"); ?>
    
    <section>
        <?php include("../includes/sidebar.php") ?>
        
        <div id="content">
            <div id="product_head" style="margin-bottom: 20px;">
                <a href="list.php" style="text-decoration: none; color: var(--text-muted); font-size: 14px;">⬅ Quay lại danh sách Sản phẩm</a>
                <h3 style="margin-top: 10px;">Quản lý Phiên bản: <span style="color: var(--primary-color);"><?php echo $product_name; ?></span></h3>
            </div>

            <div style="padding: 20px; border-radius: 8px; border: 1px solid var(--border-color, #444); margin-bottom: 20px;">
                <h4 style="margin-bottom: 15px;">Thêm phiên bản mới</h4>
                <form action="variants.php?id=<?php echo $product_id; ?>" method="post" style="display: flex; gap: 15px; align-items: flex-end;">
                    
                    <div style="flex: 1;">
                        <label for="color">Màu sắc (VD: Đen Titan) *</label>
                        <input type="text" name="color" required id="color" class="form_input" style="width: 100%;">
                    </div>
                    
                    <div style="flex: 1;">
                        <label for="version">Phiên bản / Kích cỡ</label>
                        <input type="text" name="version" id="version" placeholder="VD: 256GB (Bỏ trống nếu ko có)" class="form_input" style="width: 100%;">
                    </div>

                    <div style="flex: 1;">
                        <label for="price">Giá bán (VNĐ) *</label>
                        <input type="number" name="price" required id="price" min="0" class="form_input" style="width: 100%;">
                    </div>

                    <div style="flex: 1;">
                        <label for="stock">Số lượng Kho *</label>
                        <input type="number" name="stock" required id="stock" min="0" class="form_input" style="width: 100%;">
                    </div>

                    <div>
                        <input type="submit" name="btn_add_variant" value="+ Thêm" class="add_button" style="height: 40px; padding: 0 20px;">
                    </div>
                </form>
            </div>

            <table id="product_table">
                <thead>
                    <tr>
                        <th>Mã PB</th>
                        <th>Màu sắc</th>
                        <th>Phiên bản / Dung lượng</th>
                        <th>Giá bán hiện tại</th>
                        <th>Kho</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if (!empty($variants)) {
                        foreach ($variants as $var) {
                    ?>
                            <tr>
                                <td>#<?php echo $var['id']; ?></td>
                                <td><strong><?php echo $var['color']; ?></strong></td>
                                <td><?php echo $var['version'] ? $var['version'] : '<span class="text_muted">Không phân loại</span>'; ?></td>
                                <td style="color: var(--accent-error); font-weight: bold;">
                                    <?php echo number_format($var['price'], 0, ',', '.'); ?> đ
                                </td>
                                <td><?php echo $var['stock']; ?></td>
                                <td>
                                    <a href="variants.php?id=<?php echo $product_id; ?>&action=delete&var_id=<?php echo $var['id']; ?>"
                                        onclick="return confirm('Xóa vĩnh viễn phiên bản này?')"
                                        class="btn_action delete">Xóa</a>
                                </td>
                            </tr>
                    <?php
                        }
                    } else {
                        echo "<tr><td colspan='6' class='table_no_data'>Sản phẩm này chưa có phiên bản nào! Hãy thêm ở form phía trên.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>

            <?php include("../includes/footer.php") ?>
        </div>
    </section>
</body>
</html>
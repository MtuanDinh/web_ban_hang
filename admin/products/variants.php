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
    header("Location: variants.php?id=" . $product_id);
    exit();
}

// 4. XỬ LÝ KHI BẤM NÚT "CẬP NHẬT PHIÊN BẢN"
if (isset($_POST['btn_update_variant'])) {
    $var_id = (int)$_POST['var_id'];
    $color = trim($_POST['color']);
    $version = trim($_POST['version']);
    $price = (int)$_POST['price'];
    $stock = (int)$_POST['stock'];

    $update_data = [
        'color' => $color,
        'version' => $version,
        'price' => $price,
        'stock' => $stock
    ];

    updateDb($conn, 'product_variants', $update_data, ['id' => $var_id]);
    
    $_SESSION['flash_msg'] = "Cập nhật phiên bản #$var_id thành công!";
    $_SESSION['msg_type'] = "success";
    header("Location: variants.php?id=" . $product_id);
    exit();
}

// 5. XỬ LÝ XÓA PHIÊN BẢN
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['var_id'])) {
    $del_id = (int)$_GET['var_id'];
    deleteDb($conn, 'product_variants', ['id' => $del_id]);
    
    $_SESSION['flash_msg'] = "Đã xóa phiên bản!";
    $_SESSION['msg_type'] = "success";
    header("Location: variants.php?id=" . $product_id);
    exit();
}

// 6. KIỂM TRA XEM CÓ ĐANG Ở CHẾ ĐỘ SỬA KHÔNG (Bắt qua URL edit_var_id)
$edit_variant = null;
if (isset($_GET['edit_var_id'])) {
    $edit_id = (int)$_GET['edit_var_id'];
    $res_edit = selectDb($conn, 'product_variants', '*', ['id' => $edit_id]);
    if (!empty($res_edit)) {
        $edit_variant = $res_edit[0];
    }
}

// 7. LẤY DANH SÁCH CÁC PHIÊN BẢN HIỆN CÓ
$variants = selectDb($conn, 'product_variants', '*', ['product_id' => $product_id], 'ORDER BY id DESC');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://kit.fontawesome.com/da1a483940.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="/web_ban_hang/admin/assets/css/style_main.css">
    <title>Quản lý Phiên bản</title>
</head>
<body>
    <?php include("../includes/topbar.php"); ?>
    
    <section>
        <?php include("../includes/sidebar.php") ?>
        
        <div id="content">
            <div class="page-header">
                <a href="list.php" class="btn-back"><i class="fa-solid fa-arrow-left"></i> Quay lại danh sách</a>
                <h3 class="page-title" style="margin-top: 15px;">Quản lý Phiên bản: <span style="color: #b388ff;"><?php echo htmlspecialchars($product_name); ?></span></h3>
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

            <?php if ($edit_variant): ?>
                
                <div class="admin-form-container" style="padding: 25px; border: 1px solid var(--accent-success); box-shadow: 0 0 15px rgba(0, 230, 118, 0.1);">
                    <h4 style="margin-bottom: 20px; color: var(--accent-success);"><i class="fa-solid fa-pen-to-square"></i> Cập nhật Phiên bản #<?php echo $edit_variant['id']; ?></h4>
                    <form action="variants.php?id=<?php echo $product_id; ?>" method="post" class="form-row" style="margin-bottom: 0; align-items: flex-end;">
                        
                        <input type="hidden" name="var_id" value="<?php echo $edit_variant['id']; ?>">
                        
                        <div class="form-group" style="margin-bottom: 0;">
                            <label for="color">Màu sắc *</label>
                            <input type="text" name="color" required id="color" value="<?php echo htmlspecialchars($edit_variant['color']); ?>" class="form-control">
                        </div>
                        
                        <div class="form-group" style="margin-bottom: 0;">
                            <label for="version">Phiên bản / Kích cỡ</label>
                            <input type="text" name="version" id="version" value="<?php echo htmlspecialchars($edit_variant['version']); ?>" class="form-control">
                        </div>

                        <div class="form-group" style="margin-bottom: 0;">
                            <label for="price">Giá bán (VNĐ) *</label>
                            <input type="number" name="price" required id="price" min="0" value="<?php echo $edit_variant['price']; ?>" class="form-control">
                        </div>

                        <div class="form-group" style="margin-bottom: 0;">
                            <label for="stock">Số lượng Kho *</label>
                            <input type="number" name="stock" required id="stock" min="0" value="<?php echo $edit_variant['stock']; ?>" class="form-control">
                        </div>

                        <div style="flex: 0 0 200px; display: flex; gap: 10px;">
                            <button type="submit" name="btn_update_variant" class="btn-add-new" style="flex: 1; justify-content: center; background: var(--accent-success);">
                                <i class="fa-solid fa-floppy-disk"></i> Lưu
                            </button>
                            <a href="variants.php?id=<?php echo $product_id; ?>" class="btn-reset" style="flex: 1; display: flex; justify-content: center; align-items: center; padding: 0;">
                                <i class="fa-solid fa-xmark" style="margin-right: 5px;"></i> Hủy
                            </a>
                        </div>
                    </form>
                </div>

            <?php else: ?>
                
                <div class="admin-form-container" style="padding: 25px;">
                    <h4 style="margin-bottom: 20px; color: var(--primary-color);"><i class="fa-solid fa-layer-group"></i> Thêm phiên bản mới</h4>
                    <form action="variants.php?id=<?php echo $product_id; ?>" method="post" class="form-row" style="margin-bottom: 0; align-items: flex-end;">
                        
                        <div class="form-group" style="margin-bottom: 0;">
                            <label for="color">Màu sắc (VD: Đen Titan) *</label>
                            <input type="text" name="color" required id="color" class="form-control">
                        </div>
                        
                        <div class="form-group" style="margin-bottom: 0;">
                            <label for="version">Phiên bản / Kích cỡ</label>
                            <input type="text" name="version" id="version" placeholder="VD: 256GB" class="form-control">
                        </div>

                        <div class="form-group" style="margin-bottom: 0;">
                            <label for="price">Giá bán (VNĐ) *</label>
                            <input type="number" name="price" required id="price" min="0" class="form-control">
                        </div>

                        <div class="form-group" style="margin-bottom: 0;">
                            <label for="stock">Số lượng Kho *</label>
                            <input type="number" name="stock" required id="stock" min="0" class="form-control">
                        </div>

                        <div style="flex: 0 0 120px;">
                            <button type="submit" name="btn_add_variant" class="btn-add-new" style="width: 100%; justify-content: center;">
                                <i class="fa-solid fa-plus"></i> Thêm
                            </button>
                        </div>
                    </form>
                </div>

            <?php endif; ?>

            <div class="table-container">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th style="width: 80px;">Mã PB</th>
                            <th>Màu sắc</th>
                            <th>Phiên bản / Dung lượng</th>
                            <th>Giá bán hiện tại</th>
                            <th>Kho</th>
                            <th style="text-align: center;">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if (!empty($variants)) {
                            foreach ($variants as $var) {
                                // Nếu đang ở chế độ Sửa và dòng này là dòng đang sửa, làm nổi bật nó lên
                                $is_editing = ($edit_variant && $edit_variant['id'] == $var['id']);
                                $row_style = $is_editing ? "background: rgba(0, 230, 118, 0.05); border-left: 3px solid var(--accent-success);" : "";
                        ?>
                                <tr style="<?php echo $row_style; ?>">
                                    <td><strong style="color: var(--text-muted);">#<?php echo $var['id']; ?></strong></td>
                                    <td><strong style="color: var(--primary-color);"><?php echo htmlspecialchars($var['color']); ?></strong></td>
                                    <td><?php echo $var['version'] ? htmlspecialchars($var['version']) : '<span style="color: var(--text-muted);"><i class="fa-solid fa-minus"></i> Không phân loại</span>'; ?></td>
                                    <td style="color: var(--accent-error); font-weight: bold; font-size: 15px;">
                                        <?php echo number_format($var['price'], 0, ',', '.'); ?> đ
                                    </td>
                                    <td>
                                        <span style="background: rgba(255,255,255,0.05); padding: 4px 12px; border-radius: 12px; border: 1px solid var(--border-admin);">
                                            <?php echo $var['stock']; ?>
                                        </span>
                                    </td>
                                    <td style="text-align: center;">
                                        <a href="variants.php?id=<?php echo $product_id; ?>&edit_var_id=<?php echo $var['id']; ?>" 
                                           class="action-btn btn-edit" style="margin-right: 5px;">
                                           <i class="fa-solid fa-pen-to-square"></i> Sửa
                                        </a>
                                        
                                        <a href="variants.php?id=<?php echo $product_id; ?>&action=delete&var_id=<?php echo $var['id']; ?>"
                                            onclick="return confirm('Xóa vĩnh viễn phiên bản này?')"
                                            class="action-btn btn-delete">
                                            <i class="fa-solid fa-trash-can"></i> Xóa
                                        </a>
                                    </td>
                                </tr>
                        <?php
                            }
                        } else {
                            echo "<tr><td colspan='6' style='text-align: center; padding: 40px; color: var(--text-muted);'>
                                    <i class='fa-solid fa-cubes-stacked' style='font-size: 40px; margin-bottom: 15px; opacity: 0.5; display: block;'></i>
                                    Sản phẩm này chưa có phiên bản nào! Hãy thêm ở form phía trên.
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
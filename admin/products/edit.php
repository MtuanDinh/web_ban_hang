<?php
require_once("../includes/check_admin_alive.php");
require_once("../../includes/connect_db.php");
require_once("../../includes/db_helper.php");

// 1. Bắt ID sản phẩm cần sửa
if (!isset($_GET['id'])) {
    header("Location: list.php");
    exit();
}
$edit_id = (int)$_GET['id'];

// 2. Lấy dữ liệu sản phẩm hiện tại
$product_data = selectDb($conn, 'products', '*', ['id' => $edit_id]);
if (empty($product_data)) {
    echo "Lỗi: Sản phẩm không tồn tại!";
    exit();
}
$p = $product_data[0];

// 3. Giải nén chuỗi JSON cấu hình thành Mảng PHP
$specs = json_decode($p['description'], true);
if (!is_array($specs)) {
    $specs = []; // Đề phòng trường hợp lỗi JSON thì gán mảng rỗng
}

// Lấy danh sách danh mục để đổ vào thẻ <select>
$categories = selectDb($conn, 'categories', '*', [], 'ORDER BY id ASC');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://kit.fontawesome.com/da1a483940.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="/web_ban_hang/admin/assets/css/style_main.css">
    <title>Sửa Sản Phẩm</title>
</head>
<body>
    <?php include("../includes/topbar.php"); ?>
    
    <section>
        <?php include("../includes/sidebar.php") ?>
        
        <div id="content">
            <div class="page-header">
                <a href="list.php" class="btn-back"><i class="fa-solid fa-arrow-left"></i> Quay lại danh sách</a>
                <h3 class="page-title" style="margin-top: 15px;">Sửa Sản Phẩm: <span style="color: var(--primary-color);"><?php echo htmlspecialchars($p['name']); ?></span></h3>
            </div>

            <div class="admin-form-container">
                <form action="edit_work.php" method="post" enctype="multipart/form-data">
                    
                    <input type="hidden" name="edit_id" value="<?php echo $p['id']; ?>">
                    
                    <h3 style="font-size: 16px; border-bottom: 2px solid var(--border-admin); color: var(--primary-color); padding-bottom: 10px; margin-bottom: 20px;">
                        <i class="fa-solid fa-circle-info"></i> Thông tin cơ bản
                    </h3>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="prod_name">Tên sản phẩm *</label>
                            <input type="text" name="prod_name" required id="prod_name" value="<?php echo htmlspecialchars($p['name']); ?>" class="form-control">
                        </div>
                        
                        <div class="form-group">
                            <label for="category_id">Danh mục / Nhãn hiệu *</label>
                            <select name="category_id" id="category_id" required class="form-control">
                                <?php
                                if (!empty($categories)) {
                                    foreach ($categories as $cat) {
                                        $prefix = ($cat['parent_id'] != null) ? " --- " : "";
                                        $selected = ($cat['id'] == $p['category_id']) ? "selected" : "";
                                        echo "<option value='{$cat['id']}' {$selected}>{$prefix}{$cat['name']}</option>";
                                    }
                                }
                                ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>Ảnh Bìa Hiện Tại</label>
                            <div style="margin-bottom: 10px; background: rgba(0,0,0,0.2); padding: 10px; border-radius: 8px; border: 1px dashed var(--border-admin); display: inline-block;">
                                <?php if($p['image']): ?>
                                    <img src="/web_ban_hang/assets/uploads/<?php echo htmlspecialchars($p['image']); ?>" style="width: 80px; height: 80px; object-fit: contain; background: #fff; padding: 5px; border-radius: 5px;">
                                <?php else: ?>
                                    <span style="color: var(--accent-error);"><i class="fa-solid fa-image"></i> Chưa có ảnh</span>
                                <?php endif; ?>
                            </div>
                            <label for="prod_img">Chọn ảnh mới (Bỏ trống nếu muốn giữ ảnh cũ)</label>
                            <input type="file" name="prod_img" id="prod_img" accept="image/png, image/jpeg, image/jpg" class="form-control" style="padding: 9px 15px;">
                        </div>
                        
                        <div class="form-group">
                            <label>Bộ ảnh chi tiết (Bỏ trống để giữ bộ ảnh cũ)</label>
                            <input type="file" name="gallery[]" id="gallery" multiple accept="image/png, image/jpeg, image/jpg" class="form-control" style="padding: 9px 15px;">
                            <small style="color: var(--accent-error); display: block; margin-top: 8px;"><i class="fa-solid fa-triangle-exclamation"></i> Nếu bạn tải ảnh mới lên, toàn bộ ảnh chi tiết cũ sẽ bị xóa.</small>
                        </div>
                    </div>
                    
                    <div style="margin-top: 20px; padding: 25px; border: 1px dashed var(--border-admin); border-radius: 12px; background: rgba(0,0,0,0.1);">
                        <label style="font-size: 16px; color: var(--text-admin); margin-bottom: 15px; display: block;">
                            <i class="fa-solid fa-microchip" style="color: #b388ff;"></i> Chỉnh sửa Thông số kỹ thuật
                        </label>
                        
                        <div id="dynamic_specs">
                            <?php if (!empty($specs)): ?>
                                <?php foreach ($specs as $key => $value): ?>
                                    <div class="spec-row" style="display: flex; gap: 15px; margin-bottom: 15px;">
                                        <input type="text" name="spec_keys[]" value="<?= htmlspecialchars($key) ?>" placeholder="Tên thông số..." class="form-control" style="flex: 1;">
                                        <input type="text" name="spec_values[]" value="<?= htmlspecialchars($value) ?>" placeholder="Giá trị..." class="form-control" style="flex: 2;">
                                        <button type="button" onclick="removeSpec(this)" class="action-btn btn-delete" style="padding: 0 20px;"><i class="fa-solid fa-trash-can"></i> Xóa</button>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class="spec-row" style="display: flex; gap: 15px; margin-bottom: 15px;">
                                    <input type="text" name="spec_keys[]" placeholder="Tên thông số (VD: CPU, Công suất...)" class="form-control" style="flex: 1;">
                                    <input type="text" name="spec_values[]" placeholder="Giá trị (VD: Apple A17, 20W...)" class="form-control" style="flex: 2;">
                                    <button type="button" onclick="removeSpec(this)" class="action-btn btn-delete" style="padding: 0 20px;"><i class="fa-solid fa-trash-can"></i> Xóa</button>
                                </div>
                            <?php endif; ?>
                        </div>

                        <button type="button" onclick="addSpec()" class="action-btn" style="background: rgba(0, 230, 118, 0.1); color: var(--accent-success); border: 1px dashed var(--accent-success); padding: 10px 20px; margin-top: 10px;">
                            <i class="fa-solid fa-plus"></i> Thêm thông số khác
                        </button>
                    </div>

                    <script>
                        function addSpec() {
                            var newRow = `
                                <div class="spec-row" style="display: flex; gap: 15px; margin-bottom: 15px; animation: slideIn 0.3s ease;">
                                    <input type="text" name="spec_keys[]" placeholder="Tên thông số..." class="form-control" style="flex: 1;">
                                    <input type="text" name="spec_values[]" placeholder="Giá trị..." class="form-control" style="flex: 2;">
                                    <button type="button" onclick="removeSpec(this)" class="action-btn btn-delete" style="padding: 0 20px;"><i class="fa-solid fa-trash-can"></i> Xóa</button>
                                </div>
                            `;
                            document.getElementById('dynamic_specs').insertAdjacentHTML('beforeend', newRow);
                        }

                        function removeSpec(buttonElement) {
                            buttonElement.parentElement.remove();
                        }
                    </script>
                    
                    <button type="submit" name="btn_edit" class="btn-add-new" style="margin-top: 30px; width: 100%; justify-content: center; font-size: 16px; padding: 15px;">
                        <i class="fa-solid fa-floppy-disk"></i> LƯU THAY ĐỔI
                    </button>
                </form>
            </div>
            
            <?php include("../includes/footer.php") ?>
        </div>
    </section>
</body>
</html>
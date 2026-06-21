<?php
    $categories = selectDb($conn, 'categories', '*', [], 'ORDER BY id ASC');
?>

<div class="modal-container" id="modal">
    <div class="modal" style="width: 700px; max-height: 90vh; overflow-y: auto;"> 
        <button class="close_btn" id="close">X</button>
        <div class="modal_content">
            <h1 style="margin-bottom: 20px;">Thêm sản phẩm mới</h1>
            
            <form action="add_work.php" class="modal_form" method="post" enctype="multipart/form-data">
                
                <h3 style="font-size: 16px; border-bottom: 2px solid var(--border-color); padding-bottom: 5px; margin-bottom: 15px;">1. Thông tin cơ bản</h3>
                
                <div style="display: flex; gap: 15px;">
                    <div style="flex: 1;">
                        <label for="prod_name">Tên sản phẩm *</label>
                        <input type="text" name="prod_name" required id="prod_name" placeholder="VD: iPhone 15 Pro Max" class="form_input">
                    </div>
                    
                    <div style="flex: 1;">
                        <label for="category_id">Danh mục / Nhãn hiệu *</label>
                        <select name="category_id" id="category_id" required class="form_input" style="width: 100%; height: 40px;">
                            <option value="">-- Chọn danh mục --</option>
                            <?php
                            if (!empty($categories)) {
                                foreach ($categories as $cat) {
                                    $prefix = ($cat['parent_id'] != null) ? " --- " : "";
                                    echo "<option value='{$cat['id']}'>{$prefix}{$cat['name']}</option>";
                                }
                            }
                            ?>
                        </select>
                    </div>
                </div>

                

                <div style="display: flex; gap: 15px; margin-top: 15px; margin-bottom: 25px;">
                    <div style="flex: 1;">
                        <label for="prod_img">Ảnh Bìa (Hiển thị ở danh sách) *</label>
                        <input type="file" name="prod_img" required id="prod_img" accept="image/png, image/jpeg, image/jpg" class="form_input" style="padding: 8px;">
                    </div>
                    
                    <div style="flex: 1;">
                        <label for="gallery">Bộ ảnh chi tiết (Có thể chọn nhiều ảnh)</label>
                        <input type="file" name="gallery[]" id="gallery" multiple accept="image/png, image/jpeg, image/jpg" class="form_input" style="padding: 8px;">
                        <small style="color: var(--text-muted); display: block; margin-top: 5px;">* Giữ phím Ctrl để chọn nhiều ảnh cùng lúc.</small>
                    </div>
                </div>
                
                <h3 style="font-size: 16px; border-bottom: 2px solid var(--border-color); padding-bottom: 5px; margin-bottom: 15px;">2. Cấu hình chi tiết</h3>
                
                <div style="display: flex; gap: 15px;">
                    <div style="flex: 1;">
                        <label>Hệ điều hành</label>
                        <input type="text" name="specs[os]" placeholder="VD: iOS 17" class="form_input">
                    </div>
                    <div style="flex: 1;">
                        <label>CPU (Chip xử lý)</label>
                        <input type="text" name="specs[cpu]" placeholder="VD: Apple A17 Pro" class="form_input">
                    </div>
                </div>

                <div style="display: flex; gap: 15px; margin-top: 15px;">
                    <div style="flex: 1;">
                        <label>Màn hình</label>
                        <input type="text" name="specs[screen]" placeholder="VD: 6.7 inch, Super Retina XDR" class="form_input">
                    </div>
                    <div style="flex: 1;">
                        <label>Camera</label>
                        <input type="text" name="specs[camera]" placeholder="VD: Chính 48MP, Phụ 12MP" class="form_input">
                    </div>
                </div>

                <div style="display: flex; gap: 15px; margin-top: 15px;">
                    <div style="flex: 1;">
                        <label>RAM</label>
                        <input type="text" name="specs[ram]" placeholder="VD: 8 GB" class="form_input">
                    </div>
                    <div style="flex: 1;">
                        <label>Bộ nhớ trong (ROM)</label>
                        <input type="text" name="specs[rom]" placeholder="VD: 256 GB" class="form_input">
                    </div>
                    <div style="flex: 1;">
                        <label>Dung lượng Pin</label>
                        <input type="text" name="specs[battery]" placeholder="VD: 4422 mAh, 20W" class="form_input">
                    </div>
                </div>

                <div style="display: flex; gap: 15px; margin-top: 15px;">
                    <div style="flex: 1;">
                        <label>Loại SIM</label>
                        <input type="text" name="specs[sim]" placeholder="VD: 1 Nano SIM & 1 eSIM" class="form_input">
                    </div>
                    <div style="flex: 1;">
                        <label>Mạng di động</label>
                        <input type="text" name="specs[network]" placeholder="VD: Hỗ trợ 5G" class="form_input">
                    </div>
                    <div style="flex: 1;">
                        <label>Cổng sạc & Giao tiếp khác</label>
                        <input type="text" name="specs[port]" placeholder="VD: Type-C, Wi-Fi 6, Bluetooth 5.3" class="form_input">
                    </div>
                </div>
                <input type="submit" name="btn_add" value="Thêm sản phẩm" class="add_button" style="margin-top: 25px; width: 100%; height: 45px; font-size: 16px;">
            </form>
        </div>
    </div>
</div>
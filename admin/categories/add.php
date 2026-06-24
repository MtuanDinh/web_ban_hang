<div class="modal-container" id="modal">
    <div class="modal">
        <button class="close_btn" id="close"><i class="fa-solid fa-xmark"></i></button>
        <div class="modal_content">
            
            <h3 style="color: var(--primary-color);"><i class="fa-solid fa-folder-plus"></i> Thêm danh mục</h3>
            
            <form action="list.php" class="modal_form" method="post">
                <div>
                    <label for="name">Tên danh mục *</label>
                    <input
                        type="text"
                        name="cate_name"
                        required
                        id="name"
                        placeholder="VD: Apple, Samsung..."
                        class="form_input">
                </div>
                <div>
                    <label for="parent_sel">Chọn danh mục cha</label>
                    <select name="parent_sel" id="parent_sel" class="form_input">
                        <option value="">--- Bỏ trống nếu là danh mục gốc ---</option>
                        <?php
                        if (!empty($res)) {
                            foreach($res as $parent) {
                                echo "<option value='{$parent['id']}'>{$parent['name']}</option>";
                            }
                        }
                        ?>
                    </select>
                </div>
                
                <button type="submit" name="btn_add" class="add_button">
                    <i class="fa-solid fa-plus"></i> Thêm Mới
                </button>
            </form>
        </div>
    </div>
</div>
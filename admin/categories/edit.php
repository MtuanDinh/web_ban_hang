<?php
$edit_parents = selectDb($conn, 'categories', 'id, name', ['parent_id' => null]);
?>

<div class="modal-container" id="editModal">
    <div class="modal">
        <button class="close_btn" type="button" onclick="closeEditModal()"><i class="fa-solid fa-xmark"></i></button>
        <div class="modal_content">
            
            <h3 style="color: var(--accent-success);"><i class="fa-solid fa-pen-to-square"></i> Sửa danh mục</h3>
            
            <form action="edit_work.php" class="modal_form" method="post">
                
                <input type="hidden" name="edit_id" id="edit_id_input">

                <div>
                    <label for="edit_name_input">Tên danh mục *</label>
                    <input type="text" name="cate_name" required id="edit_name_input" class="form_input">
                </div>
                <div>
                    <label for="edit_parent_select">Chọn danh mục cha</label>
                    <select name="parent_sel" id="edit_parent_select" class="form_input">
                        <option value="">--- Bỏ trống nếu là danh mục gốc ---</option>
                        <?php
                        if (!empty($edit_parents)) {
                            foreach ($edit_parents as $parent) {
                                echo "<option value='{$parent['id']}'>{$parent['name']}</option>";
                            }
                        }
                        ?>
                    </select>
                </div>
                
                <button type="submit" name="btn_edit" class="add_button" style="background-color: var(--accent-success);">
                    <i class="fa-solid fa-floppy-disk"></i> Cập Nhật
                </button>
            </form>
        </div>
    </div>
</div>
<?php
$edit_parents = selectDb($conn, 'categories', 'id, name', ['parent_id' => null]);
?>

<div class="modal-container" id="editModal">
    <div class="modal">
        <button class="close_btn" type="button" onclick="closeEditModal()">X</button>
        <div class="modal_content">
            <h1>Sửa danh mục</h1>
            <form action="edit_work.php" class="modal_form" method="post">
                
                <input type="hidden" name="edit_id" id="edit_id_input">

                <div>
                    <label for="edit_name">Tên danh mục</label>
                    <input type="text" name="cate_name" required id="edit_name_input" class="form_input">
                </div>
                <div>
                    <label for="edit_parent">Chọn danh mục cha</label>
                    <select name="parent_sel" id="edit_parent_select">
                        <option value="">Bỏ trống nếu là danh mục cha</option>
                        <?php
                        if (!empty($edit_parents)) {
                            foreach ($edit_parents as $parent) {
                                echo "<option value='{$parent['id']}'>{$parent['name']}</option>";
                            }
                        }
                        ?>
                    </select>
                </div>
                <input type="submit" name="btn_edit" value="Cập nhật" class="add_button" style="background-color: var(--primary-color);">
            </form>
        </div>
    </div>
</div>
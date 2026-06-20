<div class="modal-container" id="modal">
    <div class="modal">
        <button class="close_btn" id="close">X</button>
        <div class="modal_content">
            <h1>Thêm danh mục</h1>
            <form action="list.php" class="modal_form" method="post">
                <div>
                    <label for="name">Tên danh mục</label>
                    <input
                        type="text"
                        name="cate_name"
                        required
                        id="name"
                        placeholder="Nhập tên danh muc(vd: Apple, Samsung,...)"
                        class="form_input">
                </div>
                <div>
                    <label for="parent_sel">Chọn danh mục cha</label>
                    <select name="parent_sel" id="parent_sel">
                        <option value="">Bỏ trống nếu là danh mục cha</option>
                        <?php
                        if (!empty($res)) {
                            foreach($res as $parent) {
                                echo "<option value='{$parent['id']}'>{$parent['name']}</option>";
                            }
                        }
                        ?>
                    </select>
                </div>
                <input type="submit" name="btn_add" value="Thêm" class="add_button">
            </form>
        </div>
    </div>
</div>


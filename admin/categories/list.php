<?php
require_once("../includes/check_admin_alive.php");
require_once("../../includes/connect_db.php");
require_once("../../includes/db_helper.php");
require_once("add_work.php");
require_once("edit_work.php");

$parents = selectDb($conn, 'categories', 'id, name', ['parent_id' => null]);

$where_conditions = [];
$where_clause = "";
$sel_parent = "";
$search_kword = "";

if (isset($_GET['filter_parent']) && $_GET['filter_parent'] != "") {
    $sel_parent = (int)$_GET['filter_parent'];
    $where_conditions[] = " c1.parent_id = {$sel_parent} ";
}

if (isset($_GET['search_keyword']) && trim($_GET['search_keyword']) != "") {
    $search_kword = trim($_GET['search_keyword']);
    $safe_kword = mysqli_real_escape_string($conn, $search_kword);
    $where_conditions[] = " c1.name LIKE '%{$safe_kword}%'";
}
if (count($where_conditions) > 0) {
    $where_clause = " WHERE " . implode(" AND ", $where_conditions);
}

$sql_query = "SELECT c1.id, c1.name, c1.parent_id, c2.name AS parent_name
                    FROM categories c1
                    LEFT JOIN categories c2 ON c1.parent_id = c2.id
                    $where_clause
                    ORDER BY c1.id DESC";
$result = mysqli_query($conn, $sql_query);

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/web_ban_hang/admin/assets/css/style_main.css">
    <title>Quản lý danh mục</title>

</head>

<body>
    <?php
    include("../includes/topbar.php");
    ?>
    <section>
        <?php include("../includes/sidebar.php") ?>
        <div id="content">
            <div id="category_head">
                <h3>Quản lý danh mục</h3>
                <div class='sel_filter'>
                    <form method="get" action="list.php">
                        <select name="filter_parent" id="filter_parent" onchange="this.form.submit()">
                            <option value="">--Chọn danh mục cha--</option>
                            <?php
                            if (!empty($parents)) {
                                foreach ($parents as $parent) {
                                    $is_selected = ($sel_parent == $parent['id']) ? "selected" : "";
                                    echo "<option value='{$parent['id']}' {$is_selected}>Hiển thị: {$parent['name']}</option>";
                                }
                            }
                            ?>
                        </select>
                        <input type="text" name="search_keyword" placeholder="Tìm kiếm danh mục..."
                            value=<?php echo isset($_GET['search_keyword']) ? $_GET['search_keyword'] : ''; ?>>
                        <button type="submit">Tìm</button>
                        <button type="button" onclick="window.location.href='list.php'">X</button>
                    </form>
                </div>
                <button id="open">Thêm danh mục mới</button>
            </div>
            <?php if (isset($_SESSION['flash_msg'])) : ?>
                <?php
                $is_error = (isset($_SESSION['msg_type']) && $_SESSION['msg_type'] == 'error');
                $toast_color = $is_error ? 'var(--accent-error)' : 'var(--accent-success)';
                $toast_icon = $is_error ? '⚠' : '✓';
                ?>
                <div id="toast_msg">

                    <span style="font-weight: bold; color: <?php echo $toast_color; ?>;">
                        <?php echo $toast_icon; ?> <?php echo $_SESSION['flash_msg']; ?>
                    </span>

                    <button onclick="this.parentElement.style.display='none'"
                        style="background: transparent; border: none; color: var(--text-muted); cursor: pointer; font-size: 16px;">X</button>
                </div>

                <?php
                unset($_SESSION['flash_msg']);
                unset($_SESSION['msg_type']);
                ?>
            <?php endif; ?>
            <table id="category_table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Tên danh mục</th>
                        <th>Danh mục cha</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if (mysqli_num_rows($result) > 0) {
                        while ($row = mysqli_fetch_assoc($result)) {
                    ?>
                            <tr>
                                <td><?php echo $row['id']; ?></td>
                                <td><?php echo $row['name']; ?></td>
                                <td>
                                    <?php
                                    if ($row['parent_name'] == null) {
                                        echo "Danh mục gốc";
                                    } else {
                                        echo $row['parent_name'];
                                    }
                                    ?>
                                </td>
                                <td>
                                    <button
                                        class="edit_btn" type="button"
                                        data-id="<?php echo $row['id'] ?>"
                                        data-name="<?php echo $row['name'] ?>"
                                        data-parent="<?php echo $row['parent_id'] == null ? '' : $row['parent_id'] ?>"
                                        onclick="openEditModal(this)">Sửa</button>
                                    <a class="del_btn" href="delete.php?id=<?php echo $row['id']; ?>"
                                        onclick="return confirm('Sản phẩm bị xóa sẽ không thể khôi phục, bạn chắc chứ?')">Xóa</a>
                                </td>
                            </tr>
                    <?php
                        }
                    }
                    ?>
                </tbody>
            </table>
            <?php include("../includes/footer.php") ?>
        </div>
    </section>

    <?php include "add.php"; ?>
    <?php include "edit.php"; ?>
</body>

<script src="/web_ban_hang/admin/assets/js/category.js"></script>

</html>
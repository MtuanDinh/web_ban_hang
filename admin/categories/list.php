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
    <script src="https://kit.fontawesome.com/da1a483940.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="/web_ban_hang/admin/assets/css/style_main.css">
    <title>Quản lý danh mục</title>
</head>

<body>
    <?php include("../includes/topbar.php"); ?>
    <section>
        <?php include("../includes/sidebar.php") ?>
        <div id="content">
            
            <div class="page-header" style="display: flex; justify-content: space-between; align-items: center;">
                <h3 class="page-title">Quản lý danh mục</h3>
                <button id="open" class="btn-add-new"><i class="fa-solid fa-folder-plus"></i> Thêm danh mục mới</button>
            </div>

            <?php if (isset($_SESSION['flash_msg'])) : ?>
                <?php
                $is_error = (isset($_SESSION['msg_type']) && $_SESSION['msg_type'] == 'error');
                $toast_color = $is_error ? 'var(--accent-error)' : 'var(--accent-success)';
                $toast_icon = $is_error ? '<i class="fa-solid fa-circle-exclamation"></i>' : '<i class="fa-solid fa-circle-check"></i>';
                ?>
                <div id="toast_msg" style="border-left-color: <?php echo $toast_color; ?>;">
                    <span style="font-weight: 500; display: flex; align-items: center; gap: 10px; color: <?php echo $toast_color; ?>;">
                        <?php echo $toast_icon; ?> <?php echo $_SESSION['flash_msg']; ?>
                    </span>
                    <button onclick="this.parentElement.style.display='none'"
                        style="background: transparent; border: none; color: var(--text-muted); cursor: pointer; font-size: 18px; transition: 0.2s;" 
                        onmouseover="this.style.color='#fff'" onmouseout="this.style.color='var(--text-muted)'">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>
                <?php unset($_SESSION['flash_msg']); unset($_SESSION['msg_type']); ?>
            <?php endif; ?>

            <div class="action-bar">
                <form method="get" action="list.php">
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <i class="fa-solid fa-filter" style="color: var(--text-muted);"></i>
                        <select name="filter_parent" id="filter_parent" onchange="this.form.submit()">
                            <option value="">-- Tất cả danh mục cha --</option>
                            <?php
                            if (!empty($parents)) {
                                foreach ($parents as $parent) {
                                    $is_selected = ($sel_parent == $parent['id']) ? "selected" : "";
                                    echo "<option value='{$parent['id']}' {$is_selected}>Nhóm: {$parent['name']}</option>";
                                }
                            }
                            ?>
                        </select>
                    </div>

                    <div style="display: flex; align-items: center; gap: 10px; flex: 1; max-width: 400px; position: relative;">
                        <input type="text" name="search_keyword" placeholder="Nhập tên danh mục cần tìm..."
                            value="<?php echo isset($_GET['search_keyword']) ? htmlspecialchars($_GET['search_keyword']) : ''; ?>" style="width: 100%;">
                    </div>

                    <button type="submit" class="btn-filter"><i class="fa-solid fa-magnifying-glass"></i> Tìm</button>
                    
                    <?php if(isset($_GET['filter_parent']) || isset($_GET['search_keyword'])): ?>
                        <a href="list.php" class="btn-reset" title="Xóa bộ lọc"><i class="fa-solid fa-rotate-left"></i></a>
                    <?php endif; ?>
                </form>
            </div>

            <div class="table-container">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th style="width: 80px;">ID</th>
                            <th>Tên danh mục</th>
                            <th>Trực thuộc (Danh mục cha)</th>
                            <th style="width: 180px; text-align: center;">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if (mysqli_num_rows($result) > 0) {
                            while ($row = mysqli_fetch_assoc($result)) {
                        ?>
                                <tr>
                                    <td><strong style="color: var(--text-muted);">#<?php echo $row['id']; ?></strong></td>
                                    
                                    <td style="font-weight: 500; color: var(--primary-color);">
                                        <?php echo htmlspecialchars($row['name']); ?>
                                    </td>
                                    
                                    <td>
                                        <?php
                                        if ($row['parent_name'] == null) {
                                            // Nhãn cho Danh mục gốc (Cấp 1)
                                            echo "<span style='background: rgba(255,255,255,0.05); padding: 4px 10px; border-radius: 4px; font-size: 12px; color: var(--text-muted); border: 1px solid var(--border-admin);'><i class='fa-solid fa-layer-group' style='margin-right: 5px;'></i> Danh mục gốc</span>";
                                        } else {
                                            // Biểu tượng nhánh cho danh mục con
                                            echo "<span style='color: var(--text-muted);'><i class='fa-solid fa-turn-up fa-rotate-90' style='margin-right: 5px; opacity: 0.5;'></i> " . htmlspecialchars($row['parent_name']) . "</span>";
                                        }
                                        ?>
                                    </td>
                                    
                                    <td style="text-align: center;">
                                        <button class="action-btn btn-edit" type="button"
                                            data-id="<?php echo $row['id'] ?>"
                                            data-name="<?php echo htmlspecialchars($row['name']) ?>"
                                            data-parent="<?php echo $row['parent_id'] == null ? '' : $row['parent_id'] ?>"
                                            onclick="openEditModal(this)"><i class="fa-solid fa-pen-to-square"></i> Sửa</button>
                                            
                                        <a class="action-btn btn-delete" href="delete.php?id=<?php echo $row['id']; ?>"
                                            onclick="return confirm('CẢNH BÁO: Bạn có chắc chắn muốn xóa danh mục này? Mọi sản phẩm đang liên kết với danh mục này cũng có thể bị ảnh hưởng.')"><i class="fa-solid fa-trash-can"></i> Xóa</a>
                                    </td>
                                </tr>
                        <?php
                            }
                        } else {
                            echo "<tr><td colspan='4' style='text-align: center; padding: 40px; color: var(--text-muted);'>
                                    <i class='fa-solid fa-folder-open' style='font-size: 40px; margin-bottom: 15px; opacity: 0.5; display: block;'></i>
                                    Chưa có danh mục nào được tạo.
                                  </td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
            
            <?php include("../includes/footer.php") ?>
        </div>
    </section>

    <?php include "add.php"; ?>
    <?php include "edit.php"; ?>
</body>

<script src="/web_ban_hang/admin/assets/js/category.js"></script>

</html>
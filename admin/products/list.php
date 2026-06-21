<?php
require_once("../includes/check_admin_alive.php");
require_once("../../includes/connect_db.php");
require_once("../../includes/db_helper.php");

$sql_product = "SELECT p.*, 
                           c1.name AS brand_name, 
                           c2.name AS cate_name
                    FROM products p
                    LEFT JOIN categories c1 ON p.category_id = c1.id
                    LEFT JOIN categories c2 ON c1.parent_id = c2.id
                    ORDER BY p.id DESC";

$result = mysqli_query($conn, $sql_product);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/web_ban_hang/admin/assets/css/style_main.css">
    <title>Quản lý sản phẩm</title>
</head>

<body>
    <?php
    include("../includes/topbar.php");
    ?>
    <section>
        <?php include("../includes/sidebar.php") ?>
        <div id="content">
            <div id="product_head">
                <h3>Quản lý sản phẩm</h3>
                <button id="open">Thêm sản phẩm mới</button>
            </div>

            <table id="product_table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Danh mục</th>
                        <th>Nhãn hiệu</th>
                        <th>Tên Sản phẩm gốc</th>
                        <th>Hình ảnh</th>
                        <th>Mô tả / Cấu hình</th>
                        <th>Thao tác</th> </tr>
                </thead>
                <tbody>
                    <?php
                    if ($result && mysqli_num_rows($result) > 0) {
                        while ($row = mysqli_fetch_assoc($result)) {
                    ?>
                            <tr>
                                <td><?php echo $row['id']; ?></td>
                                <td><?php echo $row['cate_name'] ? $row['cate_name'] : $row['brand_name']; ?></td>
                                <td><?php echo $row['cate_name'] ? $row['brand_name'] : 'Không có'; ?></td>
                                <td><strong><?php echo $row['name']; ?></strong></td>

                                <td>
                                    <?php if ($row['image']): ?>
                                        <img src="/web_ban_hang/assets/uploads/<?php echo $row['image']; ?>" alt="Product Img" class="prod_img_thumb">
                                    <?php else: ?>
                                        <span class="text_muted">Chưa có ảnh</span>
                                    <?php endif; ?>
                                </td>

                                <td class="prod_desc_cell"><?php echo $row['description']; ?></td>

                                <td>
                                    <a href="variants.php?id=<?php echo $row['id']; ?>" class="btn_action manage" style="background-color: var(--accent-info, #17a2b8); color: white; margin-right: 5px;">Phiên bản</a>
                                    
                                    <a href="edit.php?id=<?php echo $row['id']; ?>" class="btn_action edit" style="text-decoration: none;">Sửa</a>
                                    
                                    <a href="delete.php?id=<?php echo $row['id']; ?>"
                                        onclick="return confirm('XÓA CẢNH BÁO: Xóa sản phẩm gốc sẽ xóa toàn bộ các phiên bản màu sắc đi kèm! Bạn chắc chắn chứ?')"
                                        class="btn_action delete">Xóa</a>
                                </td>
                            </tr>
                    <?php
                        }
                    } else {
                        echo "<tr><td colspan='7' class='table_no_data'>Chưa có sản phẩm nào trong hệ thống!</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
            <?php include("../includes/footer.php") ?>
        </div>
    </section>
    <?php include "add.php"; ?>
    <script src="/web_ban_hang/admin/assets/js/product.js"></script>
</body>

</html>
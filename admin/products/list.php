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
    <script src="https://kit.fontawesome.com/da1a483940.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="/web_ban_hang/admin/assets/css/style_main.css">
    <title>Quản lý sản phẩm</title>
</head>

<body>
    <?php include("../includes/topbar.php"); ?>
    
    <section>
        <?php include("../includes/sidebar.php") ?>
        
        <div id="content">
            
            <div class="page-header" style="display: flex; justify-content: space-between; align-items: center;">
                <h3 class="page-title">Quản lý sản phẩm</h3>
                <button id="open" class="btn-add-new"><i class="fa-solid fa-box-open"></i> Thêm sản phẩm mới</button>
            </div>

            <div class="table-container">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th style="width: 60px;">ID</th>
                            <th style="width: 80px; text-align: center;">Hình ảnh</th>
                            <th style="width: 250px;">Tên Sản phẩm gốc</th>
                            <th>Phân loại</th>
                            <th>Mô tả / Cấu hình</th>
                            <th style="width: 250px; text-align: center;">Thao tác</th> 
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if ($result && mysqli_num_rows($result) > 0) {
                            while ($row = mysqli_fetch_assoc($result)) {
                        ?>
                                <tr>
                                    <td><strong style="color: var(--text-muted);">#<?php echo $row['id']; ?></strong></td>
                                    
                                    <td style="text-align: center;">
                                        <?php if ($row['image']): ?>
                                            <img src="/web_ban_hang/assets/uploads/<?php echo htmlspecialchars($row['image']); ?>" alt="Product Img" class="prod_img_thumb">
                                        <?php else: ?>
                                            <div style="width: 60px; height: 60px; background: rgba(255,255,255,0.05); border-radius: 8px; display: flex; align-items: center; justify-content: center; color: var(--text-muted); font-size: 20px;">
                                                <i class="fa-solid fa-image"></i>
                                            </div>
                                        <?php endif; ?>
                                    </td>

                                    <td><strong style="color: var(--primary-color); font-size: 15px;"><?php echo htmlspecialchars($row['name']); ?></strong></td>

                                    <td>
                                        <div style="display: flex; flex-direction: column; gap: 5px;">
                                            <span style="font-size: 12px; color: var(--text-muted);"><i class="fa-solid fa-layer-group" style="margin-right: 5px;"></i> Nhóm: <strong style="color: var(--text-admin);"><?php echo $row['cate_name'] ? htmlspecialchars($row['cate_name']) : htmlspecialchars($row['brand_name']); ?></strong></span>
                                            <span style="font-size: 12px; color: var(--text-muted);"><i class="fa-solid fa-tag" style="margin-right: 5px;"></i> Hãng: <strong style="color: var(--text-admin);"><?php echo $row['cate_name'] ? htmlspecialchars($row['brand_name']) : '<span style="opacity: 0.5;">Không có</span>'; ?></strong></span>
                                        </div>
                                    </td>

                                    <td class="prod_desc_cell" title="<?php echo htmlspecialchars($row['description']); ?>">
                                        <?php echo htmlspecialchars($row['description']); ?>
                                    </td>

                                    <td style="text-align: center;">
                                        <a href="variants.php?id=<?php echo $row['id']; ?>" class="action-btn btn-variants"><i class="fa-solid fa-cubes-stacked"></i> Phiên bản</a>
                                        
                                        <a href="edit.php?id=<?php echo $row['id']; ?>" class="action-btn btn-edit"><i class="fa-solid fa-pen-to-square"></i> Sửa</a>
                                        
                                        <a href="delete.php?id=<?php echo $row['id']; ?>"
                                            onclick="return confirm('XÓA CẢNH BÁO: Xóa sản phẩm gốc sẽ xóa toàn bộ các phiên bản màu sắc đi kèm! Bạn chắc chắn chứ?')"
                                            class="action-btn btn-delete"><i class="fa-solid fa-trash-can"></i> Xóa</a>
                                    </td>
                                </tr>
                        <?php
                            }
                        } else {
                            echo "<tr><td colspan='6' style='text-align: center; padding: 40px; color: var(--text-muted);'>
                                    <i class='fa-solid fa-box-open' style='font-size: 40px; margin-bottom: 15px; opacity: 0.5; display: block;'></i>
                                    Chưa có sản phẩm nào trong hệ thống!
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
    <script src="/web_ban_hang/admin/assets/js/product.js"></script>
</body>

</html>
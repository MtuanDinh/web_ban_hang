<?php
require_once("includes/check_admin_alive.php");
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/web_ban_hang/admin/assets/css/style_main.css">
    <title>Trang chủ</title>
</head>


<body>
    <?php
        include "includes/topbar.php";
    ?>
    <section>
        <?php include "includes/sidebar.php"; ?>
        <div id="content">
            <ul id="status_bar">
                <li>Số đơn hàng chờ duyệt</li>
                <li>Doanh thu trong ngày</li>
                <li>Tổng số hàng đang có</li>
            </ul>
            <h2 id="recent_title">Đơn hàng mới nhất</h2>
            <table id="recent_order">
                <thead>
                    <tr>
                        <th>Mã đơn</th>
                        <th>Khách hàng</th>
                        <th>Thời gian tạo đơn</th>
                        <th>Tổng tiền</th>
                        <th>Trạng thái</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>1</td>
                        <td>Thanh Tâm</td>
                        <td>16/6/2026</td>
                        <td>25.000.000đ</td>
                        <td class="status_need_review">Chờ xử lý</td>
                    </tr>
                    <tr>
                        <td>2</td>
                        <td>Thanh Tâm</td>
                        <td>16/6/2026</td>
                        <td>25.000.000đ</td>
                        <td class="status_wait_package">Chờ lấy hàng</td>
                    </tr>
                    <tr>
                        <td>3</td>
                        <td>Thanh Tâm</td>
                        <td>16/6/2026</td>
                        <td>25.000.000đ</td>
                        <td class="status_wait_delivery">Chờ giao hàng</td>
                    </tr>
                    <tr>
                        <td>4</td>
                        <td>Tâm Bình</td>
                        <td>15/6/2026</td>
                        <td>30.000.000đ</td>
                        <td class="status_success">Đã nhận hàng</td>
                    </tr>
                </tbody>
            </table>
            
            <?php include "includes/footer.php" ?>
        </div>
    </section>
</body>

</html>
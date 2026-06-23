<footer>
    <div class="container">
        <ul class="footer-links">
            <?php
            // 1. Lấy toàn bộ danh mục từ database (sắp xếp theo id)
            $sql_footer = "SELECT id, name FROM categories ORDER BY id ASC";
            $result_footer = mysqli_query($conn, $sql_footer);
            $categories_list = [];

            if ($result_footer && mysqli_num_rows($result_footer) > 0) {
                while ($row = mysqli_fetch_assoc($result_footer)) {
                    $categories_list[] = $row;
                }
            }

            // 2. Thuật toán chia đều danh sách thành 3 cột (để không bị vỡ CSS width: 33.3333%)
            $total_cats = count($categories_list);
            $items_per_col = $total_cats > 0 ? ceil($total_cats / 3) : 1;

            // 3. Vòng lặp in ra đúng 3 thẻ <li>
            for ($i = 0; $i < 3; $i++) {
                echo '<li>';

                // Cắt mảng lấy số lượng danh mục cho cột hiện tại
                $col_items = array_slice($categories_list, $i * $items_per_col, $items_per_col);

                if (!empty($col_items)) {
                    echo '<p>';
                    $links = [];
                    foreach ($col_items as $cat) {
                        // Tạo đường dẫn với category_id
                        $links[] = '<a href="?category_id=' . $cat['id'] . '">' . htmlspecialchars($cat['name']) . '</a>';
                    }
                    // Nối các thẻ <a> lại với nhau bằng ký tự " | " giống thiết kế cũ
                    echo implode(' | ', $links);
                    echo '</p>';
                } else {
                    // Cột rỗng (trong trường hợp số lượng danh mục trong DB quá ít)
                    echo '<p></p>';
                }

                echo '</li>';
            }
            ?>
        </ul>

        <div class="footer-info">
            Công ty Cổ phần Thương Mại Tổng Hợp DTA - GPĐKKD: 0xxxxxxxxx cấp tại Sở KH & ĐT TP. Hà Nội. Địa chỉ văn phòng: Thành phố Hà Nội, Việt Nam. Điện thoại: 0xx.xxxx.xxxx.
        </div>

        <div class="footer-badges">
            <img src="assets/image/logo-footer.png" alt="Bộ công thương">
            <img src="assets/image/logo-footer1.webp" alt="DMCA">
        </div>
    </div>
</footer>
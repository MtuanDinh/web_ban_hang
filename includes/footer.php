<footer class="main-footer">
    <div class="footer-top">
        <div class="container footer-grid">
            <!-- Cột 1: Thông tin thương hiệu -->
            <div class="footer-col company-info">
                <img src="assets/image/smartphone-anhlogo.png" alt="PhoneStore Logo" class="footer-logo">
                <p class="company-desc">Hệ thống bán lẻ điện thoại, máy tính bảng và phụ kiện chính hãng uy tín hàng đầu tại Việt Nam.</p>
                <ul class="contact-info">
                    <li><i class="fa-solid fa-location-dot"></i> Trụ sở: Thành phố Hà Nội, Việt Nam</li>
                    <li><i class="fa-solid fa-phone"></i> Hotline: 0xx.xxxx.xxxx</li>
                    <li><i class="fa-solid fa-envelope"></i> Email: contact@phonestore.com</li>
                </ul>
            </div>

            <!-- Cột 2: Danh mục sản phẩm (Lấy tự động từ Database) -->
            <div class="footer-col">
                <h3>Danh Mục Nổi Bật</h3>
                <ul class="footer-list">
                    <?php
                    $sql_footer = "SELECT id, name FROM categories ORDER BY id ASC LIMIT 5";
                    $result_footer = mysqli_query($conn, $sql_footer);

                    if ($result_footer && mysqli_num_rows($result_footer) > 0) {
                        while ($cat = mysqli_fetch_assoc($result_footer)) {
                            echo '<li><a href="index.php?category_id=' . $cat['id'] . '"><i class="fa-solid fa-angle-right"></i> ' . htmlspecialchars($cat['name']) . '</a></li>';
                        }
                    }
                    ?>
                    <li><a href="all_products.php" style="color: #d70018; font-weight: 600;"><i class="fa-solid fa-angles-right"></i> Xem tất cả</a></li>
                </ul>
            </div>

            <!-- Cột 3: Chính sách khách hàng -->
            <div class="footer-col">
                <h3>Hỗ Trợ Khách Hàng</h3>
                <ul class="footer-list">
                    <li><a href="#"><i class="fa-solid fa-angle-right"></i> Chính sách bảo hành</a></li>
                    <li><a href="#"><i class="fa-solid fa-angle-right"></i> Chính sách đổi trả 30 ngày</a></li>
                    <li><a href="#"><i class="fa-solid fa-angle-right"></i> Hướng dẫn mua hàng trả góp</a></li>
                    <li><a href="#"><i class="fa-solid fa-angle-right"></i> Giao hàng & Thanh toán</a></li>
                    <li><a href="#"><i class="fa-solid fa-angle-right"></i> Tra cứu đơn hàng</a></li>
                </ul>
            </div>

            <!-- Cột 4: Chứng nhận & Thanh toán -->
            <div class="footer-col">
                <h3>Chứng Nhận</h3>
                <div class="footer-badges-col">
                    <img src="assets/image/logo-footer.png" alt="Bộ công thương đã thông báo">
                    <img src="assets/image/logo-footer1.webp" alt="DMCA Protected">
                </div>
                <h3 style="margin-top: 25px;">Thanh Toán</h3>
                <div class="payment-methods">
                    <i class="fa-brands fa-cc-visa"></i>
                    <i class="fa-brands fa-cc-mastercard"></i>
                    <i class="fa-brands fa-cc-paypal"></i>
                    <i class="fa-brands fa-cc-apple-pay"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- KHU VỰC COPYRIGHT & NÚT ADMIN ẨN -->
    <div class="footer-bottom">
        <div class="container bottom-container">
            <p class="copyright-text">
                &copy; <?= date('Y') ?> Công ty Cổ phần Thương Mại Tổng Hợp DTA. GPĐKKD: 0xxxxxxxxx cấp tại Sở KH & ĐT TP. Hà Nội. All rights reserved.
            </p>
            
            <!-- TUYỆT CHIÊU: Ngụy trang link Admin thành số phiên bản web -->
            <a href="admin/login.php" target="_blank" class="secret-admin-link" title="System Version">v1.0.2</a>
        </div>
    </div>
</footer>

<link rel="stylesheet" href="/web_ban_hang/assets/css/style_footer.css">
<script src="assets/js/script.js"></script>
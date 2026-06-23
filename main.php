<main class="main-content">
    <div class="main-banner-wrapper">

        <div class="side-banner banner-left">
            <img class="img-side" src="assets/image/bannerleft.jpg" alt="Banner điện thoại trái">
        </div>

        <div class="center-column">

            <div class="slideshow-container">
                <div class="slide fade">
                    <div class="numbertext">1 / 3</div>
                    <img class="img-banner" src="assets/image/iPhone17ProMax_slide.webp" alt="Slide 1">
                </div>
                <div class="slide fade">
                    <div class="numbertext">2 / 3</div>
                    <img class="img-banner" src="assets/image/Oppofin x9 ultra_slide.webp" alt="Slide 2">
                </div>
                <div class="slide fade">
                    <div class="numbertext">3 / 3</div>
                    <img class="img-banner" src="assets/image/samsung-galaxy-slide.webp" alt="Slide 3">
                </div>
            </div>

            <div class="sub-banners">
                <div class="sub-banner-item">
                    <img src="assets/image/anhbannersamsungsmall.jpg" alt="Sub banner 1">
                </div>
                <div class="sub-banner-item">
                    <img src="assets/image/iphonebannersmall.jpg" alt="Sub banner 2">
                </div>
                <div class="sub-banner-item">
                    <img src="assets/image/anhbannerxiaomismall.jpg" alt="Sub banner 3">
                </div>
            </div>

        </div>

        <div class="side-banner banner-right">
            <img class="img-side" src="assets/image/bannerright.jpg" alt="Banner điện thoại phải">
        </div>

    </div>
    <section class="product-section">
        <div class="section-header">
            <h2>ĐIỆN THOẠI</h2>
            <div class="filter-tags">
                <?php
                // Truy vấn lấy các danh mục từ bảng 'categories'
                // LIMIT 5 để giới hạn hiển thị tối đa 5 hãng, tránh làm vỡ giao diện nếu có quá nhiều
                $sql_cats = "SELECT id, name FROM categories ORDER BY id ASC LIMIT 5";
                $result_cats = mysqli_query($conn, $sql_cats);

                if ($result_cats && mysqli_num_rows($result_cats) > 0) {
                    while ($cat = mysqli_fetch_assoc($result_cats)) {
                        // In ra các thẻ <a> chứa tên danh mục, bạn có thể truyền id lên URL để làm chức năng lọc sau này
                        echo '<a href="?category_id=' . $cat['id'] . '">' . htmlspecialchars($cat['name']) . '</a>';
                    }
                } else {
                    // Nếu bảng categories trong Database chưa có dữ liệu, hiển thị tạm thông báo hoặc để trống
                    echo '<a href="#">Chưa có danh mục</a>';
                }
                ?>
            </div>
            <a href="#" class="view-all">Xem tất cả ></a>
        </div>

        <div class="product-layout">
            <div class="product-promo-banner">
                <img src="assets/image/honor-promo-banner.webp" alt="Promo Banner">
                <img src="assets/image/iph17-promo-banner.webp" alt="Promo Banner">

            </div>
            <div class="product-grid">
                <?php
                // 1. KẾT NỐI CSDL
                // $conn = mysqli_connect("localhost", "root", "", "phone_store");

                // 2. TRUY VẤN
                $sql = "SELECT p.id, p.name, p.image, MIN(pv.price) as min_price 
                        FROM products p 
                        LEFT JOIN product_variants pv ON p.id = pv.product_id 
                        GROUP BY p.id 
                        ORDER BY p.id DESC
                        LIMIT 8";

                $result = mysqli_query($conn, $sql);

                // 3. VÒNG LẶP HIỂN THỊ
                if ($result && mysqli_num_rows($result) > 0) {
                    while ($row = mysqli_fetch_assoc($result)) {

                        $current_price = $row['min_price'] ? $row['min_price'] : 0;
                        $old_price = $current_price * 1.1;
                        $discount_percent = 10;

                        // ================= ĐIỂM THAY ĐỔI Ở ĐÂY =================
                        // Nối thêm 'includes/uploads/' vào trước tên file lấy từ CSDL
                        $img_src = !empty($row['image']) ? 'assets/uploads/' . $row['image'] : 'https://via.placeholder.com/300x300?text=No+Image';
                        // ========================================================
                ?>

                        <div class="product-card">
                            <div class="card-badges">
                                <span class="badge-discount">Giảm <?= $discount_percent ?>%</span>
                                <span class="badge-installment">Trả góp 0%</span>
                            </div>
                            <div class="product-image">
                                <img src="<?= htmlspecialchars($img_src) ?>" alt="<?= htmlspecialchars($row['name']) ?>">
                            </div>

                            <h3 class="product-name"><?= htmlspecialchars($row['name']) ?></h3>

                            <div class="product-price">
                                <span class="price-current"><?= number_format($current_price, 0, ',', '.') ?>đ</span>
                                <span class="price-old"><?= number_format($old_price, 0, ',', '.') ?>đ</span>
                            </div>
                            <div class="product-promo">
                                <p>Bảo hành 12 tháng chính hãng</p>
                            </div>
                            <div class="product-shipping">
                                <i class="fa-solid fa-truck-fast"></i> Giao siêu tốc 2h tại <b class="shipping-location">Hà Nội</b>
                            </div>
                            <div class="product-footer">
                                <div class="rating">⭐ 5</div>
                                <div class="wishlist">♡ Yêu thích</div>
                            </div>
                        </div>

                <?php
                    }
                } else {
                    echo "<p style='grid-column: 1 / -1; text-align: center; padding: 50px;'>Chưa có sản phẩm nào trong cửa hàng.</p>";
                }
                ?>
            </div>
    </section>

    <section class="accessory-section">
        <div class="section-header">
            <h2>Sắm thêm phụ kiện chất lượng</h2>
            <a href="#" class="view-all">Xem tất cả ></a>
        </div>
        <div class="accessory-grid">
            <div class="accessory-item">
                <img src="https://cdn-icons-png.flaticon.com/128/865/865322.png" alt="Phụ kiện Apple">
                <span>Phụ kiện Apple</span>
            </div>
            <div class="accessory-item">
                <img src="https://cdn-icons-png.flaticon.com/128/2983/2983804.png" alt="Cáp, sạc">
                <span>Cáp, sạc</span>
            </div>
            <div class="accessory-item">
                <img src="https://cdn-icons-png.flaticon.com/128/3004/3004245.png" alt="Pin sạc dự phòng">
                <span>Pin sạc dự phòng</span>
            </div>
            <div class="accessory-item">
                <img src="https://cdn-icons-png.flaticon.com/128/6530/6530068.png" alt="Ốp lưng">
                <span>Ốp lưng</span>
            </div>
            <div class="accessory-item">
                <img src="https://cdn-icons-png.flaticon.com/128/8583/8583271.png" alt="Gaming Gear">
                <span>Tai nghe</span>
            </div>
            <div class="accessory-item">
                <img src="https://cdn-icons-png.flaticon.com/128/8583/8583271.png" alt="Gaming Gear">
                <span>Quạt tản nhiệt</span>
            </div>
            <div class="accessory-item">
                <img src="https://cdn-icons-png.flaticon.com/128/8583/8583271.png" alt="Gaming Gear">
                <span>Sò lạnh</span>
            </div>
        </div>
    </section>
    <br>
</main>
<script src="assets/js/script.js"></script>
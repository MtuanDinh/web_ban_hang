<?php
require_once("includes/connect_db.php");

echo "<div style='font-family: sans-serif; padding: 30px; line-height: 1.6;'>";
echo "<h2 style='color: #2f80ed;'>🚀 Đang tiến hành thiết lập Hệ thống Danh mục và Sản phẩm (Bản Mở Rộng)...</h2>";

// ========================================================
// 1. DỌN DẸP SẠCH KHO DỮ LIỆU CŨ
// ========================================================
mysqli_query($conn, "SET FOREIGN_KEY_CHECKS = 0");
mysqli_query($conn, "TRUNCATE TABLE product_variants");
mysqli_query($conn, "TRUNCATE TABLE products");
mysqli_query($conn, "TRUNCATE TABLE categories");
mysqli_query($conn, "SET FOREIGN_KEY_CHECKS = 1");
echo "<p>✅ Đã dọn dẹp sạch sẽ các bảng dữ liệu cũ.</p>";

// ========================================================
// 2. TẠO CÂY DANH MỤC (CHA & CON)
// ========================================================
$sql_categories = "INSERT INTO categories (id, name, parent_id) VALUES 
    (1, 'Điện thoại, Tablet', NULL),
    (2, 'Phụ kiện', NULL),
    (3, 'Apple', 1), (4, 'Samsung', 1), (5, 'Xiaomi', 1),
    (6, 'OPPO', 1), (7, 'vivo', 1), (8, 'realme', 1), (9, 'HONOR', 1),
    (10, 'Tai nghe', 2), (11, 'Củ sạc', 2), (12, 'Pin dự phòng', 2),
    (13, 'Cáp sạc', 2), (14, 'Ốp lưng & Dán', 2), (15, 'Tản nhiệt & Giá đỡ', 2)
";
mysqli_query($conn, $sql_categories);
echo "<p>✅ Đã tạo Cây danh mục gồm 2 Nhóm chính và 13 Nhóm phụ.</p>";

// ========================================================
// 3. KHỞI TẠO 50 ĐIỆN THOẠI & TABLET (GẮN VÀO ĐÚNG ID HÃNG)
// ========================================================
$phones = [
    // 30 MÁY CŨ
    [3, 'iPhone 15 Pro Max', 'ip15pm.webp', 'iOS 17', 'Apple A17 Pro', '8GB', '4422 mAh'],
    [3, 'iPhone 15 Pro', 'ip15p.webp', 'iOS 17', 'Apple A17 Pro', '8GB', '3274 mAh'],
    [3, 'iPhone 15 Plus', 'ip15pl.webp', 'iOS 17', 'Apple A16 Bionic', '6GB', '4383 mAh'],
    [3, 'iPhone 15', 'ip15.webp', 'iOS 17', 'Apple A16 Bionic', '6GB', '3349 mAh'],
    [3, 'iPhone 14 Pro Max', 'ip14pm.webp', 'iOS 16', 'Apple A16 Bionic', '6GB', '4323 mAh'],
    [3, 'iPhone 13', 'ip13.webp', 'iOS 15', 'Apple A15 Bionic', '4GB', '3240 mAh'],
    [4, 'Samsung Galaxy S24 Ultra', 's24u.webp', 'Android 14', 'Snapdragon 8 Gen 3', '12GB', '5000 mAh'],
    [4, 'Samsung Galaxy S24 Plus', 's24p.webp', 'Android 14', 'Exynos 2400', '12GB', '4900 mAh'],
    [4, 'Samsung Galaxy S24', 's24.webp', 'Android 14', 'Exynos 2400', '8GB', '4000 mAh'],
    [4, 'Samsung Galaxy Z Fold5', 'zfold5.webp', 'Android 13', 'Snapdragon 8 Gen 2', '12GB', '4400 mAh'],
    [4, 'Samsung Galaxy Z Flip5', 'zflip5.webp', 'Android 13', 'Snapdragon 8 Gen 2', '8GB', '3700 mAh'],
    [4, 'Samsung Galaxy A54 5G', 'a54.webp', 'Android 13', 'Exynos 1380', '8GB', '5000 mAh'],
    [4, 'Samsung Galaxy A34', 'a34.webp', 'Android 13', 'Dimensity 1080', '8GB', '5000 mAh'],
    [5, 'Xiaomi 14 Ultra', 'mi14u.webp', 'HyperOS', 'Snapdragon 8 Gen 3', '16GB', '5300 mAh'],
    [5, 'Xiaomi 14', 'mi14.webp', 'HyperOS', 'Snapdragon 8 Gen 3', '12GB', '4610 mAh'],
    [5, 'Xiaomi 13T Pro', 'mi13t.webp', 'Android 13', 'Dimensity 9200+', '12GB', '5000 mAh'],
    [5, 'Redmi Note 13 Pro+', 'note13p.webp', 'Android 13', 'Dimensity 7200 Ultra', '8GB', '5000 mAh'],
    [5, 'Redmi Note 13', 'note13.webp', 'Android 13', 'Snapdragon 685', '6GB', '5000 mAh'],
    [6, 'OPPO Find N3', 'findn3.webp', 'Android 13', 'Snapdragon 8 Gen 2', '16GB', '4805 mAh'],
    [6, 'OPPO Reno11 Pro', 'reno11p.webp', 'Android 14', 'Dimensity 8200', '12GB', '4600 mAh'],
    [6, 'OPPO Reno11', 'reno11.webp', 'Android 14', 'Dimensity 7050', '8GB', '5000 mAh'],
    [7, 'vivo X100 Pro', 'x100p.webp', 'Android 14', 'Dimensity 9300', '16GB', '5400 mAh'],
    [7, 'vivo V29 5G', 'v29.webp', 'Android 13', 'Snapdragon 778G', '8GB', '4600 mAh'],
    [8, 'realme 11 Pro+', 'rm11p.webp', 'Android 13', 'Dimensity 7050', '8GB', '5000 mAh'],
    [8, 'realme C55', 'rmc55.webp', 'Android 13', 'Helio G88', '6GB', '5000 mAh'],
    [9, 'HONOR 90 5G', 'hn90.webp', 'Android 13', 'Snapdragon 7 Gen 1', '12GB', '5000 mAh'],
    [3, 'iPad Pro 11 inch M2', 'ipadprom2.webp', 'iPadOS 16', 'Apple M2', '8GB', '7538 mAh'],
    [3, 'iPad Air 5', 'ipadair5.webp', 'iPadOS 15', 'Apple M1', '8GB', '28.6 Wh'],
    [4, 'Samsung Galaxy Tab S9', 'tabs9.webp', 'Android 13', 'Snapdragon 8 Gen 2', '8GB', '8400 mAh'],
    [5, 'Xiaomi Pad 6', 'mipad6.webp', 'Android 13', 'Snapdragon 870', '8GB', '8840 mAh'],
    
    // 20 MÁY MỚI THÊM
    [3, 'iPhone 12', 'ip12.webp', 'iOS 15', 'Apple A14 Bionic', '4GB', '2815 mAh'],
    [3, 'iPhone 11', 'ip11.webp', 'iOS 13', 'Apple A13 Bionic', '4GB', '3110 mAh'],
    [3, 'iPad mini 6', 'ipadmini6.webp', 'iPadOS 15', 'Apple A15 Bionic', '4GB', '19.3 Wh'],
    [3, 'iPad Gen 10', 'ipad10.webp', 'iPadOS 16', 'Apple A14 Bionic', '4GB', '28.6 Wh'],
    [4, 'Samsung Galaxy S23 Ultra', 's23u.webp', 'Android 13', 'Snapdragon 8 Gen 2', '8GB', '5000 mAh'],
    [4, 'Samsung Galaxy S23 FE', 's23fe.webp', 'Android 13', 'Exynos 2200', '8GB', '4500 mAh'],
    [4, 'Samsung Galaxy A25 5G', 'a25.webp', 'Android 14', 'Exynos 1280', '8GB', '5000 mAh'],
    [4, 'Samsung Galaxy A15', 'a15.webp', 'Android 14', 'Helio G99', '8GB', '5000 mAh'],
    [4, 'Samsung Galaxy Tab S9 FE', 'tabs9fe.webp', 'Android 13', 'Exynos 1380', '6GB', '8000 mAh'],
    [5, 'Xiaomi 13', 'mi13.webp', 'Android 13', 'Snapdragon 8 Gen 2', '8GB', '4500 mAh'],
    [5, 'Redmi Note 12', 'note12.webp', 'Android 13', 'Snapdragon 4 Gen 1', '4GB', '5000 mAh'],
    [5, 'POCO X6 Pro 5G', 'pocox6p.webp', 'Android 14', 'Dimensity 8300 Ultra', '8GB', '5000 mAh'],
    [6, 'OPPO Reno10 5G', 'reno10.webp', 'Android 13', 'Dimensity 7050', '8GB', '5000 mAh'],
    [6, 'OPPO A98 5G', 'a98.webp', 'Android 13', 'Snapdragon 695', '8GB', '5000 mAh'],
    [6, 'OPPO Pad 2', 'oppopad2.webp', 'Android 13', 'Dimensity 9000', '8GB', '9510 mAh'],
    [7, 'vivo V27e', 'v27e.webp', 'Android 13', 'Helio G99', '8GB', '4600 mAh'],
    [7, 'vivo Y36', 'y36.webp', 'Android 13', 'Snapdragon 680', '8GB', '5000 mAh'],
    [8, 'realme 11', 'rm11.webp', 'Android 13', 'Helio G99', '8GB', '5000 mAh'],
    [8, 'realme C53', 'rmc53.webp', 'Android 13', 'Unisoc T120', '6GB', '5000 mAh'],
    [9, 'HONOR X9a', 'hnx9a.webp', 'Android 12', 'Snapdragon 695', '8GB', '5100 mAh']
];

$phone_colors = ['Đen Nhám', 'Trắng Tuyết', 'Xanh Titan', 'Tím Khói', 'Bạc Tinh Vân', 'Xanh Rêu'];
$phone_versions = ['128GB', '256GB', '512GB'];

foreach ($phones as $p) {
    $cat_id = $p[0];
    $specs_json = json_encode(['os' => $p[3], 'cpu' => $p[4], 'ram' => $p[5], 'battery' => $p[6]], JSON_UNESCAPED_UNICODE);
    $name = mysqli_real_escape_string($conn, $p[1]);
    $img = mysqli_real_escape_string($conn, $p[2]);

    mysqli_query($conn, "INSERT INTO products (category_id, name, image, description) VALUES ($cat_id, '$name', '$img', '$specs_json')");
    $new_product_id = mysqli_insert_id($conn);

    $base_price = rand(4, 30) * 1000000; 
    $c1 = $phone_colors[array_rand($phone_colors)];
    $c2 = $phone_colors[array_rand($phone_colors)];
    
    mysqli_query($conn, "INSERT INTO product_variants (product_id, color, version, price, stock) VALUES ($new_product_id, '$c1', '{$phone_versions[0]}', $base_price, " . rand(10, 50) . ")");
    mysqli_query($conn, "INSERT INTO product_variants (product_id, color, version, price, stock) VALUES ($new_product_id, '$c2', '{$phone_versions[1]}', " . ($base_price + 2500000) . ", " . rand(5, 20) . ")");
}
echo "<p>✅ Đã nạp thành công 50 Máy (được phân đúng hãng) cùng 100 biến thể.</p>";

// ========================================================
// 4. KHỞI TẠO 40 PHỤ KIỆN (GẮN VÀO ĐÚNG ID LOẠI PHỤ KIỆN)
// ========================================================
$accessories = [
    // 20 PHỤ KIỆN CŨ
    [10, 'Tai nghe AirPods Pro 2', 'airpodspro2.webp', 'Tai nghe không dây', 'Lên đến 30h'],
    [10, 'Tai nghe AirPods 3', 'airpods3.webp', 'Tai nghe không dây', 'Lên đến 30h'],
    [10, 'Tai nghe Galaxy Buds2 Pro', 'buds2pro.webp', 'Tai nghe không dây', 'Lên đến 18h'],
    [11, 'Sạc nhanh Apple 20W', 'sac20w.webp', 'Củ sạc', 'N/A'],
    [11, 'Sạc nhanh Samsung 45W', 'sac45w.webp', 'Củ sạc', 'N/A'],
    [11, 'Củ sạc Anker 65W GaN', 'anker65w.webp', 'Củ sạc', 'N/A'],
    [11, 'Củ sạc Ugreen 100W', 'ugreen100w.webp', 'Củ sạc', 'N/A'],
    [12, 'Pin dự phòng Xiaomi 20000mAh', 'pindp20k.webp', 'Pin dự phòng', '20000 mAh'],
    [12, 'Pin dự phòng Baseus 10000mAh', 'baseus10k.webp', 'Pin dự phòng', '10000 mAh'],
    [12, 'Pin dự phòng Anker PowerCore', 'anker10k.webp', 'Pin dự phòng', '10000 mAh'],
    [13, 'Cáp sạc Type-C to Lightning', 'capctl.webp', 'Cáp sạc', 'N/A'],
    [13, 'Cáp sạc Baseus 100W Type-C', 'cap100w.webp', 'Cáp sạc', 'N/A'],
    [14, 'Ốp lưng iPhone 15 Pro Max Clear', 'op15pm.webp', 'Ốp lưng', 'N/A'],
    [14, 'Ốp lưng Galaxy S24 Ultra Silicone', 'ops24u.webp', 'Ốp lưng', 'N/A'],
    [14, 'Kính cường lực KingKong iPhone 15', 'kinhkk.webp', 'Miếng dán bảo vệ', 'N/A'],
    [10, 'Tai nghe Sony WF-1000XM5', 'sonywf.webp', 'Tai nghe không dây', 'Lên đến 24h'],
    [10, 'Tai nghe Marshall Motif II', 'marshall.webp', 'Tai nghe không dây', 'Lên đến 30h'],
    [15, 'Quạt tản nhiệt điện thoại Memo', 'memo.webp', 'Tản nhiệt', 'N/A'],
    [15, 'Sò lạnh Black Shark', 'blackshark.webp', 'Tản nhiệt', 'N/A'],
    [15, 'Giá đỡ điện thoại bàn Baseus', 'giado.webp', 'Giá đỡ', 'N/A'],

    // 20 PHỤ KIỆN MỚI THÊM
    [10, 'Tai nghe AirPods 2', 'airpods2.webp', 'Tai nghe không dây', 'Lên đến 24h'],
    [10, 'Tai nghe Galaxy Buds FE', 'budsfe.webp', 'Tai nghe không dây', 'Lên đến 30h'],
    [10, 'Tai nghe Redmi Buds 4', 'redmibuds4.webp', 'Tai nghe không dây', 'Lên đến 30h'],
    [10, 'Tai nghe OPPO Enco Air3', 'encoair3.webp', 'Tai nghe không dây', 'Lên đến 25h'],
    [11, 'Sạc nhanh Samsung 25W', 'sac25w.webp', 'Củ sạc', 'N/A'],
    [11, 'Sạc kép Apple 35W Dual', 'sac35w.webp', 'Củ sạc', 'N/A'],
    [11, 'Sạc Baseus GaN5 Pro 65W', 'baseus65w.webp', 'Củ sạc', 'N/A'],
    [11, 'Sạc Ugreen Nexode 65W', 'ugreen65w.webp', 'Củ sạc', 'N/A'],
    [12, 'Pin sạc dự phòng MagSafe Apple', 'pimmagsafe.webp', 'Pin dự phòng', '1460 mAh'],
    [12, 'Pin dự phòng Samsung Wireless 10000mAh', 'pinsamsung10k.webp', 'Pin dự phòng', '10000 mAh'],
    [12, 'Pin dự phòng Anker 347 40000mAh', 'anker40k.webp', 'Pin dự phòng', '40000 mAh'],
    [12, 'Pin dự phòng Ugreen 20000mAh', 'ugreen20k.webp', 'Pin dự phòng', '20000 mAh'],
    [13, 'Cáp Apple USB-C to USB-C 240W', 'capc2c240w.webp', 'Cáp sạc', 'N/A'],
    [13, 'Cáp Anker PowerLine III C to Lightning', 'capanker_ctl.webp', 'Cáp sạc', 'N/A'],
    [13, 'Cáp Ugreen bọc dù Type-C', 'capugreen_c.webp', 'Cáp sạc', 'N/A'],
    [14, 'Ốp lưng UAG Monarch iPhone 15 Pro Max', 'opuag15pm.webp', 'Ốp lưng', 'N/A'],
    [14, 'Ốp lưng Spigen Liquid Crystal S24 Ultra', 'opspigen_s24u.webp', 'Ốp lưng', 'N/A'],
    [14, 'Kính cường lực Nillkin CP+ Pro', 'kinhnillkin.webp', 'Miếng dán bảo vệ', 'N/A'],
    [15, 'Quạt tản nhiệt Flydigi B6X', 'flydigib6x.webp', 'Tản nhiệt', 'N/A'],
    [15, 'Giá đỡ Laptop/Tablet Baseus', 'giadolaptop.webp', 'Giá đỡ', 'N/A']
];

foreach ($accessories as $a) {
    $cat_id = $a[0]; 
    $specs_json = json_encode(['type' => $a[3], 'battery' => $a[4]], JSON_UNESCAPED_UNICODE);
    $name = mysqli_real_escape_string($conn, $a[1]);
    $img = mysqli_real_escape_string($conn, $a[2]);

    mysqli_query($conn, "INSERT INTO products (category_id, name, image, description) VALUES ($cat_id, '$name', '$img', '$specs_json')");
    $new_product_id = mysqli_insert_id($conn);

    $acc_price = rand(2, 25) * 100000; 
    mysqli_query($conn, "INSERT INTO product_variants (product_id, color, version, price, stock) VALUES ($new_product_id, 'Mặc định', 'Tiêu chuẩn', $acc_price, " . rand(50, 200) . ")");
}
echo "<p>✅ Đã nạp thành công 40 Phụ kiện (Đã chia rõ vào Sạc, Ốp lưng, Tai nghe...).</p>";

echo "<h2 style='color: #10b981;'>🎉 HOÀN TẤT TUYỆT ĐỐI! 90 Sản phẩm đã sẵn sàng trên kệ hàng.</h2>";
echo "<a href='index.php' style='padding: 10px 20px; background: #d70018; color: #fff; text-decoration: none; border-radius: 5px; font-weight: bold;'>Về Trang Chủ</a>";
echo "</div>";
?>
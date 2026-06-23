<?php
$base_price = $row['min_price'] ? $row['min_price'] : 0;
$img_src = !empty($row['image']) ? 'assets/uploads/' . $row['image'] : 'https://via.placeholder.com/300x300?text=No+Image';
?>
<a href="detail.php?id=<?= $row['id'] ?>" class="product-card" style="text-decoration: none; color: inherit;">
    <div class="card-badges">
        <span class="badge-discount" style="display: none;">Giảm 0%</span>
        <span class="badge-installment">Trả góp 0%</span>
    </div>
    <div class="product-image">
        <img src="<?= htmlspecialchars($img_src) ?>" alt="<?= htmlspecialchars($row['name']) ?>">
    </div>

    <h3 class="product-name"><?= htmlspecialchars($row['name']) ?></h3>

    <div class="product-price">
        <span class="price-current" data-base-price="<?= $base_price ?>">
            <?php if($base_price != 0){ 
                echo number_format($base_price, 0, ',', '.') . "đ";
            } else {
                echo "Liên hệ";
            } ?>
        </span>
        <span class="price-old" style="display: none;"><?= number_format($base_price, 0, ',', '.') ?>đ</span>
    </div>
    
    <div class="product-promo">
        <p>Bảo hành 12 tháng chính hãng</p>
    </div>
    <div class="product-shipping" style="margin-top:auto;">
        <i class="fa-solid fa-truck-fast"></i> Giao siêu tốc 2h tại <b class="shipping-location">Hà Nội</b>
    </div>
</a>
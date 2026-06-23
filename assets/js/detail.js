function updatePrice() {
    var select = document.getElementById('variant_select');
    if(!select || select.options.length === 0 || !select.value) return;

    // 1. Lấy giá gốc của phiên bản người dùng đang chọn (VD: bản 256GB)
    var option = select.options[select.selectedIndex];
    var basePrice = parseInt(option.getAttribute('data-price')) || 0;

    // 2. Lấy % giảm giá từ Vị trí (do script.js lưu vào trình duyệt)
    var discountRate = parseInt(localStorage.getItem('user_discount')) || 0;

    var displayPrice = document.getElementById('display_price');
    var displayOldPrice = document.getElementById('display_old_price');
    var detailBadge = document.getElementById('detail_badge');

    if (basePrice > 0) {
        if (discountRate > 0) {
            // CÓ GIẢM GIÁ
            var newPrice = basePrice - (basePrice * discountRate / 100);
            displayPrice.textContent = new Intl.NumberFormat('vi-VN').format(newPrice) + 'đ';
            
            if(displayOldPrice) {
                displayOldPrice.textContent = new Intl.NumberFormat('vi-VN').format(basePrice) + 'đ';
                displayOldPrice.style.display = 'inline-block';
            }
            if(detailBadge) {
                detailBadge.textContent = 'Giảm ' + discountRate + '%';
                detailBadge.style.display = 'inline-block';
            }
        } else {
            // KHÔNG GIẢM GIÁ
            displayPrice.textContent = new Intl.NumberFormat('vi-VN').format(basePrice) + 'đ';
            if(displayOldPrice) displayOldPrice.style.display = 'none';
            if(detailBadge) detailBadge.style.display = 'none';
        }
    } else {
        // GIÁ LIÊN HỆ
        displayPrice.textContent = "Liên hệ";
        if(displayOldPrice) displayOldPrice.style.display = 'none';
        if(detailBadge) detailBadge.style.display = 'none';
    }
}

// Gọi hàm cập nhật giá ngay khi trang vừa tải xong
document.addEventListener("DOMContentLoaded", function() {
    updatePrice();
});
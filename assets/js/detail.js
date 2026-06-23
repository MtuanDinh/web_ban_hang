function updatePrice() {
    // Lấy thẻ <select>
    var selectBox = document.getElementById("variant_select");
    
    // Lấy thẻ <option> đang được người dùng chọn
    var selectedOption = selectBox.options[selectBox.selectedIndex];
    
    // Rút giá tiền từ thuộc tính 'data-price' của thẻ option đó
    var price = selectedOption.getAttribute("data-price");
    
    if (price) {
        // Định dạng số tiền có dấu chấm (VD: 25000000 -> 25.000.000)
        var formattedPrice = new Intl.NumberFormat('vi-VN').format(price) + ' đ';
        
        // In ra màn hình
        document.getElementById("display_price").innerText = formattedPrice;
    } else {
        document.getElementById("display_price").innerText = "Liên hệ";
    }
}

// Chạy hàm updatePrice() ngay khi trang vừa load xong để hiển thị giá của lựa chọn đầu tiên
window.onload = function() {
    updatePrice();
};

// Hàm đổi ảnh chính khi click vào ảnh nhỏ
function changeMainImage(imgSrc, clickedElement) {
    // 1. Đổi đường dẫn của ảnh bự
    document.getElementById('main-img').src = imgSrc;

    // 2. Tìm tất cả các ảnh nhỏ và xóa viền đỏ (class active) đi
    var thumbs = document.querySelectorAll('.thumb-item');
    thumbs.forEach(function(thumb) {
        thumb.classList.remove('active');
    });

    // 3. Đeo viền đỏ vào tấm ảnh vừa được click
    clickedElement.classList.add('active');
}
// Biến toàn cục để theo dõi vị trí ảnh hiện tại và bộ đếm thời gian
let currentThumbIndex = 0;
let autoSlideTimer;

/* ==========================================
   1. CẬP NHẬT GIÁ BÁN & KHUYẾN MÃI
   ========================================== */
function updatePrice() {
    var select = document.getElementById('variant_select');
    if(!select || select.options.length === 0 || !select.value) return;

    var option = select.options[select.selectedIndex];
    var basePrice = parseInt(option.getAttribute('data-price')) || 0;
    var discountRate = parseInt(localStorage.getItem('user_discount')) || 0;

    var displayPrice = document.getElementById('display_price');
    var displayOldPrice = document.getElementById('display_old_price');
    var detailBadge = document.getElementById('detail_badge');

    if (basePrice > 0) {
        if (discountRate > 0) {
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
            displayPrice.textContent = new Intl.NumberFormat('vi-VN').format(basePrice) + 'đ';
            if(displayOldPrice) displayOldPrice.style.display = 'none';
            if(detailBadge) detailBadge.style.display = 'none';
        }
    } else {
        displayPrice.textContent = "Liên hệ";
        if(displayOldPrice) displayOldPrice.style.display = 'none';
        if(detailBadge) detailBadge.style.display = 'none';
    }
}

/* ==========================================
   2. XỬ LÝ CHUYỂN ẢNH KHI BẤM VÀO THUMBNAIL
   ========================================== */
function changeMainImage(imageSrc, thumbnailElement) {
    var mainImg = document.getElementById('main-img');
    if (mainImg) {
        // Bước 1: Cho ảnh hiện tại mờ hẳn đi (Biến mất)
        mainImg.style.opacity = '0';
        
        // Bước 2: Đợi đúng 300ms (Khớp với thời gian transition của CSS) để ảnh mờ hết
        setTimeout(function() {
            // Thay đổi nguồn ảnh (src) thành ảnh mới
            mainImg.src = imageSrc;
            
            // Bước 3: Cho ảnh mới từ từ sáng lên
            mainImg.style.opacity = '1';
        }, 300); 
    }

    // Cập nhật viền đỏ cho ảnh nhỏ bên dưới
    var allThumbs = document.querySelectorAll('.thumb-item');
    allThumbs.forEach(function(thumb, index) {
        thumb.classList.remove('active');
        
        if (thumb === thumbnailElement) {
            currentThumbIndex = index;
        }
    });
    
    if (thumbnailElement) {
        thumbnailElement.classList.add('active');
    }

    // Reset lại bộ đếm tự động
    clearInterval(autoSlideTimer);
    startAutoSlide();
}
/* ==========================================
   3. TỰ ĐỘNG CHUYỂN ẢNH SAU 10 GIÂY
   ========================================== */
function startAutoSlide() {
    var allThumbs = document.querySelectorAll('.thumb-item');
    
    // Chỉ kích hoạt tự động chạy nếu sản phẩm có nhiều hơn 1 ảnh
    if (allThumbs.length > 1) {
        autoSlideTimer = setInterval(function() {
            currentThumbIndex++;
            
            // Nếu đã chạy đến ảnh cuối cùng, quay vòng lại ảnh đầu tiên
            if (currentThumbIndex >= allThumbs.length) {
                currentThumbIndex = 0; 
            }
            
            var nextThumb = allThumbs[currentThumbIndex];
            var nextImgSrc = nextThumb.querySelector('img').getAttribute('src');
            
            // Gọi hàm chuyển ảnh
            changeMainImage(nextImgSrc, nextThumb);
            
        }, 10000); // 10000 mili-giây = 10 giây
    }
}

// Khởi chạy các chức năng ngay khi trang vừa load xong
document.addEventListener("DOMContentLoaded", function() {
    updatePrice();
    startAutoSlide(); 
});
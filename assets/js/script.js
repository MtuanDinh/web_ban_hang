//slideshow


// ==========================================
// XỬ LÝ MODAL VỊ TRÍ & GIẢM GIÁ THEO KHU VỰC
// ==========================================
const btnOpenLocation = document.getElementById('btn-location');
const locationText = document.getElementById('location-text');
const modalLocation = document.querySelector('.modal-location');
const btnClose = document.querySelector('.close-btn');
const listProvinces = document.querySelectorAll('.location-list li');
const searchProvince = document.getElementById('search-province');

// 1. Khôi phục Vị trí & Giảm giá khi người dùng vừa load trang (Hoặc sang trang khác)
document.addEventListener("DOMContentLoaded", () => {
    const savedLocation = localStorage.getItem('user_location');
    const savedDiscount = localStorage.getItem('user_discount');
    
    if (savedLocation && locationText) {
        locationText.textContent = savedLocation; 
        
        // Đánh dấu lại tỉnh thành trong danh sách Modal
        listProvinces.forEach(p => {
            const cleanText = p.textContent.replace(' ', '').trim();
            const cleanSaved = savedLocation.replace(' ', '').trim();
            if (cleanText === cleanSaved) {
                listProvinces.forEach(item => {
                    item.classList.remove('active');
                    const icon = item.querySelector('.fa-circle-check');
                    if(icon) icon.remove();
                });
                p.classList.add('active');
                p.innerHTML += ' <i class="fa-solid fa-circle-check"></i>';
            }
        });
        
        // Sửa lại chữ "Giao siêu tốc 2h tại..." trên các thẻ sản phẩm
        const shippingTexts = document.querySelectorAll('.shipping-location');
        shippingTexts.forEach(shipText => shipText.textContent = savedLocation);
        
        // Chạy phép thuật giảm giá
        applyRegionalDiscount(savedDiscount);
    }
});

// 2. Mở / Đóng Modal
if (btnOpenLocation) {
    btnOpenLocation.addEventListener('click', function(event) {
        event.preventDefault();
        modalLocation.style.display = 'flex';
    });
}

if (btnClose) {
    btnClose.addEventListener('click', function(event) {
        event.preventDefault();
        modalLocation.style.display = 'none';
    });
}

// 3. Chức năng Tìm kiếm Tỉnh thành trong Modal (Gõ chữ là tự lọc)
if (searchProvince) {
    searchProvince.addEventListener('keyup', function() {
        const filter = this.value.toLowerCase();
        listProvinces.forEach(li => {
            const text = li.textContent.toLowerCase();
            if (text.includes(filter)) li.style.display = 'flex';
            else li.style.display = 'none';
        });
    });
}

// 4. Khi người dùng Click chọn 1 khu vực
listProvinces.forEach(function(province) {
    province.addEventListener('click', function() {
        // Xóa dấu check màu đỏ cũ
        listProvinces.forEach(function(item) {
            item.classList.remove('active');
            const checkIcon = item.querySelector('.fa-circle-check');
            if (checkIcon) checkIcon.remove();
        });

        // Đóng dấu check vào tỉnh vừa chọn
        this.classList.add('active');
        this.innerHTML += ' <i class="fa-solid fa-circle-check"></i>';

        let selectedLocation = this.textContent.trim();
        let discountRate = parseInt(this.getAttribute('data-discount')) || 0;

        // Cập nhật giao diện bên ngoài
        if (locationText) locationText.textContent = selectedLocation;
        const shippingTexts = document.querySelectorAll('.shipping-location');
        shippingTexts.forEach(shipText => shipText.textContent = selectedLocation);

        // Lưu vào bộ nhớ Trình duyệt (Tắt máy mở lại web vẫn nhớ)
        localStorage.setItem('user_location', selectedLocation);
        localStorage.setItem('user_discount', discountRate);

        // Kích hoạt giảm giá
        applyRegionalDiscount(discountRate);

        modalLocation.style.display = 'none';
        document.cookie = "user_discount=" + discountRate + "; path=/; max-age=" + (30*24*60*60);
        // Nhảy ra thông báo xịn sò nếu khu vực đó có giảm giá
        // if(discountRate > 0) {
        //     alert(`🎉 Chúc mừng! Khu vực ${selectedLocation} đang có tuần lễ vàng. Giảm giá thêm ${discountRate}% cho mọi sản phẩm!`);
        // }
    });
});

// 5. HÀM CỐT LÕI: Tính toán giá, dán nhãn Giảm giá và hiện Giá cũ
function applyRegionalDiscount(discountRate) {
    discountRate = parseInt(discountRate) || 0;
    
    // Quét toàn bộ các thẻ sản phẩm đang hiển thị trên trang
    const productCards = document.querySelectorAll('.product-card');
    
    productCards.forEach(card => {
        const priceCurrentEl = card.querySelector('.price-current');
        const priceOldEl = card.querySelector('.price-old');
        const badgeDiscountEl = card.querySelector('.badge-discount');
        
        // Chỉ xử lý nếu thẻ này có chứa thuộc tính data-base-price (bỏ qua các sản phẩm "Liên hệ")
        if(priceCurrentEl && priceCurrentEl.hasAttribute('data-base-price')) {
            let basePrice = parseInt(priceCurrentEl.getAttribute('data-base-price'));
            
            if (basePrice > 0) {
                if (discountRate > 0) {
                    // CÓ GIẢM GIÁ
                    // 1. Hạ giá hiển thị chính
                    let newPrice = basePrice - (basePrice * discountRate / 100);
                    priceCurrentEl.textContent = new Intl.NumberFormat('vi-VN').format(newPrice) + 'đ';
                    
                    // 2. Hiện Giá cũ (Bằng chính giá gốc) và gạch ngang
                    if(priceOldEl) {
                        priceOldEl.textContent = new Intl.NumberFormat('vi-VN').format(basePrice) + 'đ';
                        priceOldEl.style.display = 'inline-block';
                    }
                    
                    // 3. Hiện Badge Giảm X%
                    if(badgeDiscountEl) {
                        badgeDiscountEl.textContent = 'Giảm ' + discountRate + '%';
                        badgeDiscountEl.style.display = 'inline-block';
                    }
                } else {
                    // KHÔNG GIẢM GIÁ (Ví dụ: Trở về Hà Nội 0%)
                    // 1. Trả lại giá gốc
                    priceCurrentEl.textContent = new Intl.NumberFormat('vi-VN').format(basePrice) + 'đ';
                    
                    // 2. Tắt Giá cũ và Tắt Badge
                    if(priceOldEl) priceOldEl.style.display = 'none';
                    if(badgeDiscountEl) badgeDiscountEl.style.display = 'none';
                }
            }
        }
    });
    if (typeof updatePrice === 'function') {
        updatePrice();
    }
}

// ========================================================
// XỬ LÝ LỌC SẢN PHẨM BẰNG AJAX (KHÔNG LOAD LẠI TRANG)
// ========================================================
document.addEventListener('DOMContentLoaded', function() {
    
    // ----------------------------------------------------
    // 1. DÀNH CHO TRANG CHỦ (index.php) - Click vào thẻ <a>
    // ----------------------------------------------------
    const indexFilterTags = document.querySelector('.filter-tags');
    if (indexFilterTags) {
        function attachIndexEvents() {
            const links = document.querySelectorAll('.filter-tags a');
            links.forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault(); // Chặn load trang
                    loadIndexAjax(this.getAttribute('href'));
                });
            });
        }

        function loadIndexAjax(url) {
            const productsArea = document.querySelector('.product-layout'); 
            productsArea.style.position = 'relative';
            
            // Bật Loading
            let loader = document.getElementById('ajax-loader');
            if (!loader) {
                productsArea.insertAdjacentHTML('beforeend', `
                    <div id="ajax-loader" class="ajax-loader-overlay">
                        <div class="spinner"></div>
                    </div>
                `);
                loader = document.getElementById('ajax-loader');
            }
            loader.classList.add('active');

            // TUYỆT CHIÊU: Ép thời gian chờ tối thiểu 600ms (0.6 giây) để xem được animation
            const minDelay = new Promise(resolve => setTimeout(resolve, 600));

            // Bắt buộc cả 2 tác vụ (Fetch Data VÀ Đợi 600ms) cùng hoàn thành mới chạy tiếp
            Promise.all([fetch(url), minDelay])
                .then(([response]) => response.text())
                .then(html => {
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(html, 'text/html');
                    
                    const newGrid = doc.querySelector('.product-grid');
                    const newTags = doc.querySelector('.filter-tags');
                    
                    if (newGrid && newTags) {
                        document.querySelector('.product-grid').innerHTML = newGrid.innerHTML;
                        document.querySelector('.filter-tags').innerHTML = newTags.innerHTML;
                        
                        attachIndexEvents(); // Gắn lại sự kiện click cho các nút mới
                    }
                    
                    window.history.pushState({path: url}, '', url);
                })
                .catch(err => console.error("Lỗi AJAX: ", err))
                .finally(() => {
                    // Tắt Loading
                    if (loader) loader.classList.remove('active');
                });
        }
        
        attachIndexEvents();
    }

    // ----------------------------------------------------
    // 2. DÀNH CHO TRANG TẤT CẢ SẢN PHẨM - Gửi bằng <form>
    // ----------------------------------------------------
    const filterForm = document.querySelector('.filter-sidebar form');
    if (filterForm) {
        function attachFormEvents() {
            const currentForm = document.querySelector('.filter-sidebar form');
            if (currentForm) {
                currentForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    const formData = new FormData(this);
                    const params = new URLSearchParams(formData).toString();
                    const actionUrl = this.getAttribute('action') || window.location.pathname;
                    loadFormAjax(actionUrl + '?' + params);
                });
            }
        }

        function loadFormAjax(url) {
            const productsArea = document.querySelector('.products-area');
            if(!productsArea) return;

            productsArea.style.position = 'relative';
            
            let loader = document.getElementById('ajax-loader');
            if (!loader) {
                productsArea.insertAdjacentHTML('beforeend', `
                    <div id="ajax-loader" class="ajax-loader-overlay">
                        <div class="spinner"></div>
                    </div>
                `);
                loader = document.getElementById('ajax-loader');
            }
            loader.classList.add('active');

            // Ép thời gian chờ tối thiểu 600ms
            const minDelay = new Promise(resolve => setTimeout(resolve, 600));

            Promise.all([fetch(url), minDelay])
                .then(([response]) => response.text())
                .then(html => {
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(html, 'text/html');
                    
                    const newGrid = doc.querySelector('.product-grid');
                    const newSidebar = doc.querySelector('.filter-sidebar');
                    
                    if (newGrid && newSidebar) {
                        document.querySelector('.product-grid').innerHTML = newGrid.innerHTML;
                        document.querySelector('.filter-sidebar').innerHTML = newSidebar.innerHTML;
                        attachFormEvents();
                    }
                    window.history.pushState({path: url}, '', url);
                })
                .finally(() => {
                    if (loader) loader.classList.remove('active');
                });
        }
        
        attachFormEvents();
    }

    window.addEventListener('popstate', function() {
        window.location.reload(); 
    });
});
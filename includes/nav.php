<nav>
    <ul>
        <li><a href="http://localhost/web_ban_hang/"><img src="assets/image/smartphone-anhlogo.png" alt=""></a></li>

        <li>
            <a href="#"><i class="fa-solid fa-list"></i> Danh Mục <i class="fa-solid fa-angle-down"></i></a>
            <ul class="submenu">
                <li><a href="#"><i class="fa-solid fa-mobile-screen-button"></i> Điện thoại, Tablet</a></li>
                <li><a href="#"><i class="fa-solid fa-headphones"></i> Phụ kiện</a></li>
            </ul>
        </li>
        <li><a href="#"><i class="fa-solid fa-location-crosshairs"> </i> Location <i class="fa-solid fa-angle-down"></i></a>
            <div class="modal-location">
                <div class="modal-location-content">
                    <div class="modal-header">
                        <div class="search-box">
                            <i class="fa-solid fa-magnifying-glass"></i>
                            <input type="text" placeholder="Nhập tên tỉnh thành">

                        </div>
                        <button class="close-btn">Đóng <i class="fa-solid fa-xmark"></i></button>
                    </div>
                    <div class="modal-body">
                        <p class="modal-note">Vui lòng chọn tỉnh, thành phố để biết chính xác giá, khuyến mãi và tồn kho
                        </p>
                        <ul class="location-list">
                            <li>Hồ Chí Minh</li>
                            <li class="active">Hà Nội <i class="fa-solid fa-circle-check"></i></li>

                        </ul>
                    </div>
                </div>
            </div>

        </li>
        <li><i class="fa-solid fa-magnifying-glass"></i><input type="text" placeholder="Bạn muốn mua gì hôm nay?"></li>
        <li><a>Giỏ hàng <i class="fa-solid fa-cart-shopping"></i></a></li>
        <li><a>Đăng nhập <i class="fa-regular fa-circle-user"></i></a></li>
    </ul>
</nav>
<script>
    // 1. Lấy các phần tử cần thiết ra để thao tác
    const btnOpenLocation = document.querySelector('nav ul li:nth-child(3) > a');
    const modalLocation = document.querySelector('.modal-location');
    const btnClose = document.querySelector('.close-btn');
    const listProvinces = document.querySelectorAll('.location-list li');

    // 2. Lệnh MỞ popup khi bấm vào chữ Location trên menu
    btnOpenLocation.addEventListener('click', function(event) {
        event.preventDefault();
        modalLocation.style.display = 'flex';
    });

    // 3. Lệnh ĐÓNG popup khi bấm nút Đóng (X)
    btnClose.addEventListener('click', function(event) {
        event.preventDefault();
        modalLocation.style.display = 'none';
    });

    // 4. Xử lý khi bấm vào 1 Tỉnh/Thành phố bất kỳ
    listProvinces.forEach(function(province) {
        province.addEventListener('click', function() {

            // Bước 4a: Xóa hết class 'active' và dấu tích ở TẤT CẢ các tỉnh
            listProvinces.forEach(function(item) {
                item.classList.remove('active');

                // Chỉ tìm và xóa đúng icon dấu tích (tránh xóa nhầm icon khác nếu có)
                const checkIcon = item.querySelector('.fa-circle-check');
                if (checkIcon) {
                    checkIcon.remove();
                }
            });

            // Bước 4b: Thêm dấu tích và class active vào tỉnh vừa bấm
            this.classList.add('active');
            this.innerHTML += ' <i class="fa-solid fa-circle-check"></i>';

            // Bước 4c: Lấy tên tỉnh vừa click
            let selectedLocation = this.textContent.trim();

            // Cập nhật chữ trên thẻ Location của thanh Nav
            btnOpenLocation.innerHTML = '<i class="fa-solid fa-location-crosshairs"></i> ' + selectedLocation + ' <i class="fa-solid fa-angle-down"></i>';

            // Đi tìm tất cả các chữ địa điểm trong các card sản phẩm và đổi tên
            const shippingTexts = document.querySelectorAll('.shipping-location');
            shippingTexts.forEach(function(shipText) {
                shipText.textContent = selectedLocation;
            });

            // Bước 4d: Đóng popup
            modalLocation.style.display = 'none';
        });
    });
</script>
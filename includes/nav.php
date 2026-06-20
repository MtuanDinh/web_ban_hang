<nav>
    <ul>
        <li><a href="#"><img src="assets/image/smartphone-anhlogo.png" alt=""></a></li>
        
        <li>
            <a href="#"><i class="fa-solid fa-list"></i> Danh Mục <i class="fa-solid fa-angle-down"></i></a>
            <ul class="submenu">
                <li><a href="#"><i class="fa-solid fa-mobile-screen-button"></i> Điện thoại, Tablet</a></li>
                <li><a href="#"><i class="fa-solid fa-headphones"></i> Phụ kiện</a></li>
            </ul>
        </li>
        <li><a href="#"><i class="fa-solid fa-location-crosshairs">  </i> Location <i class="fa-solid fa-angle-down"></i></a>
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
                    <p class ="modal-note">Vui lòng chọn tỉnh, thành phố để biết chính xác giá, khuyến mãi và tồn kho
</p>
<ul class="location-list">
                <li>Hồ Chí Minh</li>
                <li class="active">Hà Nội <i class="fa-solid fa-circle-check"></i></li> 
                <li>An Giang</li>
                <li>Ninh Bình</li>
                <li>Bạc Liêu</li>
                <li>Bắc Giang</li>
                <li>Bắc Ninh</li>
                <li>Bến Tre</li>
                <li>Bình Dương</li>
                <li>Bình Định</li>
                <li>Cà Mau</li>
                <li>Cần Thơ</li>
                <li>Đà Nẵng</li>
                <li>Đắk Lắk</li>
                <li>Đồng Nai</li>
                <li>Đồng Tháp</li>
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
    const btnOpenLocation = document.querySelector('nav ul li:nth-child(3) > a'); // Nút Location trên thanh Menu
    const modalLocation = document.querySelector('.modal-location');              // Khung popup mờ
    const btnClose = document.querySelector('.close-btn');                        // Nút Đóng X
    const listProvinces = document.querySelectorAll('.location-list li');         // Toàn bộ các thẻ <li> chứa tên tỉnh

    // 2. Lệnh MỞ popup khi bấm vào chữ Location trên menu
    btnOpenLocation.addEventListener('click', function(event) {
        event.preventDefault(); // Chống bị giật/nhảy trang lên đầu
        modalLocation.style.display = 'flex'; // Hiển thị khung ra
    });

    // 3. Lệnh ĐÓNG popup khi bấm nút Đóng (X)
    btnClose.addEventListener('click', function(event) {
        event.preventDefault();
        modalLocation.style.display = 'none'; // Ẩn khung đi
    });

    // 4. Xử lý khi bấm vào 1 Tỉnh/Thành phố bất kỳ
    listProvinces.forEach(function(province) {
        province.addEventListener('click', function() {
            
            // Bước 4a: Đi vòng quanh xóa hết class 'active' và cái icon dấu tích ở TẤT CẢ các tỉnh
            listProvinces.forEach(function(item) {
                item.classList.remove('active');
                const icon = item.querySelector('i'); // Tìm xem có thẻ <i> (dấu tích) nào không
                if (icon) {
                    icon.remove(); // Có thì xóa đi
                }
            });

            // Bước 4b: Thêm chữ màu đỏ và gắn cái icon dấu tích vào đúng cái tỉnh mà bạn vừa bấm
            this.classList.add('active');
            this.innerHTML += ' <i class="fa-solid fa-circle-check"></i>';

            // Bước 4c: (Tùy chọn) Chọn xong thì tự động đóng luôn popup cho mượt
            modalLocation.style.display = 'none';
        });
    });
</script>
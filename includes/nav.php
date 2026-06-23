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
        <li><a href="#"><i class="fa-solid fa-location-crosshairs"> </i> Vị trí <i class="fa-solid fa-angle-down"></i></a>
            
        </li>
        <form class="search-box" action="search.php" method="GET" style="display: inline-flex; align-items: center;">
            <i class="fa-solid fa-magnifying-glass"></i>
            <input type="text" name="keyword" placeholder="Bạn muốn mua gì hôm nay?">
        </form>
        <li><a>Giỏ hàng <i class="fa-solid fa-cart-shopping"></i></a></li>
        <li><a>Đăng nhập <i class="fa-regular fa-circle-user"></i></a></li>
    </ul>
</nav>

<!-- Khung vị trí -->
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
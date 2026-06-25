
// LOGIC XỬ LÝ TÌM KIẾM THÔNG MINH
document.addEventListener("DOMContentLoaded", function() {
    const searchInput = document.getElementById('search-input');
    const searchForm = document.getElementById('main-search-form');
    const suggestBox = document.getElementById('search-suggest-box');
    const recentList = document.getElementById('recent-list');
    const liveList = document.getElementById('live-list');
    const recentSearchesDiv = document.getElementById('recent-searches');
    const liveResultsDiv = document.getElementById('live-results');
    const clearRecentBtn = document.getElementById('clear-recent');

    // 1. Quản lý bộ nhớ trình duyệt (LocalStorage) cho Lịch sử tìm kiếm
    function getRecentSearches() {
        let searches = localStorage.getItem('ps_recent_searches');
        return searches ? JSON.parse(searches) : [];
    }

    function saveRecentSearch(keyword) {
        keyword = keyword.trim();
        if(!keyword) return;
        let searches = getRecentSearches();
        searches = searches.filter(item => item !== keyword); // Xóa từ trùng lặp
        searches.unshift(keyword); // Đẩy từ mới lên đầu
        if(searches.length > 5) searches.pop(); // Chỉ lưu tối đa 5 lịch sử
        localStorage.setItem('ps_recent_searches', JSON.stringify(searches));
    }

    function renderRecentSearches() {
        let searches = getRecentSearches();
        recentList.innerHTML = '';
        if(searches.length > 0) {
            searches.forEach(keyword => {
                let li = document.createElement('li');
                li.innerHTML = `<a href="search.php?keyword=${encodeURIComponent(keyword)}" class="recent-item">${keyword}</a>`;
                recentList.appendChild(li);
            });
            recentSearchesDiv.style.display = 'block';
        } else {
            recentSearchesDiv.style.display = 'none';
        }
    }

    // 2. Khi bấm chuột vào ô nhập liệu
    searchInput.addEventListener('focus', function() {
        if (this.value.trim() === '') {
            renderRecentSearches();
            liveResultsDiv.style.display = 'none';
            // Chỉ hiện hộp nếu có lịch sử
            if(getRecentSearches().length > 0) suggestBox.style.display = 'block';
        } else {
            suggestBox.style.display = 'block';
        }
    });

    // 3. Khi khách hàng đang gõ (Live Search)
    let typingTimer = null;
    searchInput.addEventListener('input', function() {
        clearTimeout(typingTimer);
        let q = this.value.trim();
        
        if(q === '') {
            renderRecentSearches();
            liveResultsDiv.style.display = 'none';
            suggestBox.style.display = getRecentSearches().length > 0 ? 'block' : 'none';
            return;
        }

        // Tạm ẩn lịch sử, chuẩn bị hiện kết quả trực tiếp
        suggestBox.style.display = 'block';
        recentSearchesDiv.style.display = 'none';
        liveList.innerHTML = '<li style="text-align: center; color: #888; font-size: 13px;"><i class="fa-solid fa-spinner fa-spin"></i> Đang tìm kiếm...</li>';
        liveResultsDiv.style.display = 'block';
        
        // Chờ khách hàng ngừng gõ 300ms rồi mới gửi lệnh lên server để tránh treo máy
        typingTimer = setTimeout(() => {
            fetch('ajax_search.php?q=' + encodeURIComponent(q))
            .then(res => res.json())
            .then(data => {
                liveList.innerHTML = '';
                if(data.length > 0) {
                    data.forEach(item => {
                        let priceText = item.min_price ? new Intl.NumberFormat('vi-VN').format(item.min_price) + 'đ' : 'Liên hệ';
                        let img = item.image ? 'assets/uploads/' + item.image : 'https://via.placeholder.com/45';
                        let li = document.createElement('li');
                        li.innerHTML = `
                            <a href="detail.php?id=${item.id}">
                                <img src="${img}" class="live-item-img">
                                <div class="live-item-info">
                                    <div class="live-item-name">${item.name}</div>
                                    <div class="live-item-price">${priceText}</div>
                                </div>
                            </a>
                        `;
                        liveList.appendChild(li);
                    });
                } else {
                    liveList.innerHTML = '<li style="color:#888; text-align:center; font-size: 13px;">Không tìm thấy sản phẩm phù hợp</li>';
                }
            });
        }, 300); 
    });

    // 4. Lưu lại lịch sử khi bấm Enter tìm kiếm
    searchForm.addEventListener('submit', function() {
        saveRecentSearch(searchInput.value);
    });

    // 5. Nút xóa lịch sử
    clearRecentBtn.addEventListener('click', function() {
        localStorage.removeItem('ps_recent_searches');
        suggestBox.style.display = 'none';
    });

    // 6. Ẩn hộp gợi ý khi bấm chuột ra ngoài
    document.addEventListener('click', function(e) {
        if(!searchForm.contains(e.target)) {
            suggestBox.style.display = 'none';
        }
    });
});

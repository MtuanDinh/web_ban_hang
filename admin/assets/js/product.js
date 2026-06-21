
const openBtn = document.getElementById("open");
const closeBtn = document.getElementById("close");
const modal = document.getElementById("modal");

// Mở Modal Thêm
openBtn.addEventListener('click', () => {
    modal.classList.add('show_modal');
});

// Đóng Modal Thêm bằng nút X
closeBtn.addEventListener('click', () => {
    modal.classList.remove('show_modal');
});

// Click ra ngoài khoảng đen để đóng Modal
window.addEventListener('click', e => {
    if (e.target === modal) {
        modal.classList.remove('show_modal');
    }
});

// Tự động ẩn thông báo Flash Message
setTimeout(function () {
    var toast = document.getElementById('toast_msg');
    if (toast) {
        toast.style.display = 'none';
    }
}, 3000);
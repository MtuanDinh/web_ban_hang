// 1. Chức năng ẩn/hiện mật khẩu
function toggleVisibility(inputId, iconElement) {
    var input = document.getElementById(inputId);
    if (input.type === "password") {
        input.type = "text";
        iconElement.classList.remove("fa-eye");
        iconElement.classList.add("fa-eye-slash");
    } else {
        input.type = "password";
        iconElement.classList.remove("fa-eye-slash");
        iconElement.classList.add("fa-eye");
    }
}

// 2. Chức năng kiểm tra Mật khẩu khớp Real-time
const pwd = document.getElementById('pwd');
const re_pwd = document.getElementById('re_pwd');
const matchMsg = document.getElementById('match-msg');
const btnSubmit = document.getElementById('btn-submit');

function checkPasswordMatch() {
    if (re_pwd.value === "") {
        matchMsg.textContent = "";
        btnSubmit.style.opacity = "1";
        btnSubmit.style.cursor = "pointer";
    } else if (pwd.value === re_pwd.value) {
        matchMsg.innerHTML = '<i class="fa-solid fa-circle-check"></i> Mật khẩu đã khớp';
        matchMsg.style.color = '#10b981'; // Màu xanh lá
        btnSubmit.style.opacity = "1";
        btnSubmit.style.cursor = "pointer";
    } else {
        matchMsg.innerHTML = '<i class="fa-solid fa-circle-xmark"></i> Mật khẩu chưa khớp';
        matchMsg.style.color = '#d70018'; // Màu đỏ
        // Làm mờ nút submit khi chưa khớp
        btnSubmit.style.opacity = "0.5";
        btnSubmit.style.cursor = "not-allowed";
    }
}

pwd.addEventListener('keyup', checkPasswordMatch);
re_pwd.addEventListener('keyup', checkPasswordMatch);
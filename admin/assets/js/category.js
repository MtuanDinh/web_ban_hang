//close
const close = document.getElementById("close");
//open
const open = document.getElementById("open");
//modal
const modal = document.getElementById("modal");

open.addEventListener('click', () => modal.classList.add('show_modal'));
close.addEventListener('click', () => modal.classList.remove('show_modal'));
window.addEventListener('click', e => {
    e.target === modal ? modal.classList.remove('show_modal') : false;
}
)

//flash_msg
setTimeout(function () {
    var toast = document.getElementById('toast_msg');
    if (toast) {
        toast.style.display = 'none';
    }
}, 3000);


const editModal = document.getElementById("editModal");

function openEditModal(buttonElement) {
    // 1. Rút dữ liệu từ các thuộc tính data-* của nút vừa bị bấm
    let id = buttonElement.getAttribute('data-id');
    let name = buttonElement.getAttribute('data-name');
    let parent = buttonElement.getAttribute('data-parent');

    // 2. Nhồi dữ liệu vào các ô input trong Edit Modal
    document.getElementById('edit_id_input').value = id;
    document.getElementById('edit_name_input').value = name;
    document.getElementById('edit_parent_select').value = parent; // JS tự động tìm option khớp value để selected!

    // 3. Hiển thị Modal
    editModal.classList.add('show_modal');
}

function closeEditModal() {
    editModal.classList.remove('show_modal');
}

// Bổ sung thêm tính năng click ra ngoài thì tắt cho Edit Modal
window.addEventListener('click', e => {
    if (e.target === editModal) {
        closeEditModal();
    }
});
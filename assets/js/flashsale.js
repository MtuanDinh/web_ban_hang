// Script tạo hiệu ứng đếm ngược sinh động cho Flash Sale
let h = 2, m = 45, s = 18;
setInterval(() => {
    s--;
    if(s < 0) { s = 59; m--; }
    if(m < 0) { m = 59; h--; }
    if(h < 0) { h = 2; } // Hết giờ thì tự reset lặp lại
    
    const boxes = document.querySelectorAll('.cd-box');
    if(boxes.length >= 3) {
        boxes[0].textContent = h.toString().padStart(2, '0');
        boxes[1].textContent = m.toString().padStart(2, '0');
        boxes[2].textContent = s.toString().padStart(2, '0');
    }
}, 1000);
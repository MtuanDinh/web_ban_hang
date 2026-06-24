document.addEventListener("DOMContentLoaded", function() {
    
    // Cấu hình chung cho màu chữ của Chart khớp với Dark Mode
    Chart.defaults.color = '#A0A0A0';
    Chart.defaults.font.family = "'Segoe UI', Tahoma, Geneva, Verdana, sans-serif";

    /* ==========================================
       1. VẼ BIỂU ĐỒ DOANH THU (LINE CHART)
       ========================================== */
    const canvasRevenue = document.getElementById('revenueChart');
    if (canvasRevenue) {
        const ctxRevenue = canvasRevenue.getContext('2d');
        
        // Tạo hiệu ứng đổ màu Gradient Neon dưới đường kẻ
        let gradient = ctxRevenue.createLinearGradient(0, 0, 0, 300);
        gradient.addColorStop(0, 'rgba(0, 229, 255, 0.4)'); // Xanh Cyan trên cùng
        gradient.addColorStop(1, 'rgba(0, 229, 255, 0.0)'); // Mờ dần xuống đáy

        new Chart(ctxRevenue, {
            type: 'line',
            data: {
                // Nhận dữ liệu từ biến toàn cục bên file PHP
                labels: chartLabels, 
                datasets: [{
                    label: 'Doanh thu (VNĐ)',
                    data: chartRevenue, 
                    borderColor: '#00E5FF',
                    backgroundColor: gradient,
                    borderWidth: 3,
                    pointBackgroundColor: '#121212',
                    pointBorderColor: '#00E5FF',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    fill: true,
                    tension: 0.4 // Tạo đường cong mượt mà
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: 'rgba(255, 255, 255, 0.05)' },
                        ticks: {
                            callback: function(value) {
                                return value.toLocaleString('vi-VN') + 'đ';
                            }
                        }
                    },
                    x: {
                        grid: { display: false } 
                    }
                }
            }
        });
    }

    /* ==========================================
       2. VẼ BIỂU ĐỒ TRẠNG THÁI ĐƠN HÀNG (DOUGHNUT)
       ========================================== */
    const canvasStatus = document.getElementById('statusChart');
    if (canvasStatus) {
        const ctxStatus = canvasStatus.getContext('2d');
        new Chart(ctxStatus, {
            type: 'doughnut',
            data: {
                labels: ['Chờ duyệt', 'Đang giao', 'Hoàn thành', 'Đã hủy'],
                datasets: [{
                    // Nhận dữ liệu từ biến toàn cục bên file PHP
                    data: chartStatus,
                    backgroundColor: [
                        'rgba(255, 183, 77, 0.8)', // Vàng cam
                        'rgba(0, 229, 255, 0.8)',  // Xanh Cyan
                        'rgba(0, 230, 118, 0.8)',  // Xanh lá
                        'rgba(255, 64, 129, 0.8)'  // Đỏ hồng
                    ],
                    borderColor: '#1E1E1E', // Khớp với nền form
                    borderWidth: 4,
                    hoverOffset: 5
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '70%', 
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { padding: 20, usePointStyle: true, pointStyle: 'circle' }
                    }
                }
            }
        });
    }
});
<?php
require_once("includes/connect_db.php");

// Bắt từ khóa người dùng gửi lên
if (isset($_GET['q'])) {
    $q = mysqli_real_escape_string($conn, trim($_GET['q']));
    
    // Nếu rỗng thì trả về mảng rỗng
    if ($q == '') { 
        echo json_encode([]); 
        exit; 
    }

    // Truy vấn: Tìm tên sản phẩm giống từ khóa, KÈM THEO giá thấp nhất của sản phẩm đó.
    // Lệnh LIMIT 2: Đảm bảo chỉ lấy ra tối đa 2 sản phẩm như bạn yêu cầu
    $sql = "SELECT p.id, p.name, p.image, MIN(pv.price) as min_price 
            FROM products p 
            LEFT JOIN product_variants pv ON p.id = pv.product_id 
            WHERE p.name LIKE '%$q%' 
            GROUP BY p.id 
            LIMIT 2";
    
    $res = mysqli_query($conn, $sql);
    $data = [];
    
    if ($res) {
        while ($row = mysqli_fetch_assoc($res)) {
            $data[] = $row;
        }
    }
    
    // Trả dữ liệu về cho Javascript dưới định dạng JSON
    header('Content-Type: application/json');
    echo json_encode($data);
}
?>
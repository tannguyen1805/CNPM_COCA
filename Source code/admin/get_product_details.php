<?php
require_once("../model/connect.php");

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $sql = "SELECT * FROM product_bill WHERE id = $id";
    $result = $conn->query($sql);
    
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        echo json_encode($row); // Trả về dữ liệu dưới dạng JSON
    } else {
        echo json_encode([]); // Không tìm thấy sản phẩm
    }
}

$conn->close();
?>

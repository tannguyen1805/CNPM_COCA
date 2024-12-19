<?php
session_start();
require_once('model/connect.php');

// Kiểm tra nếu không có ID đơn hàng trong GET
if (!isset($_GET['order_id'])) {
    header("Location: checkout.php");
    exit();
}

$order_id = $_GET['order_id'];

// Lấy thông tin đơn hàng từ cơ sở dữ liệu
$stmt = $conn->prepare("SELECT total_price FROM product_bill WHERE id = ?");
$stmt->bind_param("i", $order_id);
$stmt->execute();
$result = $stmt->get_result();
$order = $result->fetch_assoc();

if (!$order) {
    echo "<script>alert('Đơn hàng không tồn tại.');</script>";
    header("Location: checkout.php");
    exit();
}

// Giao diện thanh toán
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thanh Toán Đơn Hàng</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <style>
        body {
            background-color: #f8f9fa; /* Màu nền */
        }
        .container {
            margin-top: 50px;
            padding: 20px;
            background-color: #ffffff; /* Màu nền của container */
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        h2 {
            color: #000000; /* Màu đen cho tiêu đề */
            font-weight: bold; /* Chữ đậm */
        }
        h4 {
            color: #000000; /* Màu đen cho phụ đề */
            font-weight: bold; /* Chữ đậm */
        }
        .btn-secondary, .btn-primary {
            background-color: #ffc107; /* Màu vàng cho các nút */
            border: none; /* Không có viền */
        }
        .btn-primary:hover, .btn-secondary:hover {
            background-color: #e0a800; /* Màu vàng đậm khi hover */
        }
    </style>
</head>
<body>
<div class="container">
    <h2 class="text-center">Thanh Toán Đơn Hàng</h2>
    <h4 class="text-center">Thông tin thanh toán</h4>
    
    <div class="text-center">
        <p>Shop đã nhận đơn hàng của bạn.</p>
        <p><strong>ID đơn hàng của bạn:</strong> <?php echo htmlspecialchars($order_id); ?></p>
        <p><strong>Tổng Tiền:</strong> <?php echo number_format($order['total_price']); ?>₫</p>
        
        <a href="index.php" class="btn btn-primary btn-lg">Quay Về Trang Chủ</a>
        <a href="success.php?order_id=<?php echo htmlspecialchars($order_id); ?>" class="btn btn-secondary btn-lg">Xem Chi Tiết Đơn Hàng</a>
    </div>
</div>
</body>
</html>

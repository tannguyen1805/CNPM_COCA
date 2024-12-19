<?php
require_once('../model/connect.php'); // Ensure correct database connection path
include 'header.php';

// Check the database connection
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

// Fetch the order ID from the query string
$order_id = isset($_GET['order_id']) ? intval($_GET['order_id']) : 0;

if ($order_id == 0) {
    die('Đơn hàng không hợp lệ!');
}

?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chi Tiết Đơn Hàng</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<div class="container mt-5">
    <!-- Order Details -->
    <h2>Chi Tiết Đơn Hàng #<?php echo $order_id; ?></h2>
    <?php
    // Fetch the order details from the `product_bill` table
    $stmt = $conn->prepare("SELECT total_price, address, created_at FROM product_bill WHERE id = ?");
    if ($stmt === false) {
        die('Lỗi truy vấn SQL: ' . $conn->error);
    }
    $stmt->bind_param("i", $order_id);
    $stmt->execute();
    $order = $stmt->get_result()->fetch_assoc();

    // Fetch product details from the `order_items` table
    $stmt = $conn->prepare("SELECT product_name, quantity, price FROM order_items WHERE order_id = ?");
    if ($stmt === false) {
        die('Lỗi truy vấn SQL: ' . $conn->error);
    }
    $stmt->bind_param("i", $order_id);
    $stmt->execute();
    $items = $stmt->get_result();
    ?>
    <h4>Thông Tin Đơn Hàng:</h4>
    <table class="table table-bordered">
        <tr>
            <th>Tổng Giá:</th>
            <td><?php echo number_format($order['total_price']); ?>₫</td>
        </tr>
        <tr>
            <th>Địa Chỉ Giao Hàng:</th>
            <td><?php echo htmlspecialchars($order['address']); ?></td>
        </tr>
        <tr>
            <th>Ngày Đặt Hàng:</th>
            <td><?php echo date("d/m/Y H:i:s", strtotime($order['created_at'])); ?></td>
        </tr>
    </table>

    <h4>Chi Tiết Sản Phẩm:</h4>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Tên Sản Phẩm</th>
                <th>Số Lượng</th>
                <th>Giá</th>
                <th>Tổng Giá</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($item = $items->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($item['product_name']); ?></td>
                    <td><?php echo htmlspecialchars($item['quantity']); ?></td>
                    <td><?php echo number_format($item['price']); ?>₫</td>
                    <td><?php echo number_format($item['quantity'] * $item['price']); ?>₫</td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
    <a href="order-list.php" class="btn btn-primary">Quay Lại Quản Lý Đơn Hàng</a>
</div>

</body>
</html>

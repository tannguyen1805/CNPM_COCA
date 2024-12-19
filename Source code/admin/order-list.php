<?php
require_once('../model/connect.php'); // Ensure correct database connection path
include 'header.php';

// Check the database connection
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản Lý Đơn Hàng</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<div class="container mt-5">
    <!-- Order Management -->
    <h2>Quản Lý Đơn Hàng</h2>
    <?php
    // Fetch all order details from the `product_bill` table
    $stmt = $conn->prepare("SELECT id, total_price, address, created_at FROM product_bill");
    
    if ($stmt === false) {
        die('Lỗi truy vấn SQL: ' . $conn->error);
    }

    $stmt->execute();
    $orders = $stmt->get_result();
    ?>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID Đơn Hàng</th>
                <th>Tổng Giá</th>
                <th>Địa Chỉ Giao Hàng</th>
                <th>Ngày Đặt Hàng</th>
                <th>Chi Tiết</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($order = $orders->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $order['id']; ?></td>
                    <td><?php echo number_format($order['total_price']); ?>₫</td>
                    <td><?php echo htmlspecialchars($order['address']); ?></td>
                    <td><?php echo date("d/m/Y H:i:s", strtotime($order['created_at'])); ?></td>
                    <td>
                        <a href="order_detail.php?order_id=<?php echo $order['id']; ?>" class="btn btn-primary">Xem Chi Tiết</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
     <a href="product-list.php" class="btn btn-secondary mt-3">Quay Lại </a>

</div>
</body>
</html>

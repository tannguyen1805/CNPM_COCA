<?php
session_start();
require_once('model/connect.php');

// Kiểm tra nếu không có ID đơn hàng trong session
if (!isset($_SESSION['order_id']) || !isset($_SESSION['user_info'])) {
    header("Location: checkout.php");
    exit();
}

// Lấy thông tin đơn hàng từ cơ sở dữ liệu
$order_id = $_SESSION['order_id'];
$stmt = $conn->prepare("SELECT total_price, created_at, address FROM product_bill WHERE id = ?");
$stmt->bind_param("i", $order_id);
$stmt->execute();
$result = $stmt->get_result();
$order = $result->fetch_assoc();

// Kiểm tra nếu không tìm thấy đơn hàng
if (!$order) {
    echo "<script>alert('Đơn hàng không tìm thấy. Vui lòng liên hệ với bộ phận hỗ trợ.');</script>";
    header("Location: checkout.php");
    exit();
}

// Lấy thông tin người dùng
$user_info = $_SESSION['user_info'];

// Thêm thông tin thanh toán vào session (giả sử bạn đã có thông tin này)
$payment_method = isset($_SESSION['payment_method']) ? $_SESSION['payment_method'] : "Chưa xác định";

// Reset sản phẩm trong session, nhưng giữ lại các thông tin khác để in hóa đơn
if (isset($_SESSION['cart'])) {
    unset($_SESSION['cart']); // Xóa giỏ hàng trong session
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Xác Nhận Đơn Hàng</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<div class="container mt-5">
    <h2>Xác Nhận Đơn Hàng</h2>
    <div class="alert alert-success">
        <strong>Cảm ơn bạn đã đặt hàng!</strong> Đơn hàng của bạn đã được xử lý thành công.
    </div>

    <h4>Thông Tin Người Dùng:</h4>
    <table class="table table-bordered">
        <tbody>
            <tr>
                <th>Tên:</th>
                <td><?php echo htmlspecialchars($user_info['name']); ?></td>
            </tr>
            <tr>
                <th>Email:</th>
                <td><?php echo htmlspecialchars($user_info['email']); ?></td>
            </tr>
            <tr>
                <th>Số Điện Thoại:</th>
                <td><?php echo htmlspecialchars($user_info['phone']); ?></td>
            </tr>
            <tr>
                <th>Địa Chỉ Giao Hàng:</th>
                <td><?php echo htmlspecialchars($order['address']); ?></td>
            </tr>
        </tbody>
    </table>

    <h4>Chi Tiết Đơn Hàng:</h4>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Tên Sản Phẩm</th>
                <th>Số Lượng</th>
                <th>Giá</th>
                <th>Tổng Giá</th> <!-- Thêm cột Tổng Giá -->
            </tr>
        </thead>
        <tbody>
            <?php
            // Lấy thông tin chi tiết đơn hàng từ bảng order_items
            $stmt = $conn->prepare("SELECT product_name, quantity, price FROM order_items WHERE order_id = ?");
            $stmt->bind_param("i", $order_id);
            $stmt->execute();
            $items_result = $stmt->get_result();

            while ($item = $items_result->fetch_assoc()) {
                $total_price = $item['quantity'] * $item['price']; // Tính tổng giá cho sản phẩm
                echo "<tr>
                    <td>" . htmlspecialchars($item['product_name']) . "</td>
                    <td>" . htmlspecialchars($item['quantity']) . "</td>
                    <td>" . number_format($item['price']) . "₫</td>
                    <td>" . number_format($total_price) . "₫</td> <!-- Hiển thị tổng giá -->
                </tr>";
            }
            ?>
            <tr>
                <td colspan="3" class="text-right"><strong>Tổng:</strong></td>
                <td><?php echo number_format($order['total_price']); ?>₫</td>
            </tr>
        </tbody>
    </table>
    
    <h4>Thông Tin Thanh Toán:</h4>
    <p>Phương Thức Thanh Toán: <?php echo htmlspecialchars($payment_method); ?></p>
    <p>Ngày Đặt Hàng: <?php echo date("d/m/Y H:i:s", strtotime($order['created_at'])); ?></p>

    <div class="text-center">
        <a href="index.php" class="btn btn-primary">Quay Về Trang Chủ</a>
        <p class="mt-3">
            Bạn có muốn <a href="print_bill.php?order_id=<?php echo $order_id; ?>">lấy hóa đơn</a> không?
        </p>
    </div>
</div>
</body>
</html>
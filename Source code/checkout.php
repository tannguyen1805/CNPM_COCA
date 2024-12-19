<?php
session_start();
require_once('model/connect.php');

// Kiểm tra nếu giỏ hàng rỗng
if (empty($_SESSION['cart'])) {
    header("Location: view-cart.php");
    exit();
}

// Tính tổng giá trị đơn hàng để hiển thị
$total_price = 0;
foreach ($_SESSION['cart'] as $item) {
    $total_price += $item['price'] * $item['quantity'];
}

// Xử lý form khi người dùng xác nhận đơn hàng
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Lưu thông tin người dùng với kiểm tra dữ liệu đầu vào
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $address = trim($_POST['address']);
    
    if (empty($name) || empty($email) || empty($phone) || empty($address)) {
        echo "<script>alert('Vui lòng điền đầy đủ thông tin.');</script>";
    } else {
        // Tạo bản ghi mới trong cơ sở dữ liệu cho đơn hàng
        $stmt = $conn->prepare("INSERT INTO product_bill (total_price, address, created_at) VALUES (?, ?, NOW())");
        $stmt->bind_param("is", $total_price, $address);
        $stmt->execute();
        $order_id = $stmt->insert_id;

        // Lưu thông tin sản phẩm vào bảng `order_items`
        foreach ($_SESSION['cart'] as $item) {
            $stmt = $conn->prepare("INSERT INTO order_items (order_id, product_name, quantity, price) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("isii", $order_id, $item['name'], $item['quantity'], $item['price']);
            $stmt->execute();
        }

        // Lưu thông tin vào session
        $_SESSION['order_id'] = $order_id;
        $_SESSION['user_info'] = ['name' => $name, 'email' => $email, 'phone' => $phone];

        // Chuyển hướng tới trang thanh toán
        header("Location: payment.php?order_id=$order_id");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thanh Toán</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<div class="container">
    <h2>Thanh Toán</h2>
    
    <h4>Đơn Hàng Của Bạn:</h4>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Hình Ảnh Sản Phẩm</th>
                <th>Tên Sản Phẩm</th>
                <th>Số Lượng</th>
                <th>Giá</th>
                <th>Tổng</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($_SESSION['cart'] as $item): ?>
                <tr>
                    <td><img src="<?php echo htmlspecialchars($item['image']); ?>" width="70" height="70" alt="Product Image"></td>
                    <td><?php echo htmlspecialchars($item['name']); ?></td>
                    <td><?php echo htmlspecialchars($item['quantity']); ?></td>
                    <td><?php echo number_format($item['price']); ?>₫</td>
                    <td><?php echo number_format($item['price'] * $item['quantity']); ?>₫</td>
                </tr>
            <?php endforeach; ?>
            <tr>
                <td colspan="4" class="text-right"><strong>Tổng:</strong></td>
                <td><?php echo number_format($total_price); ?>₫</td>
            </tr>
        </tbody>
    </table>

    <!-- Form thông tin người dùng -->
    <h4>Thông Tin Của Bạn:</h4>
    <form action="checkout.php" method="post">
        <div class="form-group">
            <label for="name">Họ và Tên</label>
            <input type="text" name="name" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" name="email" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="phone">Số Điện Thoại</label>
            <input type="text" name="phone" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="address">Địa Chỉ Giao Hàng:</label>
            <input type="text" name="address" class="form-control" required>
        </div>

        <div class="text-right">
            <button type="submit" class="btn btn-success">Xác Nhận Đơn Hàng</button>
        </div>
    </form>
</div>
</body>
</html>

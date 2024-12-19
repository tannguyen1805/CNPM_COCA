<?php
session_start();
error_reporting(E_ALL ^ E_DEPRECATED);
require_once('model/connect.php');

// Kiểm tra giỏ hàng
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Xử lý cập nhật giỏ hàng
if (isset($_POST['update_cart'])) {
    foreach ($_POST['quantities'] as $id => $quantity) {
        $id = intval($id); // Đảm bảo ID là số nguyên
        $quantity = max(1, intval($quantity)); // Đảm bảo số lượng tối thiểu là 1

        // Cập nhật số lượng sản phẩm trong giỏ hàng
        foreach ($_SESSION['cart'] as &$item) {
            if ($item['id'] == $id) {
                $item['quantity'] = $quantity; // Cập nhật số lượng
                break;
            }
        }
    }

    // Chuyển hướng về trang giỏ hàng sau khi cập nhật
    header("Location: view-cart.php");
    exit();
}

// Tính tổng giá tiền
$total_price = array_reduce($_SESSION['cart'], function($carry, $item) {
    return $carry + ($item['price'] * $item['quantity']);
}, 0);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Giỏ Hàng</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<div class="container">
    <h2>Giỏ Hàng</h2>
    <form action="view-cart.php" method="post">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Hình Ảnh Sản Phẩm</th>
                    <th>Tên Sản Phẩm</th>
                    <th>Giá</th>
                    <th>Số Lượng</th>
                    <th>Tổng</th>
                    <th>Thao Tác</th> <!-- Cột thao tác -->
                </tr>
            </thead>
            <tbody>
                <?php if (empty($_SESSION['cart'])): ?>
                    <tr>
                        <td colspan="6" class="text-center">Giỏ hàng của bạn đang trống!</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($_SESSION['cart'] as $item):
                        $item_total = $item['price'] * $item['quantity'];
                    ?>
                    <tr>
                        <td><img src="<?php echo htmlspecialchars($item['image']); ?>" width="70" height="70"></td>
                        <td><?php echo htmlspecialchars($item['name']); ?></td>
                        <td><?php echo number_format($item['price']); ?>₫</td>
                        <td>
                            <input type="number" name="quantities[<?php echo $item['id']; ?>]" value="<?php echo htmlspecialchars($item['quantity']); ?>" min="1">
                        </td>
                        <td><?php echo number_format($item_total); ?>₫</td>
                        <td>
                            <a href="remove-item.php?remove=<?php echo $item['id']; ?>" class="btn btn-danger btn-sm">Xóa</a> <!-- Nút xóa -->
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <tr>
                        <td colspan="3" class="text-right"><strong>Tổng:</strong></td>
                        <td colspan="2"><?php echo number_format($total_price); ?>₫</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <div class="text-right">
            <button type="submit" name="update_cart" class="btn btn-primary">Cập Nhật Giỏ Hàng</button>
        </div>
    </form>

    <?php if (!empty($_SESSION['cart'])): ?>
        <div class="mt-4">
            <p>Bạn có muốn tiếp tục mua sắm hoặc tiến hành thanh toán không?</p>
            <a href="index.php" class="btn btn-secondary">Tiếp Tục Mua Sắm</a>
            <a href="checkout.php" class="btn btn-success">Tiến Hành Thanh Toán</a>
        </div>
    <?php endif; ?>
</div>
</body>
</html>

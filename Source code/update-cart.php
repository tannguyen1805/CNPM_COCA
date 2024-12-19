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

// Xử lý xóa sản phẩm khỏi giỏ hàng
if (isset($_GET['remove'])) {
    $remove_id = intval($_GET['remove']);
    foreach ($_SESSION['cart'] as $key => $item) {
        if ($item['id'] == $remove_id) {
            unset($_SESSION['cart'][$key]);
        }
    }
    $_SESSION['cart'] = array_values($_SESSION['cart']); // Reset array keys after removal
}

// Tính tổng giá tiền
$total_price = 0;
foreach ($_SESSION['cart'] as $item) {
    $item_total = $item['price'] * $item['quantity'];
    $total_price += $item_total;
}

// Gửi tổng giá tiền về cho giao diện nếu cần
echo json_encode([
    'total_price' => $total_price,
    'cart' => $_SESSION['cart'],
]);
?>

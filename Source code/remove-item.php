<?php
session_start();

// Kiểm tra xem giỏ hàng có tồn tại không
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Kiểm tra xem có ID trong URL không
if (isset($_GET['remove'])) { // Sử dụng 'remove' thay vì 'id'
    $remove_id = intval($_GET['remove']); // Chuyển đổi ID thành số nguyên để tránh lỗi

    // Lặp qua giỏ hàng để tìm sản phẩm có ID tương ứng
    foreach ($_SESSION['cart'] as $key => $item) {
        if ($item['id'] == $remove_id) {
            unset($_SESSION['cart'][$key]); // Xóa sản phẩm khỏi giỏ hàng
            $_SESSION['cart'] = array_values($_SESSION['cart']); // Đặt lại chỉ số mảng
            break; // Thoát vòng lặp sau khi xóa
        }
    }
}

// Chuyển hướng về trang giỏ hàng sau khi xóa
header("Location: view-cart.php");
exit();
?>

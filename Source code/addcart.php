<?php
session_start();
require_once("model/connect.php");

// Kiểm tra và làm sạch id từ GET
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Chuẩn bị câu lệnh SQL để ngăn chặn SQL injection
$stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$product = $result->fetch_assoc();

// Kiểm tra xem sản phẩm có tồn tại không
if (!$product) {
    echo json_encode(['success' => false, 'message' => 'Sản phẩm không tồn tại.']);
    exit;
}

// Khởi tạo giỏ hàng nếu chưa tồn tại
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Kiểm tra xem sản phẩm đã có trong giỏ hàng chưa
$isFound = false;
foreach ($_SESSION['cart'] as &$item) {
    if ($item['id'] == $product['id']) {
        $item['quantity']++; // Tăng số lượng nếu sản phẩm đã có
        $isFound = true;
        break;
    }
}

// Nếu sản phẩm không có trong giỏ hàng, thêm nó vào
if (!$isFound) {
    $product['quantity'] = 1; // Khởi tạo số lượng là 1 cho sản phẩm mới
    $_SESSION['cart'][] = $product; // Thêm sản phẩm mới vào giỏ hàng
}

// Cập nhật số lượng sản phẩm trong giỏ hàng
$prdCount = count($_SESSION['cart']);

// Kiểm tra nếu là AJAX request
if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
    echo json_encode(['success' => true, 'message' => 'Sản phẩm đã được thêm vào giỏ hàng.', 'count' => $prdCount]);
} else {
    // Nếu không phải AJAX, chuyển hướng đến index.php
    header('Location: index.php');
    exit();
}
?>

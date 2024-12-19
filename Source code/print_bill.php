<?php
session_start();

// Kiểm tra nếu thông tin đơn hàng và thông tin người dùng không có trong session, chuyển hướng về checkout hoặc trang success
if (!isset($_SESSION['order_id']) || !isset($_SESSION['user_info'])) {
    header("Location: checkout.php"); // Chuyển hướng về trang checkout nếu không có thông tin
    exit();
}

// Lấy thông tin đơn hàng từ session
$order_id = $_SESSION['order_id'];
$user_info = $_SESSION['user_info'];

// Lấy thông tin đơn hàng từ cơ sở dữ liệu
require_once('model/connect.php');
$stmt = $conn->prepare("SELECT total_price, created_at, address FROM product_bill WHERE id = ?");
$stmt->bind_param("i", $order_id);
$stmt->execute();
$result = $stmt->get_result();
$order = $result->fetch_assoc();

// Kiểm tra nếu không tìm thấy đơn hàng
if (!$order) {
    echo "<script>alert('Đơn hàng không tìm thấy. Vui lòng liên hệ với bộ phận hỗ trợ.');</script>";
    exit();
}

// Lấy thông tin chi tiết các sản phẩm trong đơn hàng
$stmt = $conn->prepare("SELECT product_name, quantity, price FROM order_items WHERE order_id = ?");
$stmt->bind_param("i", $order_id);
$stmt->execute();
$items_result = $stmt->get_result();

// Tạo nội dung cho file TXT (hóa đơn)
$txt_content = "HÓA ĐƠN ĐƠN HÀNG\n";
$txt_content .= "-----------------------------\n";
$txt_content .= "Thông Tin Người Dùng:\n";
$txt_content .= "Tên: " . htmlspecialchars($user_info['name']) . "\n";
$txt_content .= "Email: " . htmlspecialchars($user_info['email']) . "\n";
$txt_content .= "Số Điện Thoại: " . htmlspecialchars($user_info['phone']) . "\n";
$txt_content .= "Địa Chỉ Giao Hàng: " . htmlspecialchars($order['address']) . "\n";
$txt_content .= "Tổng Cộng: " . number_format($order['total_price'], 2) . "₫\n";
$txt_content .= "Ngày Đặt Hàng: " . date("d/m/Y H:i:s", strtotime($order['created_at'])) . "\n\n";
$txt_content .= "Chi Tiết Sản Phẩm:\n";

while ($item = $items_result->fetch_assoc()) {
    $total_price = $item['quantity'] * $item['price']; // Tính tổng giá cho từng sản phẩm
    $txt_content .= $item['product_name'] . " - SL: " . $item['quantity'] . " - Giá: " . number_format($item['price']) . "₫ - Tổng: " . number_format($total_price) . "₫\n";
}

$txt_content .= "\n-----------------------------\n";
$txt_content .= "Cảm ơn bạn đã mua hàng! Chúng tôi mong sẽ được phục vụ bạn lần sau!\n";  // Thêm dòng cảm ơn

// Tạo tên file TXT
$file_name = 'hoa_don_' . $order_id . '.txt';

// Lưu nội dung vào file TXT
if (file_put_contents($file_name, $txt_content) === false) {
    echo "Không thể tạo file hóa đơn.";
    exit();
}

// Tải file về cho người dùng
if (file_exists($file_name)) {
    header("Content-Description: File Transfer");
    header("Content-Type: text/plain");
    header("Content-Disposition: attachment; filename=" . basename($file_name));
    header("Expires: 0");
    header("Cache-Control: must-revalidate");
    header("Pragma: public");
    header("Content-Length: " . filesize($file_name));
    readfile($file_name);

    // Xóa file sau khi tải xong để giải phóng dung lượng
    unlink($file_name);
    exit();
} else {
    echo "File hóa đơn không tồn tại.";
    exit();
}
?>

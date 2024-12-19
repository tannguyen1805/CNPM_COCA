<?php
    require_once("../model/connect.php");

    // Kiểm tra nếu có id được truyền đến
    if (isset($_GET['id'])) {
        $id = $_GET['id'];

        // Câu lệnh SQL để xóa dữ liệu
        $sql = "DELETE FROM product_bill WHERE id = $id";

        if ($conn->query($sql) === TRUE) {
            // Chuyển hướng trở lại với thông báo thành công
            header("Location: order-list.php?ps=1");
        } else {
            // Chuyển hướng trở lại với thông báo lỗi
            header("Location: order-list.php?pf=1");
        }
    }

    $conn->close();
?>

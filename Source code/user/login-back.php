<?php
session_start();
require_once('../model/connect.php');

if (isset($_POST['submit'])) {
    if (isset($_POST['username']) && isset($_POST['password'])) {
        $input = $_POST['username']; // Đây có thể là username hoặc email
        $password = $_POST['password'];

        // Mã hóa mật khẩu nhập vào bằng MD5
        $hashedPassword = md5($password);

        // Truy vấn kiểm tra username hoặc email
        $sql = "SELECT * FROM users WHERE (username = '$input' OR email = '$input') AND password = '$hashedPassword'";
        $result = mysqli_query($conn, $sql);

        if ($result && mysqli_num_rows($result) > 0) {
            $user = mysqli_fetch_assoc($result);

            // Lưu thông tin người dùng vào session
            $_SESSION['username'] = $user['username'];

            // Kiểm tra vai trò người dùng và chuyển hướng
            if ($user['role'] == 0) { // role = 0
                header("Location: ../admin/product-list.php");
                exit();
            } else if ($user['role'] == 1) { // role = 1
                header("Location: ../index.php");
                exit();
            } else {
                echo "Vai trò không hợp lệ!";
            }
        } else {
            echo "Tên đăng nhập, email hoặc mật khẩu không đúng!";
        }
    }
}
?>
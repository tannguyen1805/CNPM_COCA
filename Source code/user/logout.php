<?php
session_start();

// Xóa tất cả các biến trong session
$_SESSION = array();

// Nếu cần thiết, xóa cookie session
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Cuối cùng, hủy session
session_destroy();

// Chuyển hướng về trang index.php
header("Location: ../index.php");
exit();
?>

<?php
require_once("../model/connect.php");

$id = $_GET['id'];

$sql = "SELECT * FROM product_bill WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $customer = $result->fetch_assoc();
    echo json_encode($customer);
} else {
    echo json_encode([]);
}

$stmt->close();
$conn->close();
?>

<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'Admin') {
    echo "Access denied. Admins only.";
    exit();
}

$item_id = $_GET['id'];
$item = $conn->query("SELECT * FROM Items WHERE item_id = $item_id")->fetch_assoc();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $item_code = $_POST['item_code'];
    $description = $_POST['description'];
    $vendor_id = $_POST['vendor_id'];
    $location = $_POST['location'];
    $stock_quantity = $_POST['stock_quantity'];
    $reorder_status = $_POST['reorder_status'];
    $price_per_unit = $_POST['price_per_unit'];
    $total_value = $_POST['total_value'];
    $last_order_date = $_POST['last_order_date'];

    $sql = "UPDATE Items SET item_code = '$item_code', description = '$description', vendor_id = $vendor_id,
            location = '$location', stock_quantity = $stock_quantity, reorder_status = '$reorder_status',
            price_per_unit = $price_per_unit, total_value = $total_value, last_order_date = '$last_order_date'
            WHERE item_id = $item_id";

    if ($conn->query($sql) === TRUE) {
        echo "Record updated successfully";
    } else {
        echo "Error updating record: " . $conn->error;
    }
}
?>

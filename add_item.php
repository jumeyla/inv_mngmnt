<?php
session_start();
include 'db.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check user session
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'Admin') {
    echo "Access denied. Admins only.";
    exit();
}

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get POST data and sanitize
    $item_code = trim($_POST['item_code']);
    $description = trim($_POST['description']);
    $vendor_id = (int)$_POST['vendor_id']; // Ensure this is an integer
    $location = trim($_POST['location']);
    $stock_quantity = (int)$_POST['stock_quantity']; // Ensure this is an integer
    $reorder_status = ($_POST['reorder_status'] === 'OK') ? 0 : 1; // Convert to 0 or 1
    $price_per_unit = (float)$_POST['price_per_unit']; // Ensure this is a float
    $total_value = (float)$_POST['total_value']; // Ensure this is a float
    $last_order_date = $_POST['last_order_date']; // Date should be formatted correctly

    // Prepare SQL statement
    $stmt = $conn->prepare("INSERT INTO Items (item_code, description, vendor_id, location, stock_quantity, reorder_status, price_per_unit, total_value, last_order_date)
                             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");

    // Bind parameters
    $stmt->bind_param("ssissdids", $item_code, $description, $vendor_id, $location, $stock_quantity, $reorder_status, $price_per_unit, $total_value, $last_order_date);

    // Execute the prepared statement
    if ($stmt->execute()) {
        // Redirect back to the main page after successful insertion
        header("Location: product.php"); // Change 'main_page.php' to your actual main page URL
        exit(); // Ensure no further code is executed after the redirect
    } else {
        echo "Error: " . $stmt->error; // Provide detailed error message
    }

    // Close the statement
    $stmt->close();
}

// Fetch vendors for the dropdown
$vendors = $conn->query("SELECT * FROM Vendors");
?>

<?php
include 'db.php'; // Include the database connection

// Check if an item ID is provided via POST
if (isset($_POST['item_id'])) {
    $itemId = (int)$_POST['item_id'];

    // Prepare the DELETE statement
    $stmt = $conn->prepare("DELETE FROM items WHERE item_id = ?");
    $stmt->bind_param("i", $itemId);
    
    // Execute the statement
    if ($stmt->execute()) {
        // Redirect back to the product page with a success message
        header("Location: product.php?msg=Item+deleted+successfully");
    } else {
        // Redirect back to the product page with an error message
        header("Location: product.php?msg=Error+deleting+item");
    }

    $stmt->close();
}

// Close the connection
$conn->close();
?>

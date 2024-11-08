<?php
include 'db.php'; // Include the database connection

// Fetch data from the database
$query = "SELECT item_id, item_code, description, price_per_unit, stock_quantity, location, total_value, last_order_date 
          FROM items";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory Report</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        h2 { text-align: center; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        table, th, td { border: 1px solid #333; padding: 8px; text-align: center; }
        th { background-color: #007bff; color: white; }
        .no-data { text-align: center; font-size: 1.2em; color: #888; }
        .print-button {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            cursor: pointer;
        }
    </style>
</head>
<body>

    <h2>Inventory Report</h2>

    <?php if ($result->num_rows > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>Item ID</th>
                    <th>Item Code</th>
                    <th>Description</th>
                    <th>Price Per Unit</th>
                    <th>Stock Quantity</th>
                    <th>Location</th>
                    <th>Total Value</th>
                    <th>Last Order Date</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['item_id']); ?></td>
                        <td><?php echo htmlspecialchars($row['item_code']); ?></td>
                        <td><?php echo htmlspecialchars($row['description']); ?></td>
                        <td><?php echo htmlspecialchars($row['price_per_unit']); ?></td>
                        <td><?php echo htmlspecialchars($row['stock_quantity']); ?></td>
                        <td><?php echo htmlspecialchars($row['location']); ?></td>
                        <td><?php echo htmlspecialchars($row['total_value']); ?></td>
                        <td><?php echo htmlspecialchars($row['last_order_date']); ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p class="no-data">No data available to display in the report.</p>
    <?php endif; ?>

    <!-- Button to print/save as PDF -->
    <button onclick="window.print()" class="print-button">Print or Save as PDF</button>

</body>
</html>

<?php $conn->close(); ?>

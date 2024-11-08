<?php
// Include the database connection
include 'db.php';

// Define how many results to display per page
$results_per_page = 10;

// Determine the total number of sales records in the sales table
$result = $conn->query("SELECT COUNT(*) AS totalSales FROM sales");
$totalSales = $result->fetch_assoc()['totalSales'];

// Determine the total number of pages available
$total_pages = ceil($totalSales / $results_per_page);

// Get the current page number from the URL, default to 1 if not set
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;

// Calculate the starting limit for the SQL query
$start_from = ($page - 1) * $results_per_page;

// Query to fetch data for the sales table
$salesResult = $conn->query("
    SELECT 
        s.sale_id, 
        s.sale_date, 
        s.total_amount, 
        s.customer_name, 
        i.item_code, 
        i.description 
    FROM 
        sales s 
    JOIN 
        items i ON s.item_id = i.item_id
    ORDER BY 
        s.sale_date DESC 
    LIMIT $start_from, $results_per_page
");

// Close the connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sales Tracking</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            background-image: url('img/bg2.jpg');
            background-size: cover;
            background-position: center;
            color: white;
            display: flex; /* Enable flex layout for body */
        }
        #sidebar-wrapper {
            width: 250px;
            min-height: 100vh;
            background-color: rgba(255, 255, 255, 0.3); 
            color: black; 
            padding: 20px;
            box-shadow: 2px 0 5px rgba(0, 0, 0, 0.2);
            border-radius: 10px;
        }
        #sidebar-wrapper h4 {
            font-family: 'Gill Sans', 'Gill Sans MT', Calibri, 'Trebuchet MS', sans-serif;
            font-weight: bold;
            color: black; 
            text-transform: uppercase;
            margin-bottom: 10px;
        }
        .menu-item {
            display: flex;
            align-items: center;
            padding: 10px 15px;
            margin-bottom: 10px;
            background-color: transparent; 
            color: black; 
            text-decoration: none;
            font-size: 16px;
            font-weight: 500;
            border-radius: 8px;
            transition: background-color 0.2s ease, color 0.2s ease;
        }
        .menu-item:hover {
            background-color: #495057; 
            color: #007bff; 
        }
        .menu-item i {
            margin-right: 10px;
            background-color: #6c757d; 
            padding: 8px;
            border-radius: 5px;
            font-size: 18px;
        }
        #content-wrapper {
            flex-grow: 1; /* Allow content area to grow */
            padding: 20px; /* Add some padding */
        }
        .table {
            background-color: rgba(255, 255, 255, 0.9); /* Table background */
            border-radius: 8px;
            overflow: hidden;
            margin-bottom: 20px; /* Add margin for spacing */
        }
        .table th,
        .table td {
            padding: 12px; /* Increased padding for better spacing */
            border: 1px solid rgba(0, 0, 0, 0.1); /* Add borders to table cells */
        }
        .table th {
            background-color: #007bff; /* Header background color */
            color: white; /* Header text color */
        }
        .table tbody tr {
            transition: background-color 0.2s; /* Smooth background change on hover */
        }
        .table tbody tr:hover {
            background-color: rgba(0, 123, 255, 0.1); /* Light blue hover effect */
        }
        .table tbody tr:nth-child(even) {
            background-color: rgba(0, 0, 0, 0.05); /* Alternate row color for readability */
        }
        .logo {
            width: 100%; 
            max-width: 150px; 
            margin: 0 auto 20px;
            display: block; 
            height: auto; 
            object-fit: contain; 
            border-radius: 50%; 
            overflow: hidden; 
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div id="sidebar-wrapper">
        <img src="img/Logo.png" alt="Logo" class="logo">
        <h1>Inventory</h1>
        <a href="user_index.php" class="menu-item"> <!-- Back to Dashboard Link -->
            <i class="fas fa-tachometer-alt"></i>
            Back to Dashboard
        </a>

        <a href="inventory.php" class="menu-item">
                <i class="fas fa-box"></i>
                Product List
            </a>

            <a href="supplier_information.php" class="menu-item"> <!-- Added Supplier Information List -->
            <i class="fas fa-users"></i>
               Supplier Informations
            </a>

            <a href="order_history.php" class="menu-item"> <!-- Added Order History -->
            <i class="fas fa-history"></i>
               Order History
            </a>
            
        <a href="logout.php" class="menu-item">
            <i class="fas fa-sign-out-alt"></i>
            Logout
        </a>
    </div>

    <!-- Main content area -->
    <div id="content-wrapper">
        <div class="container mt-5">
            <h2 class="text-center mb-4">Sales Tracking</h2>

            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Sale ID</th>
                        <th>Sale Date</th>
                        <th>Customer Name</th>
                        <th>Item Code</th>
                        <th>Description</th>
                        <th>Total Amount</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($salesResult->num_rows > 0): ?>
                        <?php while ($row = $salesResult->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['sale_id']); ?></td>
                                <td><?php echo htmlspecialchars($row['sale_date']); ?></td>
                                <td><?php echo htmlspecialchars($row['customer_name']); ?></td>
                                <td><?php echo htmlspecialchars($row['item_code']); ?></td>
                                <td><?php echo htmlspecialchars($row['description']); ?></td>
                                <td><?php echo htmlspecialchars(number_format($row['total_amount'], 2)); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center">No sales records found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>

            <!-- Pagination -->
            <nav aria-label="Page navigation">
                <ul class="pagination justify-content-center">
                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                        <li class="page-item <?php echo ($i == $page) ? 'active' : ''; ?>">
                            <a class="page-link" href="sales_tracking.php?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                        </li>
                    <?php endfor; ?>
                </ul>
            </nav>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

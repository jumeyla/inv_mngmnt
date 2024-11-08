<?php
session_start();
include 'db.php'; // Include the database connection

// Check if the user is logged in and has the required role (optional)
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'Admin') {
    echo "Access denied. Admins only.";
    exit();
}

// Fetch all vendors from the database
$result = $conn->query("SELECT vendor_id, vendor_name FROM vendors");

// Close the connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vendor List</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
         /* Sidebar styling */
         #sidebar-wrapper {
            width: 250px;
            min-height: 100vh;
            background-color: #343a40; /* Darker background for sidebar */
            color: white; /* Change text color to white for better contrast */
            padding: 20px;
            box-shadow: 2px 0 5px rgba(0, 0, 0, 0.2);
        }
        #sidebar-wrapper h2 {
            font-size: 14px;
            font-weight: bold;
            color: #f8f9fa; /* Lighter color for the header */
            text-transform: uppercase;
            margin-bottom: 20px;
        }
        .menu-item {
            display: flex;
            align-items: center;
            padding: 10px 15px;
            margin-bottom: 10px;
            background-color: transparent; /* Transparent background for items */
            color: #f8f9fa; /* White text for items */
            text-decoration: none;
            font-size: 16px;
            font-weight: 500;
            border-radius: 8px;
            transition: background-color 0.2s ease, color 0.2s ease;
        }
        .menu-item:hover {
            background-color: #495057; /* Darker shade on hover */
            color: #007bff; /* Change text color on hover */
        }
        .menu-item i {
            margin-right: 10px;
            background-color: #6c757d; /* Grey background for icons */
            padding: 8px;
            border-radius: 5px;
            font-size: 18px;
        }
    </style>
</head>
<body>
<div class="d-flex" id="wrapper">
        <!-- Sidebar -->
        <div id="sidebar-wrapper">
            <h1>Inventory</h1>
            <a href="admin_index.php" class="menu-item"> <!-- Back to Dashboard Link -->
                <i class="fas fa-tachometer-alt"></i>
                Back to Dashboard
            </a>

            <a href="logout.php" class="menu-item">
                <i class="fas fa-sign-out-alt"></i>
                Logout
            </a>
        </div>

    <div class="container mt-4">
        <h1>Vendor List</h1>

        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th>Vendor ID</th>
                        <th>Vendor Name</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result->num_rows > 0): ?>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $row['vendor_id']; ?></td>
                                <td><?php echo htmlspecialchars($row['vendor_name']); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="3" class="text-center">No vendors found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

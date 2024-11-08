<?php
include 'db.php'; // Include the database connection

// Define how many results to display per page
$results_per_page = 10;

// Determine the total number of items in the items table
$result = $conn->query("SELECT COUNT(*) AS totalItems FROM items");
$totalItems = $result->fetch_assoc()['totalItems'];

// Get the current page number from the URL, default to 1 if not set
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;

// Determine the total number of pages available
$total_pages = ceil($totalItems / $results_per_page);

// Calculate the starting limit for the SQL query
$start_from = ($page - 1) * $results_per_page;

// Query to fetch data for the items table with vendor names, applying pagination
$itemsResult = $conn->query("
    SELECT 
        i.item_id, 
        i.item_code,        
        i.description, 
        i.price_per_unit, 
        v.vendor_name, 
        i.stock_quantity,
        i.location,            
        i.reorder_status,      
        i.total_value,         
        i.last_order_date      
    FROM 
        items i 
    JOIN 
        vendors v ON i.vendor_id = v.vendor_id
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
    <title>Product List</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body{
            background-image: url(img/bg2.jpg);
            background-size: cover;
            background-position: center;
            position: relative;
        }
        /* Add your custom styles here */
        .table-container {
            margin-top: 30px;
            background-color: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        .table th, .table td {
            vertical-align: middle;
        }
        .pagination {
            justify-content: center; /* Center the pagination */
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

        /* Main Content */
        #page-content-wrapper {
            flex-grow: 1;
            overflow-y: auto;
            padding: 20px;
            background-image: url(img/bg2.jpg);
            background-size: cover;
            background-position: center;
            position: relative;
        }

        .search-bar {
            display: flex;
            align-items: center;
            gap: 10px;
       }

        .search-input {
            padding: 10px;
            border: 1px solid #ced4da;
            border-radius: 5px;
            width: 1000px;
            height: 40px;
            font-size: 16px;
       }

       .search-button {
            background-color: #007bff;
            color: #fff;
            border: none;
            padding: 10px 15px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 10px;
       }
        /* Profile Bar Styles */
        .profile-bar {
            display: flex; 
            align-items: center; 
            gap: 10px;
            border-left: 1px solid #ced4da; 
            padding-left: 20px; 
            margin-right: 20px;
       }

       .profile-picture {
            width: 40px; 
            height: 40px;
            border-radius: 50%; 
       }

       .profile-info {
            text-align: left; 
       }

       .profile-name {
            font-size: 16px; 
            margin: 0; 
            color: #333;
       }

       .profile-role {
            font-size: 12px;
            color: #6c757d;
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

        nav.navbar {
            background-color: rgba(52, 58, 64, 0.5);
            backdrop-filter: blur(10px);    
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.2); 
            border-radius: 15px;
       }


    </style>
</head>
<body>
<div class="d-flex" id="wrapper">
    <!-- Sidebar -->
    <div id="sidebar-wrapper">
        <img src="img/Logo.png" alt="Logo" class="logo">
        <h1>Inventory</h1>
        <a href="user_index.php" class="menu-item"> <!-- Back to Dashboard Link -->
            <i class="fas fa-tachometer-alt"></i>
            Back to Dashboard
        </a>

        <a href="supplier_information.php" class="menu-item"> <!-- Added Supplier Information List -->
            <i class="fas fa-users"></i>
               Supplier Informations
            </a>

            <a href="order_history.php" class="menu-item"> <!-- Added Order History -->
            <i class="fas fa-history"></i>
               Order History
            </a>

        <a href="generate_report.php" class="menu-item">
               <i class="fas fa-file-pdf"></i>
               Generate Report
            </a>

        <a href="logout.php" class="menu-item">
            <i class="fas fa-sign-out-alt"></i>
            Logout
        </a>
    </div>

    <div id="page-content-wrapper">
        <nav class="navbar navbar-expand-lg navbar-light bg-light border-bottom">
            <div class="container-fluid">
            </div>

            <div class="search-bar">
                <input type="text" placeholder="Search..." class="search-input" id="searchInput">
                <button class="search-button"><i class="fas fa-search"></i></button>
            </div>

            <div class="profile-bar">
                <img src="img/profile.png" alt="Profile Picture" class="profile-picture"> <!-- Example profile image -->
                <div class="profile-info">
                    <h5 class="profile-name">Name</h5>
                    <p class="profile-role">Admin</p>
                </div>
            </div>

        </nav>

        <div class="container-fluid mt-4">
            <h3 class="text-center">PRODUCT LIST</h3>

            <div class="table-container">
                <table class="table table-bordered table-hover" id="itemsTable">
                    <thead>
                        <tr>
                            <th>Item ID</th>
                            <th>Item Code</th>
                            <th>Description</th>
                            <th>Price</th>
                            <th>Vendor</th>
                            <th>Location</th>
                            <th>Stock Quantity</th>
                            <th>Reorder Status</th>
                            <th>Total Value</th>
                            <th>Last Order Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($row = $itemsResult->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $row['item_id']; ?></td>
                            <td><?php echo htmlspecialchars($row['item_code']); ?></td>
                            <td><?php echo $row['description']; ?></td>
                            <td><?php echo number_format($row['price_per_unit'], 2); ?></td>
                            <td><?php echo $row['vendor_name']; ?></td>
                            <td><?php echo htmlspecialchars($row['location']); ?></td>
                            <td><?php echo htmlspecialchars($row['stock_quantity']); ?></td>
                            <td><?php echo htmlspecialchars($row['reorder_status'] ? 'OK' : 'RE-ORDER'); ?></td>
                            <td><?php echo number_format($row['total_value'], 2); ?></td>
                            <td><?php echo htmlspecialchars($row['last_order_date']); ?></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>

                <!-- Pagination inside table-container -->
                <nav aria-label="Page navigation">
                    <ul class="pagination justify-content-center mb-3">
                        <?php if ($page > 1): ?>
                            <li class="page-item">
                                <a class="page-link" href="?page=<?php echo $page - 1; ?>" aria-label="Previous">
                                    <span aria-hidden="true">&laquo;</span>
                                </a>
                            </li>
                        <?php endif; ?>

                        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                            <li class="page-item <?php if ($i == $page) echo 'active'; ?>">
                                <a class="page-link" href="?page=<?php echo $i; ?>">
                                    <?php echo $i; ?>
                                </a>
                            </li>
                        <?php endfor; ?>

                        <?php if ($page < $total_pages): ?>
                            <li class="page-item">
                                <a class="page-link" href="?page=<?php echo $page + 1; ?>" aria-label="Next">
                                    <span aria-hidden="true">&raquo;</span>
                                </a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </nav>
            </div>
        </div>
    </div>

    <!-- Bootstrap and JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>

    <!-- Search Filter Script -->
    <script>
        document.getElementById('searchInput').addEventListener('input', function() {
            // Get the input value and convert to lower case
            let filter = this.value.toLowerCase();
            // Get all table rows in tbody
            let rows = document.querySelectorAll('#itemsTable tbody tr');
            
            // Loop through rows and filter
            rows.forEach(row => {
                let text = row.textContent.toLowerCase();
                row.style.display = text.includes(filter) ? '' : 'none';
            });
        });
    </script>
</body>
</html>

<?php
include 'db.php'; // Include the database connection

// Define how many results to display per page
$results_per_page = 5;

// Determine the total number of items in the items table
$result = $conn->query("SELECT COUNT(*) AS totalItems FROM items");
$totalItems = $result->fetch_assoc()['totalItems'];

// Determine the total number of vendors
$resultVendors = $conn->query("SELECT COUNT(DISTINCT vendor_id) AS totalVendors FROM vendors");
$totalVendors = $resultVendors->fetch_assoc()['totalVendors'];

// Define a threshold for items to reorder
$reorderThreshold = 5; // Set your threshold here
$resultReorderItems = $conn->query("SELECT COUNT(*) AS reorderItems FROM items WHERE stock_quantity < $reorderThreshold");
$reorderItems = $resultReorderItems->fetch_assoc()['reorderItems'];

// Determine the total number of pages available
$total_pages = ceil($totalItems / $results_per_page);

// Get the current page number from the URL, default to 1 if not set
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;

// Calculate the starting limit for the SQL query
$start_from = ($page - 1) * $results_per_page;

// Query to fetch data for the items table with vendor names, applying pagination
$itemsResult = $conn->query("
    SELECT 
        i.item_id, 
        i.item_code,        -- Added item_code to the SELECT statement
        i.description, 
        i.price_per_unit, 
        v.vendor_name, 
        i.stock_quantity,
        i.location,            -- Added location
        i.reorder_status,      -- Added reorder status
        i.total_value,         -- Added total value
        i.last_order_date      -- Added last order date
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
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        /* General styling */
        body {
            display: flex;
            flex-direction: column;
            height: 100vh;
            margin: 0;
            background-image: url(img/bg2.jpg);
        }
        #wrapper {
            display: flex;
            flex-grow: 1;
            overflow: hidden;
        }
        /* Sidebar styling */
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
        .search-bar {
            display: flex;
            align-items: center;
            gap: 10px;
       }

        .search-input {
            padding: 10px;
            border: 1px solid #ced4da;
            border-radius: 5px;
            width: 250px;
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

        .navbar h2 {
            flex-grow: 1; 
            text-align: left; 
            font-family: Arial-Black;
            font-weight: bold;
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
        /* Dashboard header cards */
        .card {
            border: none;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1); /* Add shadow to cards */
            transition: transform 0.2s; /* Add hover effect */
        }
        .card:hover {
            transform: translateY(-5px); /* Lift card effect on hover */
        }
        .card-body {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100%; /* Ensure full height */
        }
    </style>
</head>
<body>

    <div class="d-flex" id="wrapper">
        <!-- Sidebar -->
        <div id="sidebar-wrapper">
        <img src="img/Logo.png" alt="Logo" class="logo">
            <h4>INVENTORY</h4>
            <a href="product.php" class="menu-item">
                <i class="fas fa-box"></i>
                Product List
            </a>
            <a href="vendor_list.php" class="menu-item"> <!-- New Link for Vendor List -->
            <i class="fas fa-list"></i>
                Vendor List
            </a>

            <a href="logout.php" class="menu-item">
                <i class="fas fa-sign-out-alt"></i>
                Logout
            </a>
        </div>

        <!-- Page Content -->
        <div id="page-content-wrapper">
            <nav class="navbar navbar-expand-lg navbar-light bg-light border-bottom">
                <div class="container-fluid">
                    <h2 class="ms-auto">ADMIN DASHBOARD</h2>
                </div>

                <div class="search-bar">
                        <input type="text" placeholder="Search..." class="search-input">
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
                <!-- Message Display -->
                <?php if (isset($_GET['msg'])): ?>
                    <div class="alert alert-info text-center" role="alert">
                        <?php echo htmlspecialchars($_GET['msg']); ?>
                    </div>
                <?php endif; ?>

                <!-- Dashboard Header Widgets -->
                <div class="row text-center">
                    <div class="col-md-4">
                        <div class="card bg-primary text-white mb-4">
                            <div class="card-body">
                                <h5>Total Items</h5>
                                <h2><?php echo $totalItems; ?></h2>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card bg-success text-white mb-4">
                            <div class="card-body">
                                <h5>Total Vendors</h5>
                                <h2><?php echo $totalVendors; ?></h2>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card bg-warning text-white mb-4">
                            <div class="card-body">
                                <h5>Items to Reorder</h5>
                                <h2><?php echo $reorderItems; ?></h2>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap and JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
</body>
</html>
                    
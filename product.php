<?php
include 'db.php'; // Include the database connection

// Error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

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
        body {
            background-image: url(img/bg2.jpg);
            background-size: cover;
            background-position: center;
            position: relative;
        }

        
        .table-container {
            margin-top: 30px;
            background-color: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .table {
            width: 100%;
            border-collapse: collapse; /* Ensure borders don't double up */
        }

        .table th, .table td {
            border: 1px solid #dee2e6; /* Add a border to table cells */
            padding: 10px; /* Add some padding for cell content */
        }

        .table th {
            background-color: #f8f9fa; /* Light gray background for headers */
            text-align: left; /* Align text to the left */
        }

        .table tr:nth-child(even) {
            background-color: #f2f2f2; /* Alternate row color */
        }

        .table tr:hover {
            background-color: #e9ecef; /* Highlight row on hover */
        }
        .pagination {
            justify-content: center;
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

        /* Modal styling */
        .modal {
            display: none; /* Hidden by default */
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5); /* Semi-transparent background */
            z-index: 1050;
            overflow: auto; /* Enable scrolling if modal content is too tall */
        }

        .modal-content {
            position: relative;
            background-color: #fff;
            margin: auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
            max-width: 500px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            top: 50%;
            transform: translateY(-50%);
            z-index: 1060;
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }

        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }
    </style>
</head>
<body>
<div class="d-flex" id="wrapper">
    <!-- Sidebar -->
    <div id="sidebar-wrapper">
        <img src="img/Logo.png" alt="Logo" class="logo">
        <h1>INVENTORY</h1>
        <a href="admin_index.php" class="menu-item">
            <i class="fas fa-tachometer-alt"></i>
            Back to Dashboard
        </a>
        <a href="#" class="menu-item" onclick="openModal()">
            <i class="fas fa-plus-square"></i>
            Add Item
        </a>
        <a href="edit_items.php" class="menu-item">
            <i class="fas fa-edit"></i>
            Edit Items
        </a>
        <a href="logout.php" class="menu-item">
            <i class="fas fa-sign-out-alt"></i>
            Logout
        </a>
    </div>

    <div id="page-content-wrapper">
        <nav class="navbar navbar-expand-lg navbar-light bg-light border-bottom">
            <div class="container-fluid"></div>

            <div class="search-bar">
    <input type="text" id="searchInput" placeholder="Search..." class="search-input">
    <button class="search-button"><i class="fas fa-search"></i></button>
</div>


            <div class="profile-bar">
                <img src="img/profile.png" alt="Profile Picture" class="profile-picture">
                <div class="profile-info">
                    <h5 class="profile-name">Name</h5>
                    <p class="profile-role">Admin</p>
                </div>
            </div>
        </nav>

        <div class="container table-container">
            <h2 class="text-center">Product List</h2>
            <table class="table table-striped">
            <thead>
    <tr>
        <th>Item ID</th> <!-- New header for Item ID -->
        <th>Item Code</th>
        <th>Description</th>
        <th>Price per Unit</th>
        <th>Vendor</th>
        <th>Stock Quantity</th>
        <th>Location</th>
        <th>Reorder Status</th>
        <th>Total Value</th>
        <th>Last Order Date</th>
        <th>Actions</th>
    </tr>
</thead>

    <tbody>
        <?php while ($row = $itemsResult->fetch_assoc()): ?>
        <tr>
            <td><?php echo htmlspecialchars($row['item_id']); ?></td> <!-- Item ID -->
            <td><?php echo htmlspecialchars($row['item_code']); ?></td>
            <td><?php echo htmlspecialchars($row['description']); ?></td>
            <td><?php echo htmlspecialchars($row['price_per_unit']); ?></td>
            <td><?php echo htmlspecialchars($row['vendor_name']); ?></td>
            <td><?php echo htmlspecialchars($row['stock_quantity']); ?></td>
            <td><?php echo htmlspecialchars($row['location']); ?></td>
            <td><?php echo htmlspecialchars($row['reorder_status']); ?></td>
            <td><?php echo htmlspecialchars($row['total_value']); ?></td>
            <td><?php echo htmlspecialchars($row['last_order_date']); ?></td>
            <td>
                <form action="delete_item.php" method="POST" style="display:inline;">
                    <input type="hidden" name="item_id" value="<?php echo $row['item_id']; ?>">
                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this item?');">
                        Delete
                    </button>
                </form>
            </td>
        </tr>
        <?php endwhile; ?>
    </tbody>
</table>

            <!-- Pagination -->
            <nav aria-label="Page navigation">
                <ul class="pagination">
                    <?php if ($page > 1): ?>
                    <li class="page-item">
                        <a class="page-link" href="?page=<?php echo $page - 1; ?>">Previous</a>
                    </li>
                    <?php endif; ?>

                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <li class="page-item <?php if ($page == $i) echo 'active'; ?>">
                        <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                    </li>
                    <?php endfor; ?>

                    <?php if ($page < $total_pages): ?>
                    <li class="page-item">
                        <a class="page-link" href="?page=<?php echo $page + 1; ?>">Next</a>
                    </li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>

        <!-- Add Item Modal -->
        <div id="addItemModal" class="modal">
            <div class="modal-content">
                <span class="close" onclick="closeModal()">&times;</span>
                <h2>Add New Item</h2>
                <form action="add_item.php" method="POST">
                    <div class="form-group">
                        <label for="item_code">Item Code:</label>
                        <input type="text" name="item_code" id="item_code" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="description">Description:</label>
                        <textarea name="description" id="description" class="form-control" required></textarea>
                    </div>
                    <div class="form-group">
                        <label for="price_per_unit">Price per Unit:</label>
                        <input type="number" step="0.01" name="price_per_unit" id="price_per_unit" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="vendor_id">Vendor:</label>
                        <select name="vendor_id" id="vendor_id" class="form-control" required>
                            <option value="">Select Vendor</option>
                            <?php
                            // Fetch vendors from the database to populate the dropdown
                            include 'db.php'; // Include the database connection again
                            $vendorsResult = $conn->query("SELECT vendor_id, vendor_name FROM vendors");
                            while ($vendor = $vendorsResult->fetch_assoc()):
                            ?>
                            <option value="<?php echo $vendor['vendor_id']; ?>"><?php echo htmlspecialchars($vendor['vendor_name']); ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="stock_quantity">Stock Quantity:</label>
                        <input type="number" name="stock_quantity" id="stock_quantity" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="location">Location:</label>
                        <input type="text" name="location" id="location" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="reorder_status">Reorder Status:</label>
                        <input type="text" name="reorder_status" id="reorder_status" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="total_value">Total Value:</label>
                        <input type="number" step="0.01" name="total_value" id="total_value" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="last_order_date">Last Order Date:</label>
                        <input type="date" name="last_order_date" id="last_order_date" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Add Item</button>
                </form>
            </div>
        </div>

    </div>
</div>

// Add Item modal script
<script>
    function openModal() {
        document.getElementById("addItemModal").style.display = "block";
    }

    function closeModal() {
        document.getElementById("addItemModal").style.display = "none";
    }

    // Close modal when clicking outside of it
    window.onclick = function(event) {
        const modal = document.getElementById("addItemModal");
        if (event.target == modal) {
            closeModal();
        }
    }
</script>

// Search Filter script
<script>
    document.getElementById('searchInput').addEventListener('input', function() {
        // Get the input value and convert to lower case
        let filter = this.value.toLowerCase();
        // Get all table rows in tbody
        let rows = document.querySelectorAll('.table tbody tr');
        
        // Loop through rows and filter
        rows.forEach(row => {
            let text = row.textContent.toLowerCase();
            row.style.display = text.includes(filter) ? '' : 'none';
        });
    });
</script>

</body>
</html>

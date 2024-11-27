<?php
session_start();
require_once '../config/connection.php';

// Check if the user is not logged in
if (!isset($_SESSION['id'])) {
    header('location: login.php');
    exit(); // Stop further execution
    }

$id = $_SESSION['id'];
$query = "SELECT * FROM users WHERE id='$id'";
$nav = mysqli_query($connection, $query);

// Fetch all users
$query_users = "SELECT * FROM users ORDER BY id DESC";
$result_users = mysqli_query($connection, $query_users);

// Fetch all users and count the number of orders with "Not Paid" status for each user
$sql_display_all = "SELECT *, IFNULL(unpaid_orders.num_unpaid_orders, 0) AS num_unpaid_orders FROM users LEFT JOIN (SELECT user_id, COUNT(*) AS num_unpaid_orders FROM orders WHERE order_status = 'Not Paid' GROUP BY user_id) AS unpaid_orders ON id = user_id ORDER BY id DESC";
$query = mysqli_query($connection, $sql_display_all);

$sql_display_all = "SELECT * FROM products";
$query_display_all = mysqli_query($connection, $sql_display_all);

// Define an array to hold product data
$products = array();

// Fetch the product data and populate the $products array
$sql_display_all = "SELECT * FROM products";
$query_display_all = mysqli_query($connection, $sql_display_all);
while ($row = mysqli_fetch_assoc($query_display_all)) {
    $products[] = $row;
}

// Determine products that need restocking
$numProductsToRestock = 0;
foreach ($products as $product) {
    if ($product['product_quantity'] <= 20) {
        $numProductsToRestock++;
    }
}

// Fetch the number of products that are out of stock
$query_restock = "SELECT COUNT(*) AS num_products FROM products WHERE product_quantity <= 20";
$result_restock = mysqli_query($connection, $query_restock);
$row_restock = mysqli_fetch_assoc($result_restock);
$numProductsToRestock = $row_restock['num_products'];

// Fetch the number of orders with "No One Appointed" status
$query_no_appointment = "SELECT COUNT(*) AS num_orders FROM orders WHERE appointed_to = 'No One Appointed'";
$result_no_appointment = mysqli_query($connection, $query_no_appointment);
$row_no_appointment = mysqli_fetch_assoc($result_no_appointment);
$numOrdersNoAppointment = $row_no_appointment['num_orders'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Page</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Amatic+SC:wght@700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/2.0.0/css/dataTables.bootstrap5.css">
    <link rel="stylesheet" href="assets/css/adduser.css">

</head>
<body>
<!-- Modal for Adding New Product Category -->
<div class="modal fade" id="addCategoryModal" tabindex="-1" aria-labelledby="addCategoryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addCategoryModalLabel">Add New Product Category</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">                
                <!-- Form for adding a new product category -->
                <form id="addCategoryForm" action="product_category.php" method="POST">
                    <div class="mb-3">
                        <label for="newCategoryName" class="form-label">Category Name</label>
                        <input type="text" name="product_category" class="form-control" id="newCategoryName" required>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" name="addBtn" id="addCategoryBtn" class="btn btn-primary">Add Category</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal for Deleting Product Category -->
<div class="modal fade" id="deleteCategoryModal" tabindex="-1" aria-labelledby="deleteCategoryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteCategoryModalLabel">Delete Product Category</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="deleteCategoryForm" action="product_delete_category.php" method="POST">
                    <div class="mb-3">
                        <label for="categoryToDelete" class="form-label">Select Category</label>
                        <select class="form-select" name="category_id" id="categoryToDelete" required>
                            <option value="" selected disabled>Select Category</option>
                            <?php
                            // Fetch and display categories from the database
                            $sql_select_categories = "SELECT id_category, product_category FROM products_category";
                            $result_categories = mysqli_query($connection, $sql_select_categories);
                            while ($row = mysqli_fetch_assoc($result_categories)) {
                                echo '<option value="' . $row['id_category'] . '">' . $row['product_category'] . '</option>';
                            }
                            ?>
                        </select>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" name="deleteBtn" class="btn btn-danger">Delete Category</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

    <main>
        <nav class="navbar navbar-expand-lg navbar-light bg-gradient bg-light py-0 shadow" id="TopNav">
            <div id="navBarBrand-container">
                <a class="navbar-brand">ADMIN PAGE</a>
            </div>
            <div id="navBarMenu-container">
                <div class="ct-top-nav-left">

                    <button id="SideBarToggle">
                        <span class="material-symbols-outlined">
                            menu
                            </span>
                    </button>
                    <a class="navbar-brand" href="index.php">SkyFly Enteprize</a>
                    <div class="collapse navbar-collapse" id="navbarNav">
                    </div>
                </div>
                <div class="ct-top-nav-right">
                    <?php while ($rows = mysqli_fetch_assoc($nav)){ ?>
                    <div class="profile">
                    <div class="info">
                        <p>Hey, <b><?php echo $rows['firstname'] ." ". $rows['lastname']; ?></b></p>
                        <small class="text-muted">Admin</small>
                    </div>
                    <div class="profile-photo">
                        <?php
                        // Fetch the image path from the database
                        $imagePath = $rows['pfp'];

                        // Construct the complete URL to the image
                        $imageURL = 'http://localhost/Ecomerce2/images/' . $imagePath;

                        // Display the image
                        echo '<img src="' . $imageURL . '" alt="Product Image">';
                        ?>
                    </div>
                <?php } ?>
                </div>
                </div>
            </div>
        </nav>
        <!-- Sidebar Section -->
        <div id="sidebarNav">
            <ul class="nav-menu">
                <!-- Dashboard widget -->
                <li class="nav-item">
                    <div class="nav-icon">
                        <span class="material-symbols-outlined">
                            dashboard
                            </span>
                    </div>
                    <a href="index.php" class="nav-link">Dashboard</a>
                </li>
                <!-- Users widget -->
                <li class="nav-item">
                    <div class="nav-icon">
                        <span class="material-symbols-outlined">
                            group
                        </span>
                    </div>
                    <a href="usermanage.php" class="nav-link notification-link">Users
                    <?php if (mysqli_num_rows($query) > 0): ?>
                        <?php while($rows = mysqli_fetch_array($query)): ?>
                            <!-- Display the warning sign if the user has 8 or more unpaid orders -->
                            <?php if ($rows['num_unpaid_orders'] >= 8): ?>
                                <span style="color: red; margin-left: auto;">&#9888;</span>
                            <?php endif; ?>
                        <?php endwhile; ?>
                    <?php endif; ?>
                    </a>
                </li>
                <!-- Order widget -->
                <li class="nav-item">
                    <div class="nav-icon">
                        <div class="notification-icon">
                            <span class="material-symbols-outlined">
                                receipt_long
                            </span>
                        </div>
                    </div>
                    <a href="order.php" class="nav-link notification-link">
                        <span class="material-icons-outlined">History</span>
                        <?php if ($numOrdersNoAppointment > 0) : ?>
                            <!-- Display the badge only if there are orders with "No One Appointed" status -->
                            <span class="badge bg-danger"><?php echo $numOrdersNoAppointment; ?></span>
                        <?php endif; ?>
                    </a>
                </li>
                <!-- Product widget -->
                <li class="nav-item">
                    <div class="nav-icon">
                        <div class="notification-icon">
                            <span class="material-symbols-outlined">
                                inventory
                            </span>
                        </div>
                    </div>
                    <a href="product.php" class="nav-link notification-link">
                        <span class="material-icons-outlined">Products</span>
                        <?php if ($numProductsToRestock > 0) : ?>
                            <!-- Display the badge only if there are products to restock -->
                            <span class="badge bg-danger"><?php echo $numProductsToRestock; ?></span>
                        <?php endif; ?>
                    </a>
                </li>
                <!-- Settings widget -->
                <li class="nav-item">
                    <div class="nav-icon">
                        <span class="material-symbols-outlined">
                            settings
                        </span>
                    </div>
                    <a href="settings.php" class="nav-link">Settings</a>
                </li>
                <!-- Account widget -->
                <li class="nav-item">
                    <div class="nav-icon">
                        <span class="material-symbols-outlined">
                            manage_accounts
                            </span>
                    </div>
                    <a href="accountmanage.php" class="nav-link">Account</a>
                </li>
                <!-- Reports widget -->
                <li class="nav-item">
                    <div class="nav-icon">
                        <span class="material-symbols-outlined">
                            summarize
                            </span>
                    </div>
                    <a href="reports.php" class="nav-link">Reports</a>
                </li>
                <!-- Logout widget -->
                <li class="nav-item">
                    <div class="nav-icon">
                        <span class="material-symbols-outlined">
                            logout
                            </span>
                    </div>
                    <a href="logout.php" class="nav-link">Logout</a>
                </li>
            </ul>
        </div>
        <!-- End of Sidebar Section -->

 <!-- Main Content - Sample Table -->
        <div id="main-wrapper" class="py-5 px-3">
            <div class="container-fluid-md">
                <h1 class="text-center"><b>Product List</b></h1>
                <hr class="border-3 border-dark mx-auto opacity-100" style="width:80px">

                <a class="btn btn-success" href="productadd.php" role="button"><span class="material-symbols-outlined">
                    person_add
                    </span>Add New Product</a>

                <!-- Add New Category Button -->
                <a class="btn btn-warning" role="button" data-bs-toggle="modal" data-bs-target="#addCategoryModal">
                    <span class="material-symbols-outlined">edit</span>Add New Product Category
                </a>

                <!-- Delete Category Button -->
                <a class="btn btn-danger" role="button" data-bs-toggle="modal" data-bs-target="#deleteCategoryModal">
                    <span class="material-symbols-outlined">edit</span>Delete Product Category
                </a>

                 <table id="example" class="table table-striped" style="width:100%">

                    <thead>
                        <tr>
                            <th>Date Created</th>
                            <th>Product ID</th>
                            <th></th>
                            <th>Product Name</th>
                            <th>Product Description</th>
                            <th>Product Quantity</th>
                            <th>Price</th>
                            <th>Category</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                         <!-- Within the while loop where you display product information -->
                        <?php foreach ($products as $product): ?>
                        <tr>
                            <td><?php echo $product['date_created']; ?></td>
                            <td><?php echo $product['product_id']; ?></td>
                            <td>
                                <?php
                                // Fetch the image path from the database
                                $imagePath = $product['product_image'];

                                // Construct the complete URL to the image
                                $imageURL = 'http://localhost/Ecomerce2/images/' . $imagePath;

                                // Display the image
                                echo '<img src="' . $imageURL . '" alt="Product Image" style="max-width: 80px; max-height: 160px;">';
                                ?>
                            </td>
                            <td><?php echo $product['product_name']; ?></td>
                            <td><?php echo $product['product_description']; ?></td>
                            <td><center>
                                <?php echo $product['product_quantity']; ?> <!-- Product quantity -->
                                <?php if ($product['product_quantity'] <= 20): ?>
                                    <span style="color: red;">&#9888;</span> <!-- Warning icon -->
                                    <colspan="7" style="color: red;"><center><b>Warning: This product is low/out of stock!</b></center>
                                <?php endif; ?>
                            </center></td>
                            <td><?php echo $product['product_price']; ?></td>
                            <td><?php echo $product['product_category']; ?></td>
                            <td>
                                <a class="btn btn-outline-warning" href="productedit.php?product_id=<?php echo $product['product_id']; ?>" role="button">
                                    <span class="material-symbols-outlined">edit</span>Edit
                                </a>
                                <a class="btn btn-outline-danger" href="productdelete.php?product_id=<?php echo $product['product_id']; ?>" role="button">
                                    <span class="material-symbols-outlined">view_timeline</span>Delete
                                </a>
                            </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <footer class="shadow-top py-4 col-auto bg-light text-dark" id="footer">
        </footer>

<!--  -->
</main>
<!-- Add this script before the closing </body> tag -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    // Function to show the add category modal
    function showAddCategoryModal() {
        $('#addCategoryModal').modal('show');
    }

    // Event listener for the add category button
    $('#addCategoryBtn').click(function(e) {
        showAddCategoryModal();
    });

    // Event listener for the form submission
    $('#addCategoryForm').submit(function(e) {
    e.preventDefault();
    // AJAX request to submit the form data
    $.ajax({
        url: $(this).attr('action'),
        method: $(this).attr('method'),
        data: $(this).serialize(),
        success: function(response) {
            // Handle success response
            console.log('Category added successfully!');
            // Close the modal
            $('#addCategoryModal').modal('hide');
            // Reload the page to update the category list
            location.reload();
        },
        error: function(xhr, status, error) {
            // Handle error response
            console.error('Error adding category:', error);
        }
    });
});
</script>

    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/2.0.0/js/dataTables.js"></script>
    <script src="https://cdn.datatables.net/2.0.0/js/dataTables.bootstrap5.js"></script>
    <script src="./assets/js/script.js"></script>
    <script src="./assets/js/custom-admin-template.js"></script>
</body>
</html>
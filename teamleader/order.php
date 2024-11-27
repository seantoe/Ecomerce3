<?php
session_start();
$page_title = 'User Management';
require_once '..\config\connection.php';

// Check if the user is not logged in
if (!isset($_SESSION['id'])) {
    header('location: login.php');
    exit(); // Stop further execution
    }
    
$id = $_SESSION['id'];
// Fetch user information for navigation
$query_nav = "SELECT * FROM users WHERE id='$id'";
$result_nav = mysqli_query($connection, $query_nav);
$row_nav = mysqli_fetch_assoc($result_nav);

$query = "SELECT * FROM settings";
$settings = mysqli_query($connection, $query);

    $name = $row_nav['firstname'] . " " . $row_nav['lastname'];
    // Query to fetch orders for the currently logged-in user
    $result = mysqli_query($connection, "SELECT * FROM orders WHERE appointed_to = '$name'");

    // Count the number of appointments received
    $numAppointments = mysqli_num_rows($result);

// Fetch all users and count the number of orders with "Not Paid" status for each user
$sql_display_all = "SELECT *, IFNULL(unpaid_orders.num_unpaid_orders, 0) AS num_unpaid_orders FROM users LEFT JOIN (SELECT user_id, COUNT(*) AS num_unpaid_orders FROM orders WHERE order_status = 'Not Paid' GROUP BY user_id) AS unpaid_orders ON id = user_id ORDER BY id DESC";
$query = mysqli_query($connection, $sql_display_all);
    
// Fetch the number of products that are out of stock
$query_restock = "SELECT COUNT(*) AS num_products FROM products WHERE product_quantity <= 20";
$result_restock = mysqli_query($connection, $query_restock);
$row_restock = mysqli_fetch_assoc($result_restock);
$numProductsToRestock = $row_restock['num_products'];
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
     <!-- Modal for the Image -->
<div id="imageModal" class="modal fade" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body">
                <img id="modalImage" src="" alt="Proof of Payment" style="width: 100%; height: 100%;">
            </div>
        </div>
    </div>
</div>

<!-- Modal for Editing Order Status -->
<div class="modal fade" id="editStatusModal" tabindex="-1" aria-labelledby="editStatusModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editStatusModalLabel">Edit Order Status</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">                
                <!-- PHP content for the modal -->
                <form id="" action="order_editstatus.php" method="POST">
                        <input type="hidden" name="order_id" id="orderID">
                        <div class="mb-3">
                            <label for="newOrderStatus" class="form-label">New Order Status</label>
                            <select name="order_status" class="form-control" id="newOrderStatus">
                                <option></option>
                                <option value="Paid">Paid</option>
                                <option value="Not Paid">Not Paid</option>
                            </select>
                        </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="submit" name="updateForm" class="btn btn-primary">Update</button>
            </div>
            </form>
        </div>
    </div>
</div>
    <main>
        <nav class="navbar navbar-expand-lg navbar-light bg-gradient bg-light py-0 shadow" id="TopNav">
            <div id="navBarBrand-container">
                <a class="navbar-brand">TEAM LEADER</a>
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
                    <div class="profile">
                    <div class="info">
                        <p>Hey, <b><?php echo $row_nav['firstname'] ." ". $row_nav['lastname']; ?></b></p>
                        <small class="text-muted">Admin</small>
                    </div>
                    <div class="profile-photo">
                        <?php
                        // Fetch the image path from the database
                        $imagePath = $row_nav['pfp'];

                        // Construct the complete URL to the image
                        $imageURL = 'http://localhost/Ecomerce2/images/' . $imagePath;

                        // Display the image
                        echo '<img src="' . $imageURL . '" alt="Product Image">';
                        ?>
                    </div>
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
                        <span class="material-symbols-outlined">
                            receipt_long
                            </span>
                    </div>
                    <a href="order.php" class="nav-link notification-link">
                        <span class="material-icons-outlined">History</span>
                        <!-- Display the badge only if there are products to restock -->
                        <span class="badge bg-danger"><?php echo $numAppointments; ?></span>
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
                <h1 class="text-center"><b>order List</b></h1>
                <hr class="border-3 border-dark mx-auto opacity-100" style="width:80px">

                 <table id="example" class="table table-striped" style="width:100%">

        <thead>
            <tr>
                <th>Order Date</th>
                <th>Order Id</th>
                <th>Client's Name</th>
                <th>Address</th>
                <th>Order Cost</th>
                <th>Order Status</th>
                <th>Appointed to</th>
                <th>Proof of Payment</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php while($row =mysqli_fetch_assoc($result)) {

            $query = "SELECT * FROM users WHERE access_level IN (4) AND id_team = $id";
            $user = mysqli_query($connection, $query);
            ?>
            <tr>
                <td><?php echo $row['order_date']; ?></td>
                <td><?php echo $row['order_id']; ?></td>
                <td><?php echo $row['firstname'] . " " . $row['lastname'];; ?></td>
                <td><?php echo $row['address']; ?></td>
                <td><?php echo $row['order_cost']; ?></td>
                <td><?php echo $row['order_status']; ?></td>
                <td><?php echo $row['appointed_to']; ?></td>
                <td>
                <?php
                // Fetch the image path from the database
                $imagePath = $row['proof_of_payment'];

                // Construct the complete URL to the image
                $imageURL = 'http://localhost/Ecomerce2/images/' . $imagePath;

                // Display the image
                echo '<img src="' .$imageURL. '" alt="Proof of Payment" style="max-width: 250px; max-height: 500px;" class="proof-of-payment-image" data-image-url="' .$imageURL. '">';
                ?>
                </td>
                <td>
                    <form action="order_details.php" method="POST">
                        <input type="hidden" name="order_status" value="<?php echo $row['order_status']; ?>">
                        <input type="hidden" name="order_id" value="<?php echo $row['order_id']; ?>">
                        <button class="btn btn-outline-primary" type="submit" name="order_details_btn" role="button"><span class="material-symbols-outlined">view_timeline</span>View</button>
                    </form>

                    <button type="button" class="btn btn-outline-warning editStatusButton" data-bs-toggle="modal" data-bs-target="#editStatusModal" data-order-id="<?php echo $row['order_id']; ?>"
                    data-order-status="<?php echo $row['order_status']; ?>" onclick="setOrderID(<?php echo $row['order_id']; ?>)">
                        <span class="material-symbols-outlined">edit</span>Edit Status
                    </button>

                    <form action="order_appoint.php" method="POST">
                        <input type="hidden" name="id_appoint" value="<?php echo $row['id_appoint']; ?>">
                        <input type="hidden" name="order_id" value="<?php echo $row['order_id']; ?>">
                        <select name="appointed_to">
                            <option value=""></option>
                            <?php while ($user_row = mysqli_fetch_array($user)) { ?>
                                <option value="<?php echo $user_row['id']; ?>"><?php echo $user_row['firstname'] . " " . $user_row['lastname']; ?></option>
                            <?php } ?>
                        </select>
                        <input type="submit" name="appoint" value="Appoint">
                    </form><br>
                </td>
            </tr>
            <?php } ?>
    </table>
            </div>
        </div>
        <footer class="shadow-top py-4 col-auto bg-light text-dark" id="footer">
        </footer>

<!--  -->
</main>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script>
    $(document).ready(function() {
        $('.proof-of-payment-image').click(function() {
            var imageURL = $(this).data('image-url');
            $('#modalImage').attr('src', imageURL);
            $('#imageModal').modal('show');
        });
    });
</script>

<script>
    function setOrderID(orderID) {
        document.getElementById('orderID').value = orderID;
    }
</script>
    <script>
        $('.editStatusButton').click(function(){
            var order_id = $(this).data('order_id');
            var order_status = $(this).data('order_status');

            $('#orderID').val(order_id);
            $('#newOrderStatus').val(order_status);
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
<?php
session_start();
require '../config/connection.php';

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

$query2 = "SELECT * FROM settings";
$settings = mysqli_query($connection, $query2);

$id2 = $_GET['id'];
$query = "SELECT * FROM users WHERE id = '$id2'";
$user = mysqli_query($connection, $query);

$query = "SELECT * FROM orders WHERE user_id = '$id2'";
$order = mysqli_query($connection, $query);

// Modified SQL query to calculate total quantity with unique product names
$query = "SELECT product_name, SUM(product_quantity) AS total_quantity FROM order_items WHERE user_id = $id2 GROUP BY product_name";
$quantity = mysqli_query($connection, $query);

// Function to calculate total quantity with unique product names
function calculateTotalQuantity($connection, $id2) {
    $totalQuantity = 0;

    $query = "SELECT SUM(product_quantity) AS total_quantity FROM order_items WHERE user_id = $id2";
    $result = mysqli_query($connection, $query);

    // Loop through each row and sum up the total quantity
    while ($row = mysqli_fetch_assoc($result)) {
        $totalQuantity += $row['total_quantity'];
    }

    return $totalQuantity;
}

$totalOrderedQuantity = calculateTotalQuantity($connection, $id2);


$query = "SELECT 
            (SELECT COUNT(order_id) FROM orders WHERE user_id = '$id2') AS total_orders,
            (SELECT COUNT(order_id) FROM orders WHERE order_status = 'Paid' AND user_id = '$id2') AS total_paid";
$result = mysqli_query($connection, $query);
// Check if the query was successful
if ($result) {
    // Fetch the result rows
    $row = mysqli_fetch_assoc($result);
    $totalOrders = 0;
    $totalPaid = 0;

    // Total orders
    $totalOrders = $row['total_orders'];
    // Total paid
    $totalPaid = $row['total_paid'];
}

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
    <main>
        <nav class="navbar navbar-expand-lg navbar-light bg-gradient bg-light py-0 shadow" id="TopNav">
            <div id="navBarBrand-container">
                <a class="navbar-brand">SALES AGENT</a>
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
                    <a href="client_list.php" class="nav-link notification-link">Users
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
                    <a href="reports_view.php" class="nav-link">Reports</a>
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
                   
  <div class="container py-5 h-100">
    <?php while($row = mysqli_fetch_array($user)) {  ?>
    <div class="row d-flex justify-content-center align-items-center h-100">
      <div class="col col-md-9 col-lg-7 col-xl-5">
        <div class="card" style="border-radius: 15px;">
          <div class="card-body p-4">
            <div class="d-flex text-black">
              <div class="flex-shrink-0">
                <?php
                    // Fetch the image path from the database
                    $imagePath = $row['pfp'];

                    // Construct the complete URL to the image
                    $imageURL = 'http://localhost/Ecomerce2/images/' . $imagePath;

                    // Display the image
                    echo '<img src="' . $imageURL . '" alt="Profile Picture" alt="Generic placeholder image" class="img-fluid"
                  style="width: 180px; border-radius: 10px;">';
                ?>
              </div>
              <div class="flex-grow-1 ms-3">
                <h5 class="mb-1">First Name: </h5><p><?php echo $row['firstname']; ?></p>
                <h5 class="mb-1">Last Name: </h5><p><?php echo $row['lastname']; ?></p>
                <h5 class="mb-1">Email: </h5><p><?php echo $row['email']; ?></p>
                <h5 class="mb-1">Position: </h5><p><?php echo $row['position']; ?></p>
                <p class="mb-2 pb-1" style="color: #2b2a2a;">ID: <p><?php echo $row['id']; ?></p></p>
                <div class="d-flex justify-content-start rounded-3 p-2 mb-2"
                  style="background-color: #efefef;">
                  <div>
                    <p class="small text-muted mb-1">Address: </p>
                    <p class="mb-0"><?php echo $row['address']; ?></p>
                  </div>
                  <div class="px-3">
                    <p class="small text-muted mb-1">Contact:</p>
                    <p class="mb-0"><?php echo $row['contact']; ?></p>
                  </div>                
                </div>                
              </div>
            </div>
          </div>
        </div>
  </div>
                
                  </div>
        <?php } ?>
            </div>
       
       <div id="main-wrapper" class="py-5 px-3">
            <div class="container-fluid-md">
                <hr class="border-3 border-dark mx-auto opacity-100" style="width:80px">

                 <table id="example" class="table table-striped" style="width:100%">

        <thead>
            <tr>
                <th><center>Product</center></th>
                <th><center>Quantity</center></th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = mysqli_fetch_assoc($quantity)) { ?>
                <tr>
                    <td><center><?php echo $row['product_name']; ?></center></td>
                    <td><center><?php echo $row['total_quantity']; ?></center></td>
                </tr>
            <?php } ?>
            </tbody>
                <td></td>
                <td><strong><center>Total Ordered Quantity: </strong><?php echo $totalOrderedQuantity; ?></center></td>
        </table>

                <hr class="border-3 border-dark mx-auto opacity-100" style="width:80px">

                 <table id="example" class="table table-striped" style="width:100%">
                    <thead>
                        <tr>
                            <th><center>Orders: <?php echo $totalOrders; ?></center></th>
                        </tr>
                    </thead>
                </table>

                <hr class="border-3 border-dark mx-auto opacity-100" style="width:80px">

                 <table id="example" class="table table-striped" style="width:100%">
                    <thead>
                        <tr>
                            <th><center>Payments: <?php echo $totalPaid; ?></center></th>
                        </tr>
                    </thead>
                </table>

                <hr class="border-3 border-dark mx-auto opacity-100" style="width:80px">

                 <table id="example" class="table table-striped" style="width:100%">
                    <thead>
                        <tr>
                            <th><center>Order Date</center></th>
                            <th><center>Order Id</center></th>
                            <th><center>Order Cost</center></th>
                            <th><center>Order Status</center></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($rows = mysqli_fetch_array($order)){ ?>
                        <tr>
                            <td><center><?php echo $rows['order_date']; ?></center></td>
                            <td><center><?php echo $rows['order_id']; ?></center></td>
                            <td><center><?php echo $rows['order_cost']; ?></center></td>
                            <td><center><?php echo $rows['order_status']; ?></center></td>
                        </tr>
                    <?php } ?>
                    </tbody>
                </table>
            </div>
    </div>

            </div>
        </div>
        <footer class="shadow-top py-4 col-auto bg-light text-dark" id="footer">
        </footer>
</main>
<!-- End of Main Content -->


<script src="./assets/js/custom-admin-template.js"></script>
</body>
</html>

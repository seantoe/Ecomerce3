<?php
session_start();
$page_title = 'User Management';
require_once '../config/connection.php';

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

$sql_display_all = "SELECT * FROM users WHERE access_level = 5 AND id_appoint = $id";
$query = mysqli_query($connection, $sql_display_all);

// Fetch the number of products that are out of stock
$query_restock = "SELECT COUNT(*) AS num_products FROM products WHERE product_quantity <= 20";
$result_restock = mysqli_query($connection, $query_restock);
$row_restock = mysqli_fetch_assoc($result_restock);
$numProductsToRestock = $row_restock['num_products'];

$sql = "SELECT address, COUNT(*) AS order_count FROM orders WHERE order_status = 'Paid' AND id_appoint = $id GROUP BY address ORDER BY order_count DESC";
$result_addresses = mysqli_query($connection, $sql);

$addressData = array();
while ($row = mysqli_fetch_assoc($result_addresses)) {
    $addressData[] = array($row['address'], (int) $row['order_count']);
}

$address_data_json = json_encode($addressData);

// Ensure user information is fetched properly
    $name = $row_nav['firstname'] . " " . $row_nav['lastname'];
    // Query to fetch orders for the currently logged-in user
    $result = mysqli_query($connection, "SELECT * FROM orders WHERE appointed_to = '$name'");

    // Count the number of appointments received
    $numAppointments = mysqli_num_rows($result);

// Fetch all users and count the number of orders with "Not Paid" status for each user
$sql_display_all = "SELECT *, IFNULL(unpaid_orders.num_unpaid_orders, 0) AS num_unpaid_orders FROM users LEFT JOIN (SELECT user_id, COUNT(*) AS num_unpaid_orders FROM orders WHERE order_status = 'Not Paid' GROUP BY user_id) AS unpaid_orders ON id = user_id ORDER BY id DESC";
$query2 = mysqli_query($connection, $sql_display_all);
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
                    <?php if (mysqli_num_rows($query2) > 0): ?>
                        <?php while($rows = mysqli_fetch_array($query2)): ?>
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
                <h1 class="text-center"><b>User List</b></h1>
                <hr class="border-3 border-dark mx-auto opacity-100" style="width:80px">

    <table id="example" class="table table-striped" style="width:100%">
        <thead>
            <tr>
                <th>#</th>
                <th>Full Name</th>
                <th>Email</th>
                <th>Contact Number</th>
                <th>Address</th>
                <th>Profile</th>
                <th>Access Level</th>
                <th>Position</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            <?php while($rows = mysqli_fetch_array($query)){ ?>
            <tr>
                <th><?php echo $rows['id']; ?></th>
                <td><?php echo $rows['firstname'] ." ". $rows['lastname'];?></td>
                <td><?php echo $rows['email']; ?></td>
                <td><?php echo $rows['contact']; ?></td>
                <td><?php echo $rows['address']; ?></td>
                <td>
                <?php
                    // Fetch the image path from the database
                    $imagePath = $rows['pfp'];

                    // Construct the complete URL to the image
                    $imageURL = 'http://localhost/Ecomerce2/images/' . $imagePath;

                    // Display the image
                    echo '<img src="' . $imageURL . '" alt="Profile Picture" style="max-width: 100px; max-height: 100px;">';
                ?>
                </td>
                <td><?php echo $rows['access_level']; ?></td>
                <td><?php echo $rows['position']; ?></td>
                <td>
                    <a class="btn btn-outline-primary" href="client_view.php?id=<?php echo $rows['id']; ?>" role="button"><span class="material-symbols-outlined">
                    view_timeline
                    </span>View</a>
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
    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/2.0.0/js/dataTables.js"></script>
    <script src="https://cdn.datatables.net/2.0.0/js/dataTables.bootstrap5.js"></script>
    <script src="./assets/js/script.js"></script>
    <script src="./assets/js/custom-admin-template.js"></script>
</body>
</html>
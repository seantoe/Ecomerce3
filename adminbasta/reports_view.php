<?php
session_start();
require '../config/connection.php';

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
$query5 = mysqli_query($connection, $sql_display_all);

$id2 = $_GET['id'];
$sql_display_all = "SELECT * FROM users WHERE id = $id2";
$query2 = mysqli_query($connection, $sql_display_all);

$id3 = $_GET['id'];
$sql_display_all = "SELECT * FROM users WHERE access_level = 4 AND id_team = $id3";
$query = mysqli_query($connection, $sql_display_all);

// Check if $_GET['id'] is set before using it
$user_id = isset($_GET['id']) ? $_GET['id'] : null;

// Modified SQL query to calculate total quantity with unique product names
$query3 = "SELECT product_name, SUM(product_quantity) AS total_quantity FROM order_items WHERE id_team = $user_id GROUP BY product_name";
$quantity = mysqli_query($connection, $query3);

// Function to calculate total quantity with unique product names
function calculateTotalQuantity($connection, $user_id) {
    $totalQuantity = 0;

    $query = "SELECT SUM(product_quantity) AS total_quantity FROM order_items WHERE id_team = $user_id";
    $result = mysqli_query($connection, $query);

    // Loop through each row and sum up the total quantity
    while ($row = mysqli_fetch_assoc($result)) {
        $totalQuantity += $row['total_quantity'];
    }

    return $totalQuantity;
}

$totalOrderedQuantity = calculateTotalQuantity($connection, $user_id);

// Corrected query to count the number of clients based on address
$query4 = "SELECT address, 
                  COUNT(DISTINCT address) AS total_clients, 
                  SUM(CASE WHEN order_status = 'Paid' THEN 1 ELSE 0 END) AS paid_orders
           FROM orders 
           WHERE id_team = $user_id 
           GROUP BY address 
           ORDER BY paid_orders DESC";
$client = mysqli_query($connection, $query4);

// Function to fetch total clients
function calculateTotalClient($connection, $user_id) {
    $totalClients = 0;

    $query = "SELECT COUNT(DISTINCT address) AS total_clients FROM orders WHERE id_team = $user_id";
    $client = mysqli_query($connection, $query);

    // Fetch total clients
    if ($row = mysqli_fetch_assoc($client)) {
        $totalClients = $row['total_clients'];
    }

    return $totalClients;
}

$totalClients = calculateTotalClient($connection, $user_id);


// SQL query to calculate total amounts
$sql = "SELECT 
            (SELECT SUM(order_cost) FROM orders WHERE id_team = $user_id) AS total_price_sold,
            (SELECT SUM(order_cost) FROM orders WHERE order_status = 'Paid' AND id_team = $user_id) AS total_amount_collected,
            (SELECT SUM(order_cost) FROM orders WHERE order_status != 'Paid' AND id_team = $user_id) AS total_for_collection";

// Execute the query
$result = mysqli_query($connection, $sql);

// Check if the query was successful
if ($result) {
    // Fetch the result rows
    $row = mysqli_fetch_assoc($result);
    $totalPriceSold = 0;
    $totalAmountCollected = 0;
    $totalForCollection = 0;

    // Total price sold
    $totalPriceSold = $row['total_price_sold'] ?? 0 ;

    // Total amount collected from paid orders
    $totalAmountCollected = $row['total_amount_collected'] ?? 0;

    // Total amount for collection
    $totalForCollection = $row['total_for_collection'] ?? 0;
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
                    <?php if (mysqli_num_rows($query5) > 0): ?>
                        <?php while($rows = mysqli_fetch_array($query5)): ?>
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
            <div class="container">
                <?php while ($row = mysqli_fetch_array($query2)) { ?>
                <h1 class="text-center"><b><?php echo $row['firstname'] ." ". $row['lastname']; ?>'s Report</b></h1>
            <?php } ?>
                <hr class="border-3 border-dark mx-auto opacity-100" style="width:80px">

                 <table class="table table-striped" style="width:100%">
                    <thead>
                        <tr>
                        <th><center>Client: <?php echo $totalClients; ?></center></th>
                        <!-- Add additional headers as needed -->
                    </tr>
                </thead>
                    <tbody>
                        <?php
                        // Iterate over the results from the orders table
                        while ($row = mysqli_fetch_assoc($client)) {
                            echo "<tr>";
                            echo "<td><center>{$row['address']}</center></td>";
                            // Add additional columns as needed
                            echo "</tr>";
                        }
                        ?>
                    </tbody>
                </table>

                <table class="table table-striped" style="width:100%">
                    <thead>
                        <tr>
                            <th><center>Product</center></th>
                            <th><center>Quantity</center></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = mysqli_fetch_assoc($quantity)) {
                            echo "<tr>
                                    <td><p><center>{$row['product_name']}</center></p></td>
                                    <td><center><span>{$row['total_quantity']}</span></center></td>
                                </tr>";
                        }
                        ?>
                    </tbody>
                    <td></td>
                    <td><strong>Total Ordered Quantity: </strong><?php echo $totalOrderedQuantity; ?></td>
                </table>

                <table class="table table-striped" style="width:100%">
                    <thead>
                        <tr>
                            <th>Total Price Sold: <td><?php echo $totalPriceSold; ?></td></th>
                        </tr>
                        <tr>
                            <th>Total Amount Collected: <td><?php echo $totalAmountCollected; ?></td></th>
                        </tr>
                        <tr>
                            <th>For Collection: <td><?php echo $totalForCollection; ?></td></th>
                        </tr>
                    </thead>
                </table><br>

                <h1 class="text-center"><b>Sales Agents Report</b></h1>
                <hr class="border-3 border-dark mx-auto opacity-100" style="width:80px">
                <table id="example" class="table table-striped" style="width:100%">

                    <thead>
                        <tr>
                            <th><center></center></th>
                            <th><center>Full Name</center></th>
                            <th><center>Contact Number</center></th>
                            <th><center>Address</center></th>
                            <th><center>Position</center></th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                         <?php while($rows = mysqli_fetch_array($query)){ ?>
                        <tr>
                            <td><center>
                            <?php
                                // Fetch the image path from the database
                                $imagePath = $rows['pfp'];

                                // Construct the complete URL to the image
                                $imageURL = 'http://localhost/Ecomerce2/images/' . $imagePath;

                                // Display the image
                                echo '<img src="' . $imageURL . '" alt="Profile Picture" style="max-width: 100px; max-height: 100px;">';
                            ?>
                            </center></td>
                            <td><center><?php echo $rows['firstname'] ." ". $rows['lastname'];?></center></td>
                            <td><center><?php echo $rows['contact']; ?></center></td>
                            <td><center><?php echo $rows['address']; ?></center></td>
                            <td><center><?php echo $rows['position']; ?></center></td>
                            <td><center>
                            <a class="btn btn-outline-primary" href="reports_viewsales.php?id=<?php echo $rows['id']; ?>" role="button"><span class="material-symbols-outlined">view_timeline</span>View</a>
                            <form method="post" action="reportaddpdf_sales.php">
                                <input type="hidden" name="user_id" value="<?php echo $rows['id']; ?>"> <!-- Example user_id value -->
                                <button type="submit" class="btn btn-outline-danger"><span class="material-symbols-outlined">picture_as_pdf</span>Download PDF</button>
                            </form>
                            </center></td>
                        </tr>
                        <?php } ?>
                    </tbody>
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
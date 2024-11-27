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

$select = "SELECT * FROM users WHERE access_level = 5 LIMIT 4";
$user = mysqli_query($connection,$select);

$select = "SELECT * FROM products LIMIT 2";
$product = mysqli_query($connection,$select);

$sql = "SELECT 
    (SELECT SUM(order_cost) FROM orders WHERE order_status = 'Paid' AND id_appoint = $id) AS total_amount_collected,
    (SELECT COUNT(id) FROM users WHERE access_level = 5 AND id_appoint = $id) AS total_clients,
    (SELECT COUNT(order_id) FROM orders WHERE id_appoint = $id) AS total_orders";

$result3 = mysqli_query($connection, $sql);

// Check if the query was successful
if ($result3) {
    // Fetch the result row
    $row = mysqli_fetch_assoc($result3);
    $totalAmountCollected = 0;
    $totalClients = 0;
    $totalOrders = 0;

    // Total amount collected from paid orders
    $totalAmountCollected = $row['total_amount_collected'];

    // Total number of clients
    $totalClients = $row['total_clients'];

    // Total number of orders
    $totalOrders = $row['total_orders'];
}

    $name = $row_nav['firstname'] . " " . $row_nav['lastname'];
    // Query to fetch orders for the currently logged-in user
    $result = mysqli_query($connection, "SELECT * FROM orders WHERE appointed_to = '$name'");

    // Count the number of appointments received
    $numAppointments = mysqli_num_rows($result);

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

$sql2 = "SELECT product_quantity, product_name FROM order_items WHERE order_status = 'paid' AND id_appoint = $id";
// Execute the SQL query
$result_products = mysqli_query($connection, $sql2);

// Check if the query was successful
if ($result_products) {
    // Fetch the result and process it
    $productData = array();
    while ($row = mysqli_fetch_assoc($result_products)) {
        $productData[] = array($row['product_name'], (int) $row['product_quantity']);
    }

    // Encode the data to JSON format
    $product_data_json = json_encode($productData);
} else {
    // If there was an error in the query execution, handle it
    echo "Error: " . mysqli_error($connection);
}

// Fetch all users and count the number of orders with "Not Paid" status for each user
$sql_display_all = "SELECT *, IFNULL(unpaid_orders.num_unpaid_orders, 0) AS num_unpaid_orders FROM users LEFT JOIN (SELECT user_id, COUNT(*) AS num_unpaid_orders FROM orders WHERE order_status = 'Not Paid' GROUP BY user_id) AS unpaid_orders ON id = user_id ORDER BY id DESC";
$query = mysqli_query($connection, $sql_display_all);

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

                <!-- Card List Row-->

                <div class="row g-3 mb-4">
                    <div class="col-12 col-sm-6 col-lg-3">
                        <div class="text-dark text-decoration-none bg-white p-3 rounded shadow d-flex justify-content-between summary-indigo">
                            <!-- Card content... -->


                            <span class="material-symbols-outlined">
                                groups
                            </span>
                            <div>Total client(s)
                            </div>
                            <h4><?php echo $totalClients ?></h4>
                        </div>
                    </div>
                    <div class="col-12 col-sm-6 col-lg-3">
                        <div class="text-dark text-decoration-none bg-white p-3 rounded shadow d-flex justify-content-between summary-primary">
                            <span class="material-symbols-outlined">
                                local_atm
                            </span>
                            <div>Total sales
                            </div>
                            <h4>₱<?php echo $totalAmountCollected ?></h4>
                        </div>
                    </div>
                    <div class="col-12 col-sm-6 col-lg-3">
                        <div class="text-dark text-decoration-none bg-white p-3 rounded shadow d-flex justify-content-between summary-success">
                            <!-- Card content... -->


                            <span class="material-symbols-outlined">
                                orders
                            </span>
                            <div>Total order(s)

                            </div>
                            <h4><?php echo $totalOrders ?></h4>
                        </div>
                    </div>
                </div>


                <!-- Legit eto lang tatawagin mo -->
                <div class="row my-2">
                    <div class="col-md-6 py-1">
                        <div class="card">
                            <div class="card-body">
                                <div id="piechart"></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 py-1">
                        <div class="card">
                            <div class="card-body">
                                <canvas id="myChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
        <footer class="shadow-top py-4 col-auto bg-light text-dark" id="footer">
        </footer>
    </main>
    <!-- Bar Chart -->

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
    const ctx = document.getElementById('myChart');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: <?php echo $product_data_json ?>.map(item => item[0]), // Extracting product names
            datasets: [{
                label: 'Best Selling Product',
                data: <?php echo $product_data_json ?>.map(item => item[1]), // Extracting order counts
                backgroundColor: [  // Define custom colors for each bar
                    'rgba(255, 99, 132, 0.6)',   // Red
                    'rgba(54, 162, 235, 0.6)',   // Blue
                    'rgba(255, 206, 86, 0.6)',   // Yellow
                    'rgba(75, 192, 192, 0.6)',   // Green
                    'rgba(153, 102, 255, 0.6)',  // Purple
                    'rgba(255, 159, 64, 0.6)'    // Orange
                    // Add more colors as needed for each product
                ],
                borderColor: [  // Define border colors for each bar
                    'rgba(255, 99, 132, 1)',
                    'rgba(54, 162, 235, 1)',
                    'rgba(255, 206, 86, 1)',
                    'rgba(75, 192, 192, 1)',
                    'rgba(153, 102, 255, 1)',
                    'rgba(255, 159, 64, 1)'
                    // Add more border colors if needed
                ],
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
  </script>

<!-- Pie Chart -->
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/chroma-js/2.1.1/chroma.min.js"></script> <!-- Include Chroma.js -->

<!-- Before the closing </body> tag -->
<script type="text/javascript">
    // Load google charts
    google.charts.load('current', {'packages':['corechart']});
    google.charts.setOnLoadCallback(drawChart);

    function drawChart() {
        // Check if address data is available
        <?php if (isset($address_data_json)) : ?>
            var addressData = <?php echo $address_data_json; ?>;

            // Sort the address data array by order count in descending order
            addressData.sort(function(a, b) {
                return b[1] - a[1];
            });

            var data = google.visualization.arrayToDataTable([
                ['Address', 'Order Count'],
                <?php
                foreach ($addressData as $address) {
                    echo "['" . $address[0] . ' (' . $address[1] . ' orders)' . "', " . $address[1] . "],";
                }
                ?>
            ]);

            // Generate aesthetically pleasing random colors
            var colors = chroma.scale(['#3498db', '#2ecc71']).mode('lch').colors(addressData.length); // Adjust the range of colors as needed

            // Optional; add a title and set the width and height of the chart
            var options = {'title':'Location With Best Sellers', 'width':550, 'height':290, colors: colors};

            // Display the chart inside the <div> element with id="piechart"
            var chart = new google.visualization.PieChart(document.getElementById('piechart'));
            chart.draw(data, options);
        <?php else : ?>
            // Handle case where address data is not available
            console.log('Address data not available.');
        <?php endif; ?>
    }
</script>


<script src="./assets/js/custom-admin-template.js"></script>


</html>

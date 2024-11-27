<?php 
session_start();
require_once '../config/connection.php';

$id = $_SESSION['id'];
$query = "SELECT * FROM users WHERE id='$id'";
$nav = mysqli_query($connection, $query);

// Fetch all users
$query_users = "SELECT * FROM users ORDER BY id DESC";
$result_users = mysqli_query($connection, $query_users);

// Fetch all users and count the number of orders with "Not Paid" status for each user
$sql_display_all = "SELECT *, IFNULL(unpaid_orders.num_unpaid_orders, 0) AS num_unpaid_orders FROM users LEFT JOIN (SELECT user_id, COUNT(*) AS num_unpaid_orders FROM orders WHERE order_status = 'Not Paid' GROUP BY user_id) AS unpaid_orders ON id = user_id ORDER BY id DESC";
$query = mysqli_query($connection, $sql_display_all);

if (isset($_POST['submit'])) {
    // Prepare and execute the query to retrieve the team leader's ID based on the address
    $team_leader_query = "SELECT id FROM users WHERE position = 'Team Leader' AND address = ?";
    $stmt_team_leader = mysqli_prepare($connection, $team_leader_query);
    mysqli_stmt_bind_param($stmt_team_leader, "s", $_POST['address']);
    mysqli_stmt_execute($stmt_team_leader);
    mysqli_stmt_bind_result($stmt_team_leader, $team_leader_id);
    mysqli_stmt_fetch($stmt_team_leader);
    mysqli_stmt_close($stmt_team_leader);

    // Insert the user into the database
    $insert_query = "INSERT INTO users (firstname, lastname, email, password, access_level, address, contact, position, id_team) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($connection, $insert_query);

    $firstname = $_POST['firstname'];
    $lastname = $_POST['lastname'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $address = $_POST['address'];
    $access_level = 4; // Set access level to 4 for Sales Agent
    $contact = $_POST['contact'];
    $position = 'Sales Agent'; // Set position to 'Sales Agent'

    // Hash the password
    $hashed_password = md5($password);

    // Bind parameters for the prepared statement
    mysqli_stmt_bind_param($stmt, "ssssisssi", $firstname, $lastname, $email, $hashed_password, $access_level, $address, $contact, $position, $team_leader_id);
    
    // Execute the statement
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    // Redirect to the user management page
    header("Location: usermanage.php");
    exit();
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
            <h1 class="text-center"><b>Sales Agent User</b></h1>
                <hr class="border-3 border-dark mx-auto opacity-100" style="width:70px">
            <div class="container-fluid-md">

                
                  <div class="container py-5">
                    <div class="row d-flex justify-content-center align-items-center">
                        <div class="col-md-6 mb-3">
                            <h4 class="text-center">Select User:</h4> 
                    <a class="btn btn-primary" href="user_addadmin.php" role="button"><span class="material-symbols-outlined">
                    shield_person
                    </span>Admin</a>

                    <a class="btn btn-warning" href="user_addcountry.php" role="button"><span class="material-symbols-outlined">
                    supervisor_account
                    </span>Country Manager</a>

                    <a class="btn btn-success" href="user_addteam.php" role="button"><span class="material-symbols-outlined">
                    groups_2
                    </span>Team Leader</a>

                    <a class="btn btn-dark" href="user_addsales.php" role="button"><span class="material-symbols-outlined">
                    support_agent
                    </span>Sales Agent</a>
                </div>
            </div>
                    <div class="row d-flex justify-content-center align-items-center h-100">
                      <div class="col-12">
                        <div class="card card-registration card-registration-2" style="border-radius: 15px;">
                          <div class="card-body p-0">
                            <div class="row g-0">
                              <div class="col-lg-6">
                                <div class="p-5">
                <form action="user_addsales.php" method="POST" enctype="multipart/form-data">
                  <h3 class="fw-normal mb-5" style="color: #4835d4;">General Infomation</h3>

                  <div class="row">
                    <div class="col-md-6 mb-4 pb-2">

                      <div class="form-outline">
                        <label class="form-label" for="form3Examplev2">First name</label>
                        <input type="text" name="firstname" id="form3Examplev2" class="form-control form-control-lg" value="<?php echo isset($_POST['firstname']) ? $_POST['firstname'] : ''; ?>" />
                      </div>

                    </div>
                    <div class="col-md-6 mb-4 pb-2">

                      <div class="form-outline">
                        <label class="form-label" for="form3Examplev3">Last name</label>
                        <input type="text" name="lastname" id="form3Examplev3" class="form-control form-control-lg" value="<?php echo isset($_POST['lastname']) ? $_POST['lastname'] : ''; ?>" />
                      </div>

                    </div>
                  </div>

                  <div class="mb-4 pb-2">
                    <div class="form-outline">
                        <p style="color:red"><?php if(isset($_GET['error'])){ echo $_GET['error']; } ?></p>
                        <label class="form-label" for="form3Examplev4">Email</label>
                      <input type="text" name="email" id="form3Examplev4" class="form-control form-control-lg" value="<?php echo isset($_POST['email']) ? $_POST['email'] : ''; ?>" />
                    </div>
                  </div>

                  <div class="mb-4 pb-2">
                    <div class="form-outline">
                        <label class="form-label" for="form3Examplev4">Password</label>
                      <input type="password" name="password" id="form3Examplev4" class="form-control form-control-lg" value="<?php echo isset($_POST['password']) ? $_POST['password'] : ''; ?>" />
                    </div>
                  </div>

                </div>
              </div>
              <div class="col-lg-6 bg-indigo text-white">
                <div class="p-5">
                  <h3 class="fw-normal mb-5">Contact Details</h3>

                    <div class="mb-4 pb-2">
                    <div class="form-outline form-white">
                        <label class="form-label" for="form3Examplea2">Address</label>
                      <input type="text" name="address" id="form3Examplea2" class="form-control form-control-lg" value="<?php echo isset($_POST['address']) ? $_POST['address'] : ''; ?>" />
                    </div>
                  </div>   
                 
                    <div class="mb-4 pb-2">
                    <div class="form-outline form-white">
                        <label class="form-label" for="form3Examplea2">Contact</label>
                      <input type="text" name="contact" id="form3Examplea2" class="form-control form-control-lg" value="<?php echo isset($_POST['contact']) ? $_POST['contact'] : ''; ?>" />
                    </div>
                  </div>
                  

                  <button type="submit" name="submit" href="usermanage.php" class="btn btn-light btn-lg"
                    data-mdb-ripple-color="dark">Add User</button>
                    <a type="button" href="usermanage.php" class="btn btn-light btn-lg"
                    data-mdb-ripple-color="dark">Go Back</a>

                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</form>



            </div>
        </div>
        <footer class="shadow-top py-4 col-auto bg-light text-dark" id="footer">
        </footer>

<!--  -->

    </main>
    <script src="./assets/js/custom-admin-template.js"></script>
</body>
</html>
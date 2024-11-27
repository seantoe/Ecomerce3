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
$query = "SELECT * FROM users WHERE id='$id'";
$nav = mysqli_query($connection, $query);

// Fetch all users
$query_users = "SELECT * FROM users ORDER BY id DESC";
$result_users = mysqli_query($connection, $query_users);

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

// Fetch the number of orders with "Not Paid" status for users
$query_unpaid_orders = "SELECT COUNT(*) AS num_orders FROM orders WHERE order_status = 'Not Paid' GROUP BY user_id";
$result_unpaid_orders = mysqli_query($connection, $query_unpaid_orders);
$numOrdersNoPayment = mysqli_num_rows($result_unpaid_orders);

if (isset($_POST['unbanUserBtn'])) {
    $banned_user_id = $_POST['banned_user_id'];
    
    // Update the ban status to 0 for the selected user
    $query_unban_user = "UPDATE users SET ban = 0 WHERE id = '$banned_user_id'";
    $result_unban_user = mysqli_query($connection, $query_unban_user);

    if ($result_unban_user) {
        // Redirect to the same page to refresh the user list
        header('Location: usermanage.php');
        exit();
    } else {
        // Handle the case where the update query fails
        echo "Error: Unable to unban user.";
    }
}
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
    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
</head>
<body>
<!-- Ban User Modal -->
<div class="modal fade" id="banUserModal" tabindex="-1" aria-labelledby="banUserModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="banUserModalLabel">Banned Users</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Full Name</th>
                            <th>Email</th>
                            <th>Contact Number</th>
                            <th>Address</th>
                            <th>Profile</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Fetch banned users from the database
                        $query_banned_users = "SELECT * FROM users WHERE ban = 1";
                        $result_banned_users = mysqli_query($connection, $query_banned_users);

                        while ($row_banned_users = mysqli_fetch_assoc($result_banned_users)) {
                            // Display banned users' information in the modal table
                        ?>
                            <tr>
                                <td><?php echo $row_banned_users['firstname'] . " " . $row_banned_users['lastname']; ?></td>
                                <td><?php echo $row_banned_users['email']; ?></td>
                                <td><?php echo $row_banned_users['contact']; ?></td>
                                <td><?php echo $row_banned_users['address']; ?></td>
                                <td>
                                    <?php
                                    // Display the banned user's profile picture
                                    $imagePath = $row_banned_users['pfp'];
                                    $imageURL = 'http://localhost/Ecomerce2/images/' . $imagePath;
                                    echo '<img src="' . $imageURL . '" alt="Profile Picture" style="width: 80px; height: 80px;">';
                                    ?>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<!-- End Ban User Modal -->

<!-- Unban User Modal -->
<div class="modal fade" id="unbanUserModal" tabindex="-1" aria-labelledby="unbanUserModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="unbanUserModalLabel">Unban User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="unbanUserForm" method="post">
                    <div class="mb-3">
                        <label for="selectBannedUser" class="form-label">Select Banned User</label>
                        <select class="form-select" id="selectBannedUser" name="banned_user_id">
                            <option selected disabled>Select a user to unban</option>
                            <?php
                            // Fetch banned users from the database
                            $query_banned_users = "SELECT id, firstname, lastname FROM users WHERE ban = 1";
                            $result_banned_users = mysqli_query($connection, $query_banned_users);

                            while ($row_banned_users = mysqli_fetch_assoc($result_banned_users)) {
                                // Display banned users in the select dropdown
                                echo '<option value="' . $row_banned_users['id'] . '">' . $row_banned_users['firstname'] . ' ' . $row_banned_users['lastname'] . '</option>';
                            }
                            ?>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="submit" form="unbanUserForm" name="unbanUserBtn" class="btn btn-primary">Unban User</button>
            </div>
        </div>
    </div>
</div>
<!-- End Unban User Modal -->
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
                    <a href="usermanage.php" class="nav-link">Users
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
        <h1 class="text-center"><b>User List</b></h1>
        <hr class="border-3 border-dark mx-auto opacity-100" style="width:80px">

        <a class="btn btn-success" href="user_addadmin.php" role="button"><span class="material-symbols-outlined">person_add</span>Add User</a>

        <a class="btn btn-danger btn-ban" role="button" data-bs-toggle="modal" data-bs-target="#banUserModal"><span class="material-symbols-outlined">person</span>Banned User(s)</a>

        <a class="btn btn-primary btn-ban" role="button" data-bs-toggle="modal" data-bs-target="#unbanUserModal"><span class="material-symbols-outlined">person</span>Unban User(s)</a>

        <table id="example" class="table table-striped" style="width:100%">
            <thead>
                <tr>
                    <th>Full Name</th>
                    <th>Email</th>
                    <th>Contact Number</th>
                    <th>Address</th>
                    <th>Profile</th>
                    <th>Position</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php
                while ($rows = mysqli_fetch_array($result_users)) {
                    // Count the number of unpaid orders for each user
                    $user_id = $rows['id'];
                    $query_unpaid_orders = "SELECT COUNT(*) AS num_unpaid_orders FROM orders WHERE user_id = '$user_id' AND order_status = 'Not Paid'";
                    $result_unpaid_orders = mysqli_query($connection, $query_unpaid_orders);
                    $row_unpaid_orders = mysqli_fetch_assoc($result_unpaid_orders);
                    $num_unpaid_orders = $row_unpaid_orders['num_unpaid_orders'];
                ?>
                    <tr>
                        <td><?php echo $rows['firstname'] . " " . $rows['lastname']; ?></td>
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
                            echo '<img src="' . $imageURL . '" alt="Profile Picture" style="width: 80px; height: 80px;">';
                            ?>
                        </td>
                        <td><?php echo $rows['position']; ?></td>
                        <td>
                            <a class="btn btn-outline-primary" href="userview.php?id=<?php echo $rows['id']; ?>" role="button"><span class="material-symbols-outlined">view_timeline</span>View</a>
                            <a class="btn btn-outline-warning" href="useredit.php?id=<?php echo $rows['id']; ?>" role="button"><span class="material-symbols-outlined">edit</span>Edit</a>
                            <a class="btn btn-outline-danger" href="userdelete.php?id=<?php echo $rows['id']; ?>" role="button"><span class="material-symbols-outlined">view_timeline</span>Delete</a><br>
                            <?php
                            // Display the warning sign if the user has 8 or more unpaid orders
                            if ($num_unpaid_orders >= 8) {
                                echo '<span style="color: red;">&#9888;</span><span style="color: red;"><b>Warning: This user has 8 or more unpaid orders!</b></span>';
                            }
                            ?>
                        </td>
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

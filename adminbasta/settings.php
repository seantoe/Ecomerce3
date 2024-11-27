<?php
session_start();
$page_title = 'User Management';
require_once '../config/connection.php';

$id = $_SESSION['id'];
$query = "SELECT * FROM users WHERE id='$id'";
$nav = mysqli_query($connection, $query);

$sql_display_all = "SELECT * FROM settings";
$settings1 = mysqli_query($connection, $sql_display_all);

$sql_display_all = "SELECT * FROM settings2";
$settings2 = mysqli_query($connection, $sql_display_all);

// Fetch all users
$query_users = "SELECT * FROM users ORDER BY id DESC";
$result_users = mysqli_query($connection, $query_users);

// Fetch all users and count the number of orders with "Not Paid" status for each user
$sql_display_all = "SELECT *, IFNULL(unpaid_orders.num_unpaid_orders, 0) AS num_unpaid_orders FROM users LEFT JOIN (SELECT user_id, COUNT(*) AS num_unpaid_orders FROM orders WHERE order_status = 'Not Paid' GROUP BY user_id) AS unpaid_orders ON id = user_id ORDER BY id DESC";
$query2 = mysqli_query($connection, $sql_display_all);

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
<!-- Modal for adding Settings 1 -->
<div class="modal fade" id="addSettings1Modal" tabindex="-1" aria-labelledby="addSettings1ModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addSettings1ModalLabel">Add Settings 1</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Form for adding Settings 1 -->
                <form method="post" action="settings_add1.php" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label for="title" class="form-label">Title</label>
                        <input type="text" class="form-control" id="title" name="title" required>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="3" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="logo" class="form-label">Logo</label>
                        <input type="file" class="form-control" id="logo" name="logo" required>
                    </div>
                    <div class="mb-3">
                        <label for="image1" class="form-label">Image 1</label>
                        <input type="file" class="form-control" id="logo" name="image1" required>
                    </div>
                    <div class="mb-3">
                        <label for="image2" class="form-label">Image 2</label>
                        <input type="file" class="form-control" id="logo" name="image2" required>
                    </div>
                    <div class="mb-3">
                        <label for="contact1" class="form-label">Contact 1</label>
                        <input type="text" class="form-control" id="contact1" name="contact1" required>
                    </div>
                    <div class="mb-3">
                        <label for="contact2" class="form-label">Contact 2</label>
                        <input type="text" class="form-control" id="contact2" name="contact2" required>
                    </div>
                    <!-- Add more fields as needed -->
                    <button type="submit" name="submitBtn" class="btn btn-primary">Submit</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal for adding Settings 2 -->
<div class="modal fade" id="addSettings2Modal" tabindex="-1" aria-labelledby="addSettings2ModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addSettings2ModalLabel">Add Settings 2</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Form for adding Settings 2 -->
                <form method="post" action="settings_add2.php" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label for="about1" class="form-label">About 1</label>
                        <textarea class="form-control" id="about1" name="about1" rows="3" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="about2" class="form-label">About 2</label>
                        <textarea class="form-control" id="about2" name="about2" rows="3" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="about3" class="form-label">About 3</label>
                        <textarea class="form-control" id="about3" name="about3" rows="3" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="about4" class="form-label">About 4</label>
                        <textarea class="form-control" id="about4" name="about4" rows="3" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="image1" class="form-label">Image 1</label>
                        <input type="file" class="form-control" id="logo" name="image1" required>
                    </div>
                    <div class="mb-3">
                        <label for="image2" class="form-label">Image 2</label>
                        <input type="file" class="form-control" id="logo" name="image2" required>
                    </div>
                    <div class="mb-3">
                        <label for="image3" class="form-label">Image 3</label>
                        <input type="file" class="form-control" id="logo" name="image3" required>
                    </div>
                    <div class="mb-3">
                        <label for="image4" class="form-label">Image 4</label>
                        <input type="file" class="form-control" id="logo" name="image4" required>
                    </div>
                    <div class="mb-3">
                        <label for="image5" class="form-label">Image 5</label>
                        <input type="file" class="form-control" id="logo" name="image5" required>
                    </div>
                    <div class="mb-3">
                        <label for="image6" class="form-label">Image 6</label>
                        <input type="file" class="form-control" id="logo" name="image6" required>
                    </div>
                    <!-- Add more fields as needed -->
                    <button type="submit" name="submitBtn" class="btn btn-primary">Submit</button>
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
                <h1 class="text-center"><b>Settings</b></h1>
                <hr class="border-3 border-dark mx-auto opacity-100" style="width:80px">

                <!-- Add New Category Button -->
                <a class="btn btn-success" role="button" data-bs-toggle="modal" data-bs-target="#addSettings1Modal">
                    <span class="material-symbols-outlined">edit</span>Add Settings 1
                </a>

                 <table class="table table-striped" style="width:100%">
                    <thead>
                        <tr>
                            <th><center>Title</center></th>
                            <th><center>Description</center></th>
                            <th><center>Logo</center></th>
                            <th><center>Image 1</center></th>
                            <th><center>Image 2</center></th>
                            <th><center>Contact 1</center></th>
                            <th><center>Contact 2</center></th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                         <?php while($rows = mysqli_fetch_array($settings1)){ ?>
                        <tr>
                            <td><center><?php echo $rows['title']; ?></center></td>
                            <td><center><?php echo $rows['description']; ?></center></td>
                            <td><center>
                                <?php
                                // Fetch the image path from the database
                                $imagePath = $rows['logo'];

                                // Construct the complete URL to the image
                                $imageURL = 'http://localhost/Ecomerce2/images/' . $imagePath;

                                // Display the image
                                echo '<img src="' . $imageURL . '" style="max-width: 100px; max-height: 100px;">';
                                ?>  
                            </center></td>
                            <td><center>
                                <?php
                                // Fetch the image path from the database
                                $imagePath = $rows['image1'];

                                // Construct the complete URL to the image
                                $imageURL = 'http://localhost/Ecomerce2/images/' . $imagePath;

                                // Display the image
                                echo '<img src="' . $imageURL . '" style="max-width: 150px; max-height: 300px;">';
                                ?>  
                            </center></td>
                            <td><center>
                                <?php
                                // Fetch the image path from the database
                                $imagePath = $rows['image2'];

                                // Construct the complete URL to the image
                                $imageURL = 'http://localhost/Ecomerce2/images/' . $imagePath;

                                // Display the image
                                echo '<img src="' . $imageURL . '" style="max-width: 100px; max-height: 100px;">';
                                ?>  
                            </center></td>
                            <td><center><?php echo $rows['contact1']; ?></center></td>
                            <td><center><?php echo $rows['contact2']; ?></center></td>
                            <td><center>

                            <a class="btn btn-outline-warning" href="settingsedit.php?id=<?php echo $rows['id']; ?>" role="button"><span class="material-symbols-outlined">edit</span>Edit</a>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>

                <!-- Add New Category Button -->
                <a class="btn btn-success" role="button" data-bs-toggle="modal" data-bs-target="#addSettings2Modal">
                    <span class="material-symbols-outlined">edit</span>Add Settings 2
                </a>

                <table class="table table-striped" style="width:100%">
                    <thead>
                        <tr>
                            <th><center>About 1</center></th>
                            <th><center>About 2</center></th>
                            <th><center>About 3</center></th>
                            <th><center>About 4</center></th>
                            <th><center>Image 1</center></th>
                            <th><center>Image 2</center></th>
                            <th><center>Image 3</center></th>
                            <th><center>Image 4</center></th>
                            <th><center>Image 5</center></th>
                            <th><center>Image 6</center></th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                         <?php while($rows = mysqli_fetch_array($settings2)){ ?>
                        <tr>
                            <td><center><?php echo $rows['about1']; ?></center></td>
                            <td><center><?php echo $rows['about2']; ?></center></td>
                            <td><center><?php echo $rows['about3']; ?></center></td>
                            <td><center><?php echo $rows['about4']; ?></center></td>
                            <td><center>
                                <?php
                                // Fetch the image path from the database
                                $imagePath = $rows['image1'];

                                // Construct the complete URL to the image
                                $imageURL = 'http://localhost/Ecomerce2/images/' . $imagePath;

                                // Display the image
                                echo '<img src="' . $imageURL . '" style="max-width: 100px; max-height: 100px;">';
                                ?>  
                            </center></td>
                            <td><center>
                                <?php
                                // Fetch the image path from the database
                                $imagePath = $rows['image2'];

                                // Construct the complete URL to the image
                                $imageURL = 'http://localhost/Ecomerce2/images/' . $imagePath;

                                // Display the image
                                echo '<img src="' . $imageURL . '" style="max-width: 150px; max-height: 300px;">';
                                ?>  
                            </center></td>
                            <td><center>
                                <?php
                                // Fetch the image path from the database
                                $imagePath = $rows['image3'];

                                // Construct the complete URL to the image
                                $imageURL = 'http://localhost/Ecomerce2/images/' . $imagePath;

                                // Display the image
                                echo '<img src="' . $imageURL . '" style="max-width: 100px; max-height: 100px;">';
                                ?>  
                            </center></td>
                            <td><center>
                                <?php
                                // Fetch the image path from the database
                                $imagePath = $rows['image4'];

                                // Construct the complete URL to the image
                                $imageURL = 'http://localhost/Ecomerce2/images/' . $imagePath;

                                // Display the image
                                echo '<img src="' . $imageURL . '" style="max-width: 100px; max-height: 100px;">';
                                ?>  
                            </center></td>
                            <td><center>
                                <?php
                                // Fetch the image path from the database
                                $imagePath = $rows['image5'];

                                // Construct the complete URL to the image
                                $imageURL = 'http://localhost/Ecomerce2/images/' . $imagePath;

                                // Display the image
                                echo '<img src="' . $imageURL . '" style="max-width: 100px; max-height: 100px;">';
                                ?>  
                            </center></td>
                            <td><center>
                                <?php
                                // Fetch the image path from the database
                                $imagePath = $rows['image6'];

                                // Construct the complete URL to the image
                                $imageURL = 'http://localhost/Ecomerce2/images/' . $imagePath;

                                // Display the image
                                echo '<img src="' . $imageURL . '" style="max-width: 100px; max-height: 100px;">';
                                ?>  
                            </center></td>
                            <td><center>

                            <a class="btn btn-outline-warning" href="settingsedit2.php?id=<?php echo $rows['id']; ?>" role="button"><span class="material-symbols-outlined">edit</span>Edit</a>
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
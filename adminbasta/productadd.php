<?php 
session_start();
require_once '../config/connection.php';

$id = $_SESSION['id'];
$query = "SELECT * FROM users WHERE id='$id'";
$nav = mysqli_query($connection, $query);

$select = "SELECT * FROM products";
$result2 = mysqli_query($connection, $select);

$select = "SELECT * FROM products_category";
$result = mysqli_query($connection, $select);

// Fetch all users
$query_users = "SELECT * FROM users ORDER BY id DESC";
$result_users = mysqli_query($connection, $query_users);

// Fetch all users and count the number of orders with "Not Paid" status for each user
$sql_display_all = "SELECT *, IFNULL(unpaid_orders.num_unpaid_orders, 0) AS num_unpaid_orders FROM users LEFT JOIN (SELECT user_id, COUNT(*) AS num_unpaid_orders FROM orders WHERE order_status = 'Not Paid' GROUP BY user_id) AS unpaid_orders ON id = user_id ORDER BY id DESC";
$query = mysqli_query($connection, $sql_display_all);

if(isset($_POST['submit'])){
    //prepared statement to prevent SQL injection
    $insert_query = "INSERT INTO products (product_image, product_name, product_description, product_quantity, product_price, product_category) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($connection, $insert_query);

    if (!$stmt) {
        die("Error: " . mysqli_error($connection));
    }


    $name = $_POST['product_name'];
    $description = $_POST['product_description'];
    $quantity = $_POST['product_quantity'];
    $price = $_POST['product_price'];
    $category = $_POST['product_category'];

    // Define the target directory for file uploads
    $targetDirectory = '../images/';

    // Define the file path
    $imgPath = $targetDirectory . basename($_FILES['product_image']['name']);

    // Move the uploaded file to the target directory
    move_uploaded_file($_FILES['product_image']['tmp_name'], $imgPath);

    mysqli_stmt_bind_param($stmt, "sssiis", $imgPath, $name, $description, $quantity, $price, $category);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    header("Location: product.php");
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

                        http://localhost/Ecomerce2/teamleader/accountmanage.php

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

                
                  <div class="container py-5">
                    <h1 class="text-center"><b>New Product</b></h1>
                <hr class="border-3 border-dark mx-auto opacity-100" style="width:70px">
                    <div class="row d-flex justify-content-center align-items-center h-100">
                      <div class="col-12">
                        <div class="card card-registration card-registration-2" style="border-radius: 15px;">
                          <div class="card-body p-0">
                            <div class="row g-0">
                              <div class="col-lg-6">
                                <div class="p-5">
                <form action="productadd.php" method="POST" enctype="multipart/form-data">

                  <div class="row">

                    <div class="mb-4 pb-2">
                        <div class="form-outline">
                            <label class="form-label" for="product_category">Category</label>
                            <select name="product_category" id="product_category" class="form-select form-select-lg">
                                <option></option>
                                <?php while($row = mysqli_fetch_assoc($result)) { ?>
                                <option><?php echo $row['product_category']; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-6 mb-4 pb-2">

                      <div class="form-outline">
                        <input type="text" name="product_name" id="form3Examplev2" class="form-control form-control-lg" />
                        <label class="form-label" for="form3Examplev2">Product name</label>
                      </div>

                    </div>
                    <div class="col-md-6 mb-4 pb-2">

                      <div class="form-outline">
                        <input type="text" name="product_description" id="form3Examplev3" class="form-control form-control-lg" />
                        <label class="form-label" for="form3Examplev3">Description</label>
                      </div>

                    </div>
                  </div>

                  <div class="mb-4 pb-2">
                    <div class="form-outline">
                        <label class="form-label" for="form3Examplev4">Product image</label>
                        <label for="uploadimg" class="upload">
                            <input type="file" name="product_image" id="uploadimg" id="form3Examplev4" class="form-control form-control-m" onchange="previewFile()">
                            <i class="fa-solid fa-plus"></i>
                        </label>
                        <?php $row = mysqli_fetch_assoc($result2);
                            if ($row){
                            // Fetch the image path from the database
                            $imagePath = $row['product_image'];

                            // Construct the complete URL to the image
                            $imageURL = 'http://localhost/Ecomerce2/images/' . $imagePath;

                            // Display the image
                            echo '<img id="previewImage" id="currentProfilePicture" src="' . $imageURL . '" alt="Profile Picture" style=" max-width: 200px; max-height: 350px;" class="img">';
                            } 
                        ?>
                    </div>
                  </div>

                </div>
              </div>
              <div class="col-lg-6">
                <div class="p-5">

                <div class="mb-4 pb-2">
                    <div class="form-outline">
                      <input type="number" name="product_quantity" id="form3Examplev4" class="form-control form-control-lg" />
                      <label class="form-label" for="form3Examplev4">Quantity</label>
                    </div>
                  </div>

                  <div class="mb-4 pb-2">
                    <div class="form-outline form-white">
                      <input type="text" name="product_price" id="form3Examplea2" class="form-control form-control-lg" />
                      <label class="form-label" for="form3Examplea2">Price</label>
                    </div>
                  </div>        

                  <center><button type="submit" name="submit" href="productadd.php" class="btn btn-light btn-lg"
                    data-mdb-ripple-color="dark">Add Product</button>
                    <a type="button" href="product.php" class="btn btn-light btn-lg"
                    data-mdb-ripple-color="dark">Go Back</a>
                </center>

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

<script>
    function previewFile() {
        const preview = document.getElementById('previewImage');
        const currentProfilePicture = document.getElementById('currentProfilePicture');
        const file = document.querySelector('input[type=file]').files[0];
        const reader = new FileReader();

        reader.onloadend = function () {
            preview.src = reader.result;
        };

        if (file) {
            reader.readAsDataURL(file);
        } else {
            // If no file is selected, display the current profile picture
            preview.src = currentProfilePicture.src;
        }
    }
</script>
    <script src="./assets/js/custom-admin-template.js"></script>
</body>
</html>
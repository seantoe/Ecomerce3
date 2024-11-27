<?php
    session_start();
    $page_title = 'Account Management';
    require_once '../config/connection.php';

$id = $_SESSION['id'];
// Fetch user information for navigation
$query_nav = "SELECT * FROM users WHERE id='$id'";
$result_nav = mysqli_query($connection, $query_nav);
$row_nav = mysqli_fetch_assoc($result_nav);

$id = $_SESSION['id'];
$query = "SELECT * FROM users WHERE id='$id'";
$sql = mysqli_query($connection, $query);

if (isset($_POST['updateForm'])) {
        $id = $_POST['id'];
        $email= $_POST['email'];
        $fname = $_POST['firstname'];
        $lname = $_POST['lastname'];
        $address = $_POST['address'];
        $contact = $_POST['contact'];
        // Check if a file is uploaded
        if(isset($_FILES['pfp']) && $_FILES['pfp']['error'] === UPLOAD_ERR_OK) {
            // Define the target directory outside of the teamleader directory
            $targetDirectory = '../images/';

            // Define the file name and path
            $pfp = $targetDirectory . basename($_FILES['pfp']['name']);

            // Move the uploaded file to the target directory
            if(move_uploaded_file($_FILES['pfp']['tmp_name'], $pfp)) {
                // Update the database with the new profile picture path
                $updateQuery = "UPDATE users SET email='$email', firstname='$fname', lastname='$lname', address='$address', contact='$contact', pfp='$pfp' WHERE id='$id' ";
                mysqli_query($connection, $updateQuery);
            } else {
                // Handle file upload error
                echo "Sorry, there was an error uploading your file.";
            }
        } else {
            // If no file is uploaded, update the database without changing the profile picture
            $updateQuery = "UPDATE users SET email='$email', firstname='$fname', lastname='$lname', address='$address', contact='$contact' WHERE id='$id' ";
            mysqli_query($connection, $updateQuery);
        }

        header('location: accountmanage.php');
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

                
                  <div class="container py-5">
                    <h1 class="text-center"><b>Account Management</b></h1>
                <hr class="border-3 border-dark mx-auto opacity-100" style="width:70px">
                    <div class="row d-flex justify-content-center align-items-center h-100">
                      <div class="col-12">
                        <div class="card card-registration card-registration-2" style="border-radius: 15px;">
                          <div class="card-body p-0">
                            <div class="row g-0">
                              <div class="col-lg-6">
                                <div class="p-5">
                <form action="accountmanage.php" method="POST" enctype="multipart/form-data">
                    <?php while($row = mysqli_fetch_array($sql)) { ?>

                  <h3 class="fw-normal mb-5" style="color: #4835d4;">General Infomation</h3>

                  <div class="mb-4 pb-2">
                    <div class="form-outline">
                      <?php
                            // Fetch the image path from the database
                            $imagePath = $row['pfp'];

                            // Construct the complete URL to the image
                            $imageURL = 'http://localhost/Ecomerce2/images/' . $imagePath;

                            // Display the image
                            echo '<img id="previewImage" id="currentProfilePicture" src="' . $imageURL . '" alt="Profile Picture" width="150" height="150" class="img" style="border-radius: 50%;">';
                        ?>
                        <label for="uploadimg" class="upload">
                            <input type="file" name="pfp" id="uploadimg" id="form3Examplev4" class="form-control form-control-m" onchange="previewFile()">
                            <i class="fa-solid fa-plus"></i>
                        </label>
                    </div>
                  </div>

                  <div class="row">
                    <div class="col-md-6 mb-4 pb-2">
                        <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                      <div class="form-outline">
                        <input type="text" name="firstname" value="<?php echo $row['firstname']; ?>" id="form3Examplev2" class="form-control form-control-lg" />
                        <label class="form-label" for="form3Examplev2">First name</label>
                      </div>

                    </div>
                    <div class="col-md-6 mb-4 pb-2">

                      <div class="form-outline">
                        <input type="text" name="lastname" value="<?php echo $row['lastname']; ?>" id="form3Examplev3" class="form-control form-control-lg" />
                        <label class="form-label" for="form3Examplev3">Last name</label>
                      </div>

                    </div>
                  </div>

                  <div class="mb-4 pb-2">
                    <div class="form-outline">
                      <input type="text" name="email" value="<?php echo $row['email']; ?>" id="form3Examplev4" class="form-control form-control-lg" />
                      <label class="form-label" for="form3Examplev4">Email</label>
                    </div>
                  </div>

                </div>
              </div>
              <div class="col-lg-6 bg-indigo text-white">
                <div class="p-5">
                  <h3 class="fw-normal mb-5">Contact Details</h3>

                  <div class="mb-4 pb-2">
                    <div class="form-outline form-white">
                      <input type="text" name="address" value="<?php echo $row['address']; ?>" id="form3Examplea2" class="form-control form-control-lg" />
                      <label class="form-label" for="form3Examplea2">Full Address</label>
                    </div>
                  </div>        
                 
                    <div class="mb-4 pb-2">
                    <div class="form-outline form-white">
                      <input type="text" name="contact" value="<?php echo $row['contact']; ?>" id="form3Examplea2" class="form-control form-control-lg" />
                      <label class="form-label" for="form3Examplea2">Contact</label>
                    </div>
                  </div>
                  
                  <button type="submit" name="updateForm" class="btn btn-light btn-lg"
                    data-mdb-ripple-color="dark">Update</button>
                <?php } ?>
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
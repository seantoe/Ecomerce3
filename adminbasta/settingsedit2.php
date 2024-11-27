<?php
session_start();
$page_title = 'Edit';
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

if(isset($_GET['id'])){
    // Fetch the product details based on the provided ID and populate the form for editing
$id = $_GET['id'];
}
$selectQuery = "SELECT * FROM settings2 WHERE id=?";
$stmt = mysqli_prepare($connection, $selectQuery);
mysqli_stmt_bind_param($stmt, 'i', $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (isset($_POST['updateForm'])) {
    $id = $_POST['id'];
    $about1 = $_POST['about1'];
    $about2 = $_POST['about2'];
    $about3 = $_POST['about3'];
    $about4 = $_POST['about4'];

    // Define the target directory for file uploads
    $targetDirectory = '../images/';

    // Define the file paths
    $image1 = $targetDirectory . basename($_FILES['image1']['name']);
    $image2 = $targetDirectory . basename($_FILES['image2']['name']);
    $image3 = $targetDirectory . basename($_FILES['image3']['name']);
    $image4 = $targetDirectory . basename($_FILES['image4']['name']);
    $image5 = $targetDirectory . basename($_FILES['image5']['name']);
    $image6 = $targetDirectory . basename($_FILES['image6']['name']);

    // Move the uploaded files to the target directory
    move_uploaded_file($_FILES['image1']['tmp_name'], $image1);
    move_uploaded_file($_FILES['image2']['tmp_name'], $image2);
    move_uploaded_file($_FILES['image3']['tmp_name'], $image3);
    move_uploaded_file($_FILES['image4']['tmp_name'], $image4);
    move_uploaded_file($_FILES['image5']['tmp_name'], $image5);
    move_uploaded_file($_FILES['image6']['tmp_name'], $image6);

    $updateQuery = "UPDATE settings2 SET about1=?, about2=?, about3=?, about4=?, image1=?, image2=?, image3=?, image4=?, image5=?, image6=?, WHERE id=?";
    $updateStmt = mysqli_prepare($connection, $updateQuery);
    mysqli_stmt_bind_param($updateStmt, 'ssssssssssi', $about1,$about2,$about3,$about4,$image1,$image2,$image3,$image4,$image5,$image6, $id);
    if (mysqli_stmt_execute($updateStmt)) {
        header('location: settings.php');
        exit();
    } else {
        // Handle the error, e.g., display an error message or log it
        echo "Update failed: " . mysqli_error($connection);
    }
    mysqli_stmt_close($updateStmt);
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
                    <h1 class="text-center"><b>Settings Edit</b></h1>
                <hr class="border-3 border-dark mx-auto opacity-100" style="width:70px">
                    <div class="row d-flex justify-content-center align-items-center h-100">
                      <div class="col-12">
                        <div class="card card-registration card-registration-2" style="border-radius: 15px;">
                          <div class="card-body p-0">
                            <div class="row g-0">
                              <div class="col-lg-6">
                                <div class="p-5">
                <form action="settingsedit.php" method="POST" enctype="multipart/form-data">
                    <?php while ($row = mysqli_fetch_array($result)) { ?>
                      <input type="hidden" name="id" value="<?php echo $row['id']; ?>">

                  <div class="mb-4 pb-2">
                    <div class="form-outline">
                      <input type="text" name="about1" value="<?php echo $row['about1']; ?>" id="form3Examplev4" class="form-control form-control-m" />
                      <label class="form-label" for="form3Examplev4">About 1</label>
                    </div>
                  </div>

                  <div class="mb-4 pb-2">
                    <div class="form-outline">
                      <input type="text" name="about2" value="<?php echo $row['about2']; ?>" id="form3Examplev4" class="form-control form-control-sm" />
                      <label class="form-label" for="form3Examplev4">About 2</label>
                    </div>
                  </div>

                  <div class="mb-4 pb-2">
                    <div class="form-outline">
                      <input type="text" name="about3" value="<?php echo $row['about3']; ?>" id="form3Examplev4" class="form-control form-control-sm" />
                      <label class="form-label" for="form3Examplev4">About 3</label>
                    </div>
                  </div>

                  <div class="mb-4 pb-2">
                    <div class="form-outline">
                      <input type="text" name="about4" value="<?php echo $row['about4']; ?>" id="form3Examplev4" class="form-control form-control-sm" />
                      <label class="form-label" for="form3Examplev4">About 4</label>
                    </div>
                  </div>

                  <div class="mb-4 pb-2">
                    <div class="form-outline">
                      <input type="file" name="image1" accept="image/*" id="image1Input" class="form-control form-control-m" onchange="previewImage(this, 'logoPreview')"  />
                      <label class="form-label" for="form3Examplev4">Image 1</label>
                      <div class="profile-photo">
                       <?php
                        // Fetch the image path from the database
                        $imagePath = $row['image1'];

                        // Construct the complete URL to the image
                        $imageURL = 'http://localhost/Ecomerce2/images/' . $imagePath;

                        // Display the image
                        echo '<img id="image1Preview" src="' . $imageURL . '" alt="Image 1 Preview" style="max-width: 150px; max-height: 300px; margin-top: 10px;">';
                        ?>
                        </div>
                    </div>
                  </div>
                  
                    <div class="mb-4 pb-2">
                    <div class="form-outline">
                      <input type="file" name="image2" accept="image/*" id="image2Input" class="form-control form-control-m" onchange="previewImage(this, 'image2Preview')" />
                      <label class="form-label" for="form3Examplev4">Image</label>
                      <div class="profile-photo">
                       <?php
                        // Fetch the image path from the database
                        $imagePath = $row['image2'];

                        // Construct the complete URL to the image
                        $imageURL = 'http://localhost/Ecomerce2/images/' . $imagePath;

                        // Display the image
                        echo '<img id="image2Preview" src="' . $imageURL . '" alt="Image 2 Preview" style="max-width: 150px; max-height: 300px; margin-top: 10px;">';
                        ?>
                        </div>
                    </div>
                  </div>
                    
                </div>
              </div>
              <div class="col-lg-6">
                <div class="p-5">

                  <div class="mb-4 pb-2">
                    <div class="form-outline">
                      <input type="file" name="image3" accept="image/*" id="image3Input" class="form-control form-control-m" onchange="previewImage(this, 'image3Preview')" />
                      <label class="form-label" for="form3Examplev4">Image</label>
                      <div class="profile-photo">
                       <?php
                        // Fetch the image path from the database
                        $imagePath = $row['image3'];

                        // Construct the complete URL to the image
                        $imageURL = 'http://localhost/Ecomerce2/images/' . $imagePath;

                        // Display the image
                        echo '<img id="image3Preview" src="' . $imageURL . '" alt="Image 3 Preview" style="max-width: 150px; max-height: 300px; margin-top: 10px;">';
                        ?>
                        </div>
                    </div>
                  </div>

                  <div class="mb-4 pb-2">
                    <div class="form-outline">
                      <input type="file" name="image4" accept="image/*" id="image4Input" class="form-control form-control-m" onchange="previewImage(this, 'image4Preview')" />
                      <label class="form-label" for="form3Examplev4">Image</label>
                      <div class="profile-photo">
                       <?php
                        // Fetch the image path from the database
                        $imagePath = $row['image4'];

                        // Construct the complete URL to the image
                        $imageURL = 'http://localhost/Ecomerce2/images/' . $imagePath;

                        // Display the image
                        echo '<img id="image4Preview" src="' . $imageURL . '" alt="Image 4 Preview" style="max-width: 150px; max-height: 300px; margin-top: 10px;">';
                        ?>
                        </div>
                    </div>
                  </div>

                  <div class="mb-4 pb-2">
                    <div class="form-outline">
                      <input type="file" name="image5" accept="image/*" id="image5Input" class="form-control form-control-m" onchange="previewImage(this, 'image5Preview')" />
                      <label class="form-label" for="form3Examplev4">Image</label>
                      <div class="profile-photo">
                       <?php
                        // Fetch the image path from the database
                        $imagePath = $row['image5'];

                        // Construct the complete URL to the image
                        $imageURL = 'http://localhost/Ecomerce2/images/' . $imagePath;

                        // Display the image
                        echo '<img id="image5Preview" src="' . $imageURL . '" alt="Image 5 Preview" style="max-width: 150px; max-height: 300px; margin-top: 10px;">';
                        ?>
                        </div>
                    </div>
                  </div>

                  <div class="mb-4 pb-2">
                    <div class="form-outline">
                      <input type="file" name="image6" accept="image/*" id="image6Input" class="form-control form-control-m" onchange="previewImage(this, 'image6Preview')" />
                      <label class="form-label" for="form3Examplev4">Image</label>
                      <div class="profile-photo">
                       <?php
                        // Fetch the image path from the database
                        $imagePath = $row['image6'];

                        // Construct the complete URL to the image
                        $imageURL = 'http://localhost/Ecomerce2/images/' . $imagePath;

                        // Display the image
                        echo '<img id="image6Preview" src="' . $imageURL . '" alt="Image 6 Preview" style="max-width: 150px; max-height: 300px; margin-top: 10px;">';
                        ?>
                        </div>
                    </div>
                  </div>

                  <button type="submit" name="updateForm" class="btn btn-dark btn-lg"
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
    function previewImage(input, imageID) {
        const preview = document.getElementById(imageID);
        const file = input.files[0];
        const reader = new FileReader();

        reader.onloadend = function () {
            preview.src = reader.result;
        }

        if (file) {
            reader.readAsDataURL(file);
        } else {
            preview.src = "";
        }
    }
</script>
    <script src="./assets/js/custom-admin-template.js"></script>
</body>
</html>
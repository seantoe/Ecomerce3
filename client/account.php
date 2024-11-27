<?php
    session_start();
    require_once '../config/connection.php';

// Check if the user is not logged in
if (!isset($_SESSION['id'])) {
    header('location: ../login.php');
    exit(); // Stop further execution
    }

if(isset($_POST['change_password'])){

    $password = $_POST['password'];
    $confirmpassword = $_POST['confirmPassword'];
    $email = $_SESSION['email'];

    if($password !== $confirmpassword){
        header('location: account.php?error=Password Do Not Match');

    }else if(strlen($password) < 6){
        header('location: account.php?error=Password must be at least 6 characters');

    }else{

        $stmt = $connection->prepare("UPDATE users SET password = ? WHERE email = ?");
        $stmt->bind_param('ss', md5($password), $email);

        if($stmt->execute()){
            header('location: account.php?message=Password has been updated successfully');

        }else{
            header('location: account.php?error=Password did not update');
        }
    }
}

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

        header('location: account.php');
    }

$sqldisplayweb = "SELECT * FROM settings";
$query2 = mysqli_query($connection, $sqldisplayweb);

$select="SELECT * FROM settings";
$result=mysqli_query($connection,$select);


//get orders
if (!isset($_SESSION['id'])) {
    header('location: ../login.php');
    exit(); // Stop further execution
    }else{

        $user_id = $_SESSION['id'];
        $stmt = $connection->prepare("SELECT * FROM orders WHERE user_id = ?");

        $stmt->bind_param('i', $user_id);

        $stmt->execute();

        $orders = $stmt->get_result(); //[]
    }

// Handle removal of order from the database
if(isset($_POST['remove_order'])) {
    $order_id = $_POST['remove_order_id'];
    
    // Remove order from the orders table
    $delete_order_query = "DELETE FROM orders WHERE order_id = ?";
    $stmt1 = $connection->prepare($delete_order_query);
    $stmt1->bind_param('i', $order_id);
    $stmt1->execute();
    $stmt1->close();

    // Remove related order items from the order_item table
    $delete_order_items_query = "DELETE FROM order_items WHERE order_id = ?";
    $stmt2 = $connection->prepare($delete_order_items_query);
    $stmt2->bind_param('i', $order_id);
    $stmt2->execute();
    $stmt2->close();

    // Redirect to refresh the page or perform any other action
    header('location: account.php');
    exit(); // Stop further execution
}

?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Ecommerce Website</title>
        <link rel="stylesheet" href="../css/style2.css">
        <link rel="stylesheet" href="../css/client.css">
        
        
        <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet"> 

        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" />

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Amatic+SC:wght@700&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />

        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/css/bootstrap.min.css">
        <link rel="stylesheet" href="https://cdn.datatables.net/2.0.0/css/dataTables.bootstrap5.css">
    </head>

<body>
    <!-- header -->
    <?php while($rows = mysqli_fetch_array($query2)){ ?>
            <div class="header">
                <div class="container">
                     <div class="navbar">
                        <div class="logo">
                        <?php
                            // Fetch the image path from the database
                            $imagePath = $rows['logo'];

                            // Construct the complete URL to the image
                            $imageURL = 'http://localhost/Ecomerce2/images/' . $imagePath;

                            // Display the image
                            echo '<img src="' . $imageURL . '" alt="Profile Picture" width="125px">';
                        ?>
                        </div>
                    <nav>
                        <ul id="MenuItems">
                            <li><a href="homepage.php">Home</a></li>
                            <li><a href="about.php">About</a></li>
                            <li><a href="products.php">Products</a></li>
                            <li><a href="account.php">Account</a></li>
                        </ul>
                    </nav>
                    <a href="cart.php"><span class="material-symbols-outlined">
                        shopping_cart
                        </span></a>
                    </div>
                </div>
            </div>
<?php } ?>

<!-- Content -->
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

                 <form action="account.php" method="POST" enctype="multipart/form-data">
                  <?php while($row = mysqli_fetch_array($sql)) { ?>
                 <div class="row">
                    <div class="col-md-6 mb-4 pb-2">
                  <h3 class="fw-normal mb-5" style="color:black;">Profile</h3>

                  </div>
                  <input type="hidden" name="id" value="<?php echo $row['id']; ?>" readonly>
                  <div class="col-md-6 mb-4 pb-2">
                  <div class="imgholder">                        
                        <?php
                            // Fetch the image path from the database
                            $imagePath = $row['pfp'];

                            // Construct the complete URL to the image
                            $imageURL = 'http://localhost/Ecomerce2/images/' . $imagePath;

                            // Display the image
                            echo '<img id="previewImage" id="currentProfilePicture" src="' . $imageURL . '" width="150" height="150" class="img" style="border-radius: 50%;">';
                        ?>
                        <label for="uploadimg" class="upload">
                            <input type="file" name="pfp" id="uploadimg" class="picture" onchange="previewFile()">
                            <i class="fa-solid fa-plus"></i>
                        </label>
                    </div>
                  </div>
                  </div>  

                  <div class="row">
                    <div class="col-md-6 mb-4 pb-2">

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

                  <div class="row">
                    <div class="col-md-6 mb-4 pb-2">

                      <div class="form-outline">
                        <input type="text" name="address" value="<?php echo $row['address']; ?>" id="form3Examplev2" class="form-control form-control-lg" />
                        <label class="form-label" for="form3Examplev2">Address</label>
                      </div>

                    </div>
                    <div class="col-md-6 mb-4 pb-2">

                      <div class="form-outline">
                        <input type="text" name="contact" value="<?php echo $row['contact']; ?>" id="form3Examplev3" class="form-control form-control-lg" />
                        <label class="form-label" for="form3Examplev3">Contact</label>
                      </div>

                    </div>
                  </div>

                  <div class="row">
                    <div class="col-md-6 mb-4 pb-2">

                    <button type="submit" name="updateForm" class="btn btn-secondary btn-lg"
                    data-mdb-ripple-color="dark">Update</button>

                    </div>
                    <div class="col-md-6 mb-4 pb-2">

                    <a href="logout.php" class="btn btn-danger btn-lg">Logout</a>
                    </div>
                  </div>
              <?php } ?>
          </form>
                </div>
              </div>

              <div class="col-lg-6 bg-indigo text-black">
                <div class="p-5">
                    <form action="account.php" method="POST">
                    <h3 class="fw-normal mb-5">Change Password</h3>
                  

                  <div class="mb-4 pb-2">
                    <div class="form-outline form-white">
                      <p style="color:red"><?php if(isset($_GET['error'])) { echo $_GET['error']; } ?></p>
                      <p style="color:green"><?php if(isset($_GET['message'])) { echo $_GET['message']; } ?></p>
                      <input type="password" name="password" id="form3Examplea2" class="form-control form-control-lg" />
                      <label class="form-label" for="form3Examplea2">Password</label>
                    </div>
                  </div>        
                 
                    <div class="mb-4 pb-2">
                    <div class="form-outline form-white">
                      <input type="password" name="confirmPassword" id="form3Examplea2" class="form-control form-control-lg" />
                      <label class="form-label" for="form3Examplea2">Confirm Password</label>
                    </div>
                  </div>
                  

                  <button type="submit" name="change_password" class="btn btn-light btn-lg"
                    data-mdb-ripple-color="dark">Change Password</button>
                </form>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
 

<div class="ordertable">
<table id="example" class="table table-striped" style="width:100%">
<h1 class="text-center"><b>Your Orders</b></h1>
        <thead>
            <tr>
                <th><center>Order Id</center></th>
                <th><center>Order Cost</center></th>
                <th><center>Order Status</center></th>                
                <th><center>Order Date</center></th>
                <th><center>Order Details</center></th>
            </tr>
        </thead>
        <tbody>
          <?php while($row = $orders->fetch_assoc()) { ?>
            <tr>
                <td><center><?php echo $row['order_id']; ?></center></td>
                <td><center><?php echo $row['order_cost']; ?></center></td>
                <td><center><?php echo $row['order_status']; ?></center></td>
                <td><center><?php echo $row['order_date']; ?></center></td>
                <td><center>
                    <form action="order_details.php" method="POST">
                      <input type="hidden" name="order_status" value="<?php echo $row['order_status']; ?>">
                      <input type="hidden" name="order_id" value="<?php echo $row['order_id']; ?>">
                      <input type="submit" name="order_details_btn" value="Details">
                    </form>
                  <?php if ($row['order_status'] !== 'Pending' && $row['order_status'] !== 'Paid') { ?>
                  <form action="account.php" method="POST">
                      <input type="hidden" name="remove_order_id" value="<?php echo $row['order_id']; ?>">
                      <input type="submit" name="remove_order" value="Remove Order"/>
                  </form>
                  <?php } ?>
                </center></td>                
            </tr>
        <?php } ?>
        </tbody>
    </table>
</div>

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
    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/2.0.0/js/dataTables.js"></script>
    <script src="https://cdn.datatables.net/2.0.0/js/dataTables.bootstrap5.js"></script>

<script type="logout.js"></script>
</body>
</html>
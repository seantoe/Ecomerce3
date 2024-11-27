<?php
session_start();
require '../config/connection.php';

// Check if the user is not logged in
if (!isset($_SESSION['id'])) {
    header('location: login.php');
    exit(); // Stop further execution
}

$select = "SELECT * FROM settings";
$result = mysqli_query($connection, $select);

$sqldisplayweb = "SELECT * FROM settings";
$query2 = mysqli_query($connection, $sqldisplayweb);

if (isset($_POST['proceed_btn'])) {
    // Define the target directory
    $targetDirectory = '../images/';

    // Define the file name and path
    $proof_of_payment = $targetDirectory . basename($_FILES['proof_of_payment']['name']);

    // Move the uploaded file to the target directory
    if (move_uploaded_file($_FILES['proof_of_payment']['tmp_name'], $proof_of_payment)) {
        // Update order status and proof of payment in the database
        $order_id = $_POST['order_id']; // Assuming you have stored order_id in session
        $update_query = "UPDATE orders SET order_status = 'Pending', proof_of_payment = '$proof_of_payment' WHERE order_id = $order_id";
        mysqli_query($connection, $update_query);

        // Update product_quantity in the products table
        $products_query = "SELECT product_id, product_quantity FROM order_items WHERE order_id = $order_id";
        $products_result = mysqli_query($connection, $products_query);

        // Iterate over each product in the order
        while ($row = mysqli_fetch_assoc($products_result)) {
            $product_id = $row['product_id'];
            $product_quantity = $row['product_quantity'];

            // Query to fetch current product quantity from the products table
            $fetch_quantity_query = "SELECT product_quantity FROM products WHERE product_id = $product_id";
            $fetch_quantity_result = mysqli_query($connection, $fetch_quantity_query);
            $current_quantity_row = mysqli_fetch_assoc($fetch_quantity_result);
            $current_quantity = $current_quantity_row['product_quantity'];

            // Calculate updated quantity
            $updated_quantity = $current_quantity - $product_quantity;

            // Update the product quantity in the products table
            $update_product_query = "UPDATE products SET product_quantity = $updated_quantity WHERE product_id = $product_id";
            mysqli_query($connection, $update_product_query);
        }

        header('location: account.php');
    } else {
        // Handle file upload error
        echo "Sorry, there was an error uploading your file.";
    }
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
                        <img src="<?php echo $rows['logo']; ?>" width="125px">
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


<!-- Payment -->
    <div class="container py-5">
    <h1 class="text-center"><b>Proof of Payment</b></h1>
                <hr class="border-3 border-dark mx-auto opacity-100" style="width:70px">
<section class="h-100 gradient-custom">
  <div class="container py-5 h-100">
    <div class="row d-flex justify-content-center align-items-center h-100">
      <div class="col-lg-10 col-xl-8">
        <div class="card" style="border-radius: 10px; border-bottom-left-radius: 10px; border-bottom-right-radius: 10px;">
          <div class="card-header px-4 py-5">           
          </div>
          <div class="card-body p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">              
            </div>
            <form action="payment.php" method="POST" enctype="multipart/form-data">
                <?php if(isset($_POST['order_status']) && $_POST['order_status'] == 'Not Paid'){ ?>
                    <?php $order_id = $_POST['order_id']; ?>
                    <input type="hidden" name="order_id" value="<?php echo $order_id; ?>">
            <!-- Item Details-->
            <div class="card shadow-0 border mb-4">
              <div class="card-body">
                <div class="row">
                  <div class="col-md-6 mb-4 pb-2">
                  <div class="imgholder">                        
                        <img id="previewImage" src="" alt="" width="300" height="300" class="img">
                        <label for="uploadimg" class="upload">
                            <input type="file" name="proof_of_payment" id="uploadimg" class="picture" onchange="previewFile()">
                            <i class="fa-solid fa-plus"></i>
                        </label>
                    </div>


                  </div>
                  </div>            
                <!-- Break Point -->
                <hr class="mb-4" style="background-color: #fafafa; opacity: 1;"> 
                <h5 class="d-flex align-items-center justify-content-end text-black text-uppercase mb-0">Total
              Payment: <span class="h2 mb-0 ms-2">₱<?php echo $_POST['order_total_price']; ?></span></h5>
              </div>
            </div>                      
          </div>
          <center><button class="btn btn-dark btn-lg btn-block" type="submit" name="proceed_btn">Proceed</button></center>

          <?php }else if(isset($_SESSION['total']) && $_SESSION['total'] != 0) { ?>
                    <?php $order_id = $_SESSION['order_id']; ?>
                    <input type="hidden" name="order_id" value="<?php echo $order_id; ?>">
            <!-- Item Details-->
            <div class="card shadow-0 border mb-4">
              <div class="card-body">
                <div class="row">
                  <div class="col-md-6 mb-4 pb-2">
                  <div class="imgholder">                        
                        <img id="previewImage" src="" alt="" width="300" height="300" class="img">
                        <label for="uploadimg" class="upload">
                            <input type="file" name="proof_of_payment" id="uploadimg" class="picture" onchange="previewFile()">
                            <i class="fa-solid fa-plus"></i>
                        </label>
                    </div>


                  </div>
                  </div>            
                <!-- Break Point -->
                <h5 class="d-flex align-items-center justify-content-end text-black text-uppercase mb-0">Total
              Payment: <span class="h2 mb-0 ms-2">₱<?php echo $_SESSION['total']; ?></span></h5>
              </div>
            <center><button class="btn btn-dark btn-lg btn-block" type="submit" name="proceed_btn">Proceed</button></center>
      <?php } ?>
        </div>
        </div>
    </div>
    </div>
    </div>

  </div>

</section>

 <footer class="my-5 pt-5 text-muted text-center text-small">
    <p class="mb-1">&copy; 2017-2019 Company Name</p>
  </footer>

<script>
    function previewFile() {
    const preview = document.getElementById('previewImage');
        const file = document.querySelector('input[type=file]').files[0];
        const reader = new FileReader();

        reader.onloadend = function () {
            preview.src = reader.result;
        };

        if (file) {
            reader.readAsDataURL(file);
        } else {
            preview.src = "";
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
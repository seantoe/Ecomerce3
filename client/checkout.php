<?php
session_start();
require '../config/connection.php';

// Check if the user is not logged in
if (!isset($_SESSION['id'])) {
    header('location: ../login.php');
    exit(); // Stop further execution
    }

if (!empty($_SESSION['cart'])){

    //let user in


    //send user to homepage
}else{

    header('location:homepage.php');

}

$id = $_SESSION['id'];
$userdisplay = "SELECT * FROM users WHERE id=$id";
$query = mysqli_query($connection, $userdisplay);


$sqldisplayweb = "SELECT * FROM settings";
$query2 = mysqli_query($connection, $sqldisplayweb);

$select="SELECT * FROM settings";
$result=mysqli_query($connection,$select);
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

<!-- checkout -->
<!-- Content -->
    <div class="container py-5">
<section class="h-100 gradient-custom">
  <div class="container py-5 h-100">
    <div class="row d-flex justify-content-center align-items-center h-100">
      <div class="col-lg-10 col-xl-8">
        <div class="shadow card" style="border-radius: 10px; border-bottom-left-radius: 0px; border-bottom-right-radius: 0px;">
          <div class="card-header px-4 py-5">   
           <h1 class="text-center"><b>Checkout</b></h1>        
          </div>
          <div class="card-body p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">              
            </div>
            <form action="../config/place_order.php" method="POST">
              <?php while($rows = mysqli_fetch_array($query)){ ?>
                <div class="row">    
                  <div class="col-md-8 order-md-1">
                    <h4 class="mb-3"></h4>
                    <form class="needs-validation" novalidate>
                      <div class="row">
                        <div class="col-md-6 mb-3">
                          <label for="firstName">First name</label>
                          <input type="text" name="firstname" class="form-control" id="firstName" value="<?php echo $rows['firstname']; ?>" required>
                          <div class="invalid-feedback">
                            First name is required.
                          </div>
                        </div>
                        <div class="col-md-6 mb-3">
                          <label for="lastName">Last name</label>
                          <input type="text" name="lastname" class="form-control" id="lastName" value="<?php echo $rows['lastname']; ?>" required>
                          <div class="invalid-feedback">
                            Last name is required.
                          </div>
                        </div>
                      </div>

                      <div class="mb-3">
                        <label for="username">Email</label>
                        <div class="input-group">
                          <div class="input-group-prepend">
                            <span class="input-group-text">@</span>
                          </div>
                          <input type="text" name="email" class="form-control" id="username" value="<?php echo $rows['email']; ?>" required>
                          <div class="invalid-feedback" style="width: 100%;">
                            Your Email is required.
                          </div>
                        </div>
                      </div>

                      <div class="mb-3">
                        <label for="address">Address</label>
                        <input type="text" name="address" class="form-control" id="address" value="<?php echo $rows['address']; ?>" required>
                        <div class="invalid-feedback">
                          Please enter your address.
                        </div>
                      </div>

                      <div class="mb-3">
                        <label for="address2">Contact</label>
                        <input type="text" name="contact" class="form-control" id="address2" value="<?php echo $rows['contact']; ?>">
                      </div>

                              
                      <hr class="mb-4">

                      <h4 class="mb-3">Total Amount:</h4>

                      <div class="d-block my-3">          
                          <label class="custom-control-label" for="paypal">₱<?php echo $_SESSION['total']; ?></label>
                        
                      </div>        
                      <hr class="mb-4">
                      <div class="border-0 px-4 py-5">
                          <h5 class="d-flex align-items-center justify-content-end text-white text-uppercase mb-0"><button class="btn btn-primary btn-lg btn-block" type="submit" name="place_order">Place Order</button></h5>
                        </div>
                    </form>
                  </div>
                </div>
              </div>
              </div>
               
              </div>
              </div>
              </div>
              </section>

               <footer class="my-5 pt-5 text-muted text-center text-small">
                  <p class="mb-1">Copyright &copy; Sample 2024 Developed By: </p>
                </footer>
            <?php } ?>
    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/2.0.0/js/dataTables.js"></script>
    <script src="https://cdn.datatables.net/2.0.0/js/dataTables.bootstrap5.js"></script>

<script type="logout.js"></script>
</body>
</html>
<?php
    session_start();
    require 'config/connection.php';

$sqldisplayweb = "SELECT * FROM settings";
$query = mysqli_query($connection, $sqldisplayweb);

$sqldisplayweb = "SELECT * FROM products";
$query2 = mysqli_query($connection, $sqldisplayweb);

$sqldisplayweb = "SELECT * FROM settings";
$query3 = mysqli_query($connection, $sqldisplayweb);
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Ecommerce Website</title>
        <link rel="stylesheet" href="css/style2.css">
        <link rel="stylesheet" href="css/client.css">
        
        
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
    <?php while($rows = mysqli_fetch_array($query)){ ?>
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
                        </ul>
                    </nav>
                    <a href="client/cart.php"><span class="material-symbols-outlined">
                        shopping_cart
                        </span></a>
                    </div>
                </div>
            </div>
    <?php } ?>

<!-- Content -->
<section style="background-color: #eee;">
  <div class="container py-5">
    <h1 class="text-center"><b>Products</b></h1>
    <hr class="border-3 border-dark mx-auto opacity-100" style="width:70px">
  </div>
  <div class="container py-1">
    <div class="row justify-content-center">
      <?php while ($row = mysqli_fetch_array($query2)) { ?>
        <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
          <div class="product-card">
            <div class="shadow card text-black">
              <i class="fab fa-apple fa-lg px-3"></i>
              <a href="client/singleproduct.php?product_id=<?php echo $row['product_id']; ?>">
                <?php
                  // Fetch the image path from the database
                  $imagePath = $row['product_image'];

                  // Construct the complete URL to the image
                  $imageURL = 'http://localhost/Ecomerce2/images/' . $imagePath;

                  // Display the image
                  echo '<img src="' . $imageURL . '" class="card-img-top" alt="Product Image">';
                ?>
              </a>
              <div class="card-body">            
                <div class="d-flex justify-content-between">
                  <span>Available Stock:</span><span><?php echo $row['product_quantity']; ?></span>
                </div>
                <div class="d-flex justify-content-between">
                  <span>Price</span><span>₱<?php echo $row['product_price']; ?></span>
                </div>
                <div class="d-flex justify-content-between">
                  <span>Category</span><span><?php echo $row['product_category']; ?></span>
                </div>              
              </div>
            </div>
          </div>
        </div>
      <?php } ?>
    </div>
  </div>
</section>


<?php while($rows = mysqli_fetch_array($query3)) { ?>
 <footer class="my-5 pt-5 text-muted text-center text-small">
    <div class='footer-col-1'>
        <p>Copyright © Sample 2024<br>
            Developed By:</p>
        <h4>Contact Us</h4>
            <?php echo $rows['contact1']; ?><br>
            <?php echo $rows['contact2']; ?>   
    </div>
  </footer>
<?php } ?>

    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/2.0.0/js/dataTables.js"></script>
    <script src="https://cdn.datatables.net/2.0.0/js/dataTables.bootstrap5.js"></script>

<script type="logout.js"></script>
</body>
</html>
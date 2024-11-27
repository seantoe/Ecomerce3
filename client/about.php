<?php
    session_start();
    require_once '../config/connection.php';

// Check if the user is not logged in
if (!isset($_SESSION['id'])) {
    header('location: ../login.php');
    exit(); // Stop further execution
    }

$id = $_SESSION['id'];
$query = "SELECT * FROM users WHERE id='$id'";
$sql = mysqli_query($connection, $query);

$sqldisplayweb = "SELECT * FROM settings";
$query = mysqli_query($connection, $sqldisplayweb);

$sqldisplayweb = "SELECT * FROM settings2";
$query2 = mysqli_query($connection, $sqldisplayweb);

$sqldisplayweb = "SELECT * FROM settings";
$query3 = mysqli_query($connection, $sqldisplayweb);

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
<?php while ($rows = mysqli_fetch_array($query2)) { ?>
<section style="background-color: #eee;">
  <div class="container py-5">
      <div class="container d-flex justify-content-center align-items-center">
        <div class="card bg-light p-3 m-0">
          <div class="row gy-3 gy-md-0 align-items-md-center">            
            <div class="col-md-11">
              <div class="card-body p-0">
                <h2 class="card-title h4 mb-3"><?php echo $rows['about1']; ?></h2>
                <p class="card-text lead"><?php echo $rows['about2']; ?></p>
                <br>
                <p class="card-text lead"><?php echo $rows['about3']; ?></p>
                <br>
                <p class="card-text lead"><?php echo $rows['about4']; ?></p>
              </div>
            </div>
          </div>
        </div>
      </div>

    

<div class="row p-3">
  <div class="col-lg-4 col-md-12 mb-4">
    <?php
        // Fetch the image path from the database
        $imagePath = $rows['image1'];

        // Construct the complete URL to the image
        $imageURL = 'http://localhost/Ecomerce2/images/' . $imagePath;

        // Display the image
        echo '<img src="' . $imageURL . '" class="img-fluid rounded-start mx-auto d-block" id="cardimg" alt="Image 1" style="max-width: 400px;">';
    ?>
  </div>

  <div class="col-lg-4 col-md-6 mb-4">
    <?php
        // Fetch the image path from the database
        $imagePath = $rows['image2'];

        // Construct the complete URL to the image
        $imageURL = 'http://localhost/Ecomerce2/images/' . $imagePath;

        // Display the image
        echo '<img src="' . $imageURL . '" class="img-fluid rounded-start mx-auto d-block" id="cardimg" alt="Image 1" style="max-width: 400px;">';
    ?>
  </div>

  <div class="col-lg-4 col-md-6 mb-4">
    <?php
        // Fetch the image path from the database
        $imagePath = $rows['image3'];

        // Construct the complete URL to the image
        $imageURL = 'http://localhost/Ecomerce2/images/' . $imagePath;

        // Display the image
        echo '<img src="' . $imageURL . '" class="img-fluid rounded-start mx-auto d-block" id="cardimg" alt="Image 1" style="max-width: 400px;">';
    ?>
  </div>
</div>

<div class="row p-3">
  <div class="col-lg-4 col-md-12 mb-4">
    <?php
        // Fetch the image path from the database
        $imagePath = $rows['image4'];

        // Construct the complete URL to the image
        $imageURL = 'http://localhost/Ecomerce2/images/' . $imagePath;

        // Display the image
        echo '<img src="' . $imageURL . '" class="img-fluid rounded-start mx-auto d-block" id="cardimg" alt="Image 1" style="max-width: 400px;">';
    ?>
  </div>

  <div class="col-lg-4 col-md-6 mb-4">
    <?php
        // Fetch the image path from the database
        $imagePath = $rows['image5'];

        // Construct the complete URL to the image
        $imageURL = 'http://localhost/Ecomerce2/images/' . $imagePath;

        // Display the image
        echo '<img src="' . $imageURL . '" class="img-fluid rounded-start mx-auto d-block" id="cardimg" alt="Image 1" style="max-width: 400px;">';
    ?>
  </div>

  <div class="col-lg-4 col-md-6 mb-4">
    <?php
        // Fetch the image path from the database
        $imagePath = $rows['image6'];

        // Construct the complete URL to the image
        $imageURL = 'http://localhost/Ecomerce2/images/' . $imagePath;

        // Display the image
        echo '<img src="' . $imageURL . '" class="img-fluid rounded-start mx-auto d-block" id="cardimg" alt="Image 1" style="max-width: 400px;">';
    ?>
  </div>
</div>
<?php } ?>

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
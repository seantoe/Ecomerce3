<?php 
session_start();
require '../config/connection.php';
	
// Check if the user is not logged in
if (!isset($_SESSION['id'])) {
    header('location: ../login.php');
    echo "<script>alert('You must log in first')</script>";
    exit(); // Stop further execution
    }

$select="SELECT * FROM settings";
$result=mysqli_query($connection,$select);

$select="SELECT * FROM settings";
$query2=mysqli_query($connection,$select);

if(isset($_GET['product_id'])){

    $product_id = $_GET['product_id'];

    $stmt = $connection->prepare("SELECT * FROM products WHERE product_id = ?");
    $stmt->bind_param("i", $product_id);

    $stmt->execute();

    $product = $stmt->get_result(); //[]

// no product was given
}else{

    header('location: homepage.php');
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
<div class="container my-5 d-flex justify-content-center">
    <div class="row">
        <?php while($row = $product->fetch_assoc()) { ?>
            <div class="col-md-5">
                <div class="main-img">
                        <?php
                            // Fetch the image path from the database
                            $imagePath = $row['product_image'];

                            // Construct the complete URL to the image
                            $imageURL = 'http://localhost/Ecomerce2/images/' . $imagePath;

                            // Display the image
                            echo '<img src="' . $imageURL . '" class="shadow img-fluid" height="650px" width="550px">';
                        ?>                 
                </div>
            </div>
            <div class="col-md-7">
                <div class="main-description px-2">                  
                    <div class="product-title text-bold my-3">
                        <?php echo $row['product_category']; ?><br>
                        <?php echo $row['product_name']; ?>
                    </div>

                    <div class="price-area my-4">
                        <p class="new-price text-bold mb-1">₱ <?php echo $row['product_price']; ?></p>                        
                    </div>

                    <form action="cart.php" method="POST">
                        <input type="hidden" name="product_id" value="<?php echo $row['product_id']; ?>">
                        <input type="hidden" name="product_image" value="<?php echo $row['product_image']; ?>">
                        <input type="hidden" name="product_name" value="<?php echo $row['product_name']; ?>">
                        <input type="hidden" name="product_price" value="<?php echo $row['product_price']; ?>">
                        <?php
                            // Assuming $product_id contains the ID of the product
                            $query = "SELECT product_quantity FROM products WHERE product_id = ?";
                            $stmt = $connection->prepare($query);
                            $stmt->bind_param('i', $product_id);
                            $stmt->execute();
                            $stmt->bind_result($available_quantity);
                            $stmt->fetch();
                            $stmt->close();
                        ?>
                        <div class="buttons d-flex my-5">                                      
                            <div class="block">
                                <input type="number" class="shadow form-control" id="cart_quantity" placeholder="Enter amount" name="product_quantity" min="0" max="<?php echo $available_quantity; ?>">
                                <?php if ($available_quantity == 0) { ?>
                                    <div class="block quantity">
                                        <button type='button' disabled class="shadow btn custom-btn">Add to cart</button><span class="stock-status">Out of stock</span>
                                    </div>
                                <?php } else { ?>
                                    <div class="block">
                                        <button type='submit' name='add_to_cart' class="shadow btn custom-btn">Add to cart</button>
                                        <span>&nbsp <?php echo $available_quantity; ?> stocks available</span>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                    </form>

                    <p class="text-secondary mb-1">Product Details</p>
                    <p class="text-secondary mb-1"><?php echo $row['product_description']; ?></p>
                </div>    
            </div>
        <?php } ?> 
    </div>
</div>

 
    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/2.0.0/js/dataTables.js"></script>
    <script src="https://cdn.datatables.net/2.0.0/js/dataTables.bootstrap5.js"></script>

<script type="logout.js"></script>
</body>
</html>
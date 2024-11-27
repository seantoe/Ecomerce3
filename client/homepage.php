<?php
session_start();
$page_title = 'Homepage';
require_once '../config/connection.php';

// Check if the user is not logged in
if (!isset($_SESSION['id'])) {
    header('location: login.php');
    exit(); // Stop further execution
    }

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
        <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">   
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.4.2/css/fontawesome.min.css">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
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
            <section style="background-color: #eee;">
            <div class="content">
                    <div class="row">
                        <div class="col-3"><h1><?php echo $rows['title']; ?><br></h1>
                            <p><?php echo $rows['description']; ?></p>
                            <a href="products.php" class="btn">Explore Now &#8594;</a>
                        </div>
                        <div class="col-2">
                            <img src="<?php echo $rows['image1']; ?>">
                        </div>
                    </div>
                </div>
            </section>
                <?php } ?>

<!--- Featured Products --->
    <div class="small-container">
        <h2 class="title">Feature Products</h2>
            <div class="row">
<?php include('../config/featured_products.php'); ?>
<?php while($row = $featured_products->fetch_assoc()) { ?>
                <div class="col-4">
                <?php
                            // Fetch the image path from the database
                            $imagePath = $row['product_image'];

                            // Construct the complete URL to the image
                            $imageURL = 'http://localhost/Ecomerce2/images/' . $imagePath;

                            // Display the image
                            echo '<img src="' . $imageURL . '" alt="Profile Picture">';
                        ?>
                    <h4><?php echo $row['product_name']; ?></h4>
                    <div class='rating'>
                        <i class='bi bi-star-fill'></i>
                        <i class='bi bi-star-fill'></i>
                        <i class='bi bi-star-fill'></i>
                        <i class='bi bi-star-fill'></i>
                        <i class='bi bi-star'></i>
                    </div>
                    <p>Price: P<?php echo $row['product_price']; ?></p>
                    <a href="<?php echo "singleproduct.php?product_id=". $row['product_id']; ?>"><button>View Product</button></a>
                    </div> 
<?php } ?>
            </div>
        </div>

<!--- offer ----->
    <div class="offer">
        <div class="small-container">
            <div class="row">
                <div class="col-2">
                    <img src="images/war.png" class="offer-img">
                </div>
                <div class="col-2">
                    <p>Examples text</p>
                    <h1>Name product</h1>
                    <small>Product descprtion</small>
                    <a href="products.php" class="btn">Buy Now &#8594;</a>
                </div>
            </div>
        </div>
    </div>

<!--- Footer -----> 
<?php
$select="SELECT * FROM settings";
    $result=mysqli_query($connection,$select);
    while($row=mysqli_fetch_assoc($result)){
        $contact1=$row['contact1'];
        $contact2=$row['contact2'];
        echo "
            <div class='footer'>
                <div class='container'>
                    <div class='row'>
                        <div class='footer-col-1'>
                            <p>Copyright © Sample 2024<br>
                                Developed By:</p>
                        </div>
                    <div class='footer-col-2'>
                        <h3>Contact Us</h3>
                         $contact1<br>
                         $contact2   
                    </div>
                </div>
            </div>
        </div>
        ";
    }
?>
<!----js toggle menu --->
<script type="text/javascript">
var MenuItems = document.getElementById("MenuItems");
MenuItems.style.maxHeight = "0px";
function menutoggle(){
    if(MenuItems.stle.maxHeight == "0px")
    {
        MenuItems.style.maxHeight = "200px";
    }
    else
    {
        MenuItems.style.maxHeight = "0px";
    }
}
</script>
</body>
</html>
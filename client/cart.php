<?php
session_start();
require '../config/connection.php';

// Check if the user is not logged in
if (!isset($_SESSION['id'])) {
    header('location: ../login.php');
    exit(); // Stop further execution
    }

$select="SELECT * FROM settings";
$result=mysqli_query($connection,$select);

$sqldisplayweb = "SELECT * FROM settings";
$query2 = mysqli_query($connection, $sqldisplayweb);

// Initialize total to 0 if it doesn't exist yet
if (!isset($_SESSION['total'])) {
    $_SESSION['total'] = 0;
}

// Initialize an empty cart array if it doesn't exist yet
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = array();
}

if (isset($_POST['add_to_cart'])){

	//if user has already added a product to cart
	if(isset($_SESSION['cart'])){

		$products_array_ids = array_column($_SESSION['cart'], "product_id"); // [2,3,4]
		//if product has already been added to cart or not
		if(!in_array($_POST['product_id'], $products_array_ids)){

			$product_id = $_POST['product_id'];

			$product_array = array(
				'product_id' => $_POST['product_id'],
				'product_name' => $_POST['product_name'],
				'product_price' => $_POST['product_price'],
				'product_image' => $_POST['product_image'],
				'product_quantity' => $_POST['product_quantity']
			);

			$_SESSION['cart'][$product_id] = $product_array;


		//product has already been added
		}else{

			echo '<script>alert("Product was already added to cart")</script>';

		}


		//if this is the first product
	}else{

		$product_id = $_POST['product_id'];
		$product_name = $_POST['product_name'];
		$product_price = $_POST['product_price'];
		$product_image = $_POST['product_image'];
		$product_quantity = $_POST['product_quantity'];

		$product_array = array(
			'product_id' => $product_id,
			'product_name' => $product_name,
			'product_price' => $product_price,
			'product_image' => $product_image,
			'product_quantity' => $product_quantity
		);

		$_SESSION['cart'][$product_id] = $product_array;
		//[ 2=>[] , 3=>[], 5=>[]  ]


	}

	//calculate total
	calculateTotalcart();



//remove product from the cart
}else if(isset($_POST['remove_product'])) {

	$product_id = $_POST['product_id'];
	unset($_SESSION['cart'][$product_id]);

	//calculate total
	calculateTotalcart();


}else if(isset($_POST['edit_quantity'])){

	// we get id and quantity from the form
	$product_id = $_POST['product_id'];
	$product_quantity = $_POST['product_quantity'];

	//get the product array from the session
	$product_array = $_SESSION['cart'][$product_id];

	//update product quantity
	$product_array['product_quantity'] = $product_quantity;

	//return array back to its place
	$_SESSION['cart'][$product_id] = $product_array;


	//calculate total
	calculateTotalcart();



}else{
	//header('location: homepage.php');
}


function calculateTotalCart(){

	$total = 0;

	foreach($_SESSION['cart'] as $key => $value){

		$product = $_SESSION['cart'][$key];

		$product_price = $product['product_price'];
		$product_quantity = $product['product_quantity'];

		$total = $total + ($product_price * $product_quantity);
	}

	$_SESSION['total'] = $total;
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
<!-- Content -->
<section class="h-100 h-custom">
    <div class="container py-5 h-100">
        <div class="row d-flex justify-content-center align-items-center h-100">
            <div class="col-12">
                <div class="card card-registration card-registration-2" style="border-radius: 15px;">
                    <div class="card-body p-0">
                        <div class="row g-0">
                            <div class="col-lg-8">
                                <div class="p-5">
                                    <div class="d-flex justify-content-between align-items-center mb-5">
                                        <h1 class="fw-bold mb-0 text-black">Shopping Cart</h1>
                                        <h6 class="mb-0 text-muted"><?php echo count($_SESSION['cart']); ?> item(s)</h6>
                                    </div>
                                    <hr class="my-4">
                                    <?php foreach($_SESSION['cart'] as $key => $value){ ?>
                                        <div class="row mb-4 d-flex justify-content-between align-items-center">
                                            <div class="col-md-2 col-lg-2 col-xl-2">
                                                <img src="<?php echo $value['product_image']; ?>" class="img-fluid rounded-3" alt="Product Image">
                                            </div>
                                            <div class="col-md-3 col-lg-3 col-xl-3">
                                                <h6 class="text-muted">GreatEs</h6>
                                                <h6 class="text-black mb-0"><?php echo $value['product_name']; ?></h6>
                                            </div>
                                            <div class="col-md-3 col-lg-3 col-xl-2 d-flex">
                                                <form action="cart.php" method="POST">
                                                    <input type="hidden" name="product_id" value="<?php echo $value['product_id']; ?>">
                                                    <input type="hidden" name="product_image" value="<?php echo $value['product_image']; ?>">
                                                    <input type="hidden" name="product_name" value="<?php echo $value['product_name']; ?>">
                                                    <input type="hidden" name="product_price" value="<?php echo $value['product_price']; ?>">
                                                    <!-- Assuming $product_id contains the ID of the product -->
                                                    <?php
                                                    $query = "SELECT product_quantity FROM products WHERE product_id = ?";
                                                    $stmt = $connection->prepare($query);
                                                    $stmt->bind_param('i', $value['product_id']);
                                                    $stmt->execute();
                                                    $stmt->bind_result($available_quantity);
                                                    $stmt->fetch();
                                                    $stmt->close();
                                                    ?>
                                                    <input id="form1" min="1" max="<?php echo $available_quantity; ?>" name="product_quantity" type="number"
                                                           class="form-control form-control-sm" value="<?php echo $value['product_quantity']; ?>"/>
                                            </div>
                                            </form>
                                            <div class="col-md-3 col-lg-2 col-xl-2 offset-lg-1">
                                                <h6 class="mb-0"><?php echo $value['product_price']; ?></h6>
                                            </div>
                                            <div class="col-md-1 col-lg-1 col-xl-1 text-end">
                                                <form action="cart.php" method="POST">
                                                    <input type="hidden" name="product_id" value="<?php echo $value['product_id'] ?>">
                                                    <button type="submit" name="remove_product" class="btn btn-link text-muted">
                                                        <span class="material-symbols-outlined">close</span>
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                        <hr class="my-4">
                                    <?php } ?>
                                </div>
                            </div>
                            <div class="col-lg-4 bg-grey">
                                <div class="p-5">
                                    <h3 class="fw-bold mb-5 mt-2 pt-1">Summary</h3>
                                    <hr class="my-4">
                                    <?php foreach($_SESSION['cart'] as $key => $value){ ?>
                                        <div class="d-flex justify-content-between mb-4">
                                            <h5 class="text-uppercase"><?php echo $value['product_name']; ?></h5>
                                            <h5><?php echo $value['product_quantity'] * $value['product_price']; ?></h5>
                                        </div>
                                    <?php } ?>
                                    <hr class="my-4">
                                    <div class="d-flex justify-content-between mb-5">
                                        <h5 class="text-uppercase">Total price</h5>
                                        <?php if(isset($_SESSION['total']) && $_SESSION['total'] != 0) { ?>
                                            <h5><?php echo $_SESSION['total']; ?></h5>
                                        <?php } ?>
                                    </div>
                                    <form action="checkout.php" method="POST">
                                        <button type="submit" name="checkout" class="btn btn-dark btn-block btn-lg" data-mdb-ripple-color="dark">Checkout</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>


 
    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/2.0.0/js/dataTables.js"></script>
    <script src="https://cdn.datatables.net/2.0.0/js/dataTables.bootstrap5.js"></script>

<script type="logout.js"></script>
</body>
</html>
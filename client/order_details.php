<?php
session_start();
require '../config/connection.php';

// Check if the user is not logged in
if (!isset($_SESSION['id'])) {
    header('location: ../login.php');
    exit(); // Stop further execution
}

$select = "SELECT * FROM settings";
$result = mysqli_query($connection, $select);

$sqldisplayweb = "SELECT * FROM settings";
$query2 = mysqli_query($connection, $sqldisplayweb);

if (isset($_POST['order_details_btn']) && isset($_POST['order_id'])) {

    $order_id = $_POST['order_id'];
    $order_status = $_POST['order_status'];

    $stmt = $connection->prepare("SELECT oi.*, p.product_quantity FROM order_items oi JOIN products p ON oi.product_id = p.product_id WHERE oi.order_id = ?");

    $stmt->bind_param('i', $order_id);

    $stmt->execute();

    $order_details = $stmt->get_result();

    $order_total_price = 0;

    // Flag to check if any product is out of stock
    $isAnyProductOutOfStock = false;

    foreach ($order_details as $row) {
        $order_total_price += $row['product_price'] * $row['product_quantity'];
        
        // Check if any product is out of stock
        if ($row['product_quantity'] <= 0) {
            $isAnyProductOutOfStock = true;
        }
    }

} else {

    header('location: account.php');
    exit();
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
    <!--- Header ----->
    <?php while ($rows = mysqli_fetch_array($query2)) { ?>
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


    <!--- Orders Details -->
    <div class="container py-5">
        <h1 class="text-center"><b>Order Details</b></h1>
        <hr class="border-3 border-dark mx-auto opacity-100" style="width:70px">
        <section class="h-100 gradient-custom">
            <div class="container py-5 h-100">
                <div class="row d-flex justify-content-center align-items-center h-100">
                    <div class="col-lg-10 col-xl-8">
                        <div class="card" style="border-radius: 10px; border-bottom-left-radius: 0px; border-bottom-right-radius: 0px;">
                            <div class="card-header px-4 py-5">
                            </div>
                            <div class="card-body p-4">
                                <div class="d-flex justify-content-between align-items-center mb-4">
                                </div>
                                <?php foreach ($order_details as $row) {
                                    if ($row['product_quantity'] > 0) { ?>
                                <!-- Item Details-->
                                <div class="card shadow-0 border mb-4">
                                    <div class="card-body">
                                                <div class="row">
                                                    <div class="col-md-2">
                                                        <img src="<?php echo $row['product_image']; ?>" class="img-fluid rounded-3" alt="Product Image">
                                                    </div>
                                                    <div class="col-md-2 text-center d-flex justify-content-center align-items-center">
                                                        <p class="text-muted mb-0"><?php echo $row['product_name']; ?></p>
                                                    </div>

                                                    <div class="col-md-2 text-center d-flex justify-content-center align-items-center">
                                                        <p class="text-muted mb-0 small">Qty: <?php echo $row['product_quantity']; ?></p>
                                                    </div>
                                                    <div class="col-md-2 text-center d-flex justify-content-center align-items-center">
                                                        <p class="text-muted mb-0 small">₱<?php echo $row['product_price']; ?></p>
                                                    </div>
                                                </div>
                                                <!-- Break Point -->
                                                <hr class="mb-4" style="background-color: #e0e0e0; opacity: 1;">
                                    </div>
                                </div>
                                <?php }
                                    } ?>
                                <?php if ($order_status == "Not Paid" && !$isAnyProductOutOfStock) { ?>
                                    <form action="payment.php" method="POST">
                                        <input type="hidden" name="order_id" value="<?php echo $order_id; ?>">
                                        <input type="hidden" name="order_total_price" value="<?php echo $order_total_price; ?>">
                                        <input type="hidden" name="order_status" value="<?php echo $order_status; ?>">
                                        <center><button class="btn btn-primary btn-lg btn-block" type="submit" name="order_pay_btn">Send Proof of Payment</button></center>
                                    </form>
                                <?php } else if ($isAnyProductOutOfStock) { ?>
                                <!-- Item Details-->
                                <div class="card shadow-0 border mb-4">
                                    <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-2">
                                            <img src="<?php echo $row['product_image']; ?>" class="img-fluid rounded-3" alt="Product Image">
                                        </div>
                                        <div class="col-md-2 text-center d-flex justify-content-center align-items-center">
                                            <p class="text-muted mb-0"><?php echo $row['product_name']; ?></p>
                                        </div>

                                        <div class="col-md-2 text-center d-flex justify-content-center align-items-center">
                                            <p class="text-muted mb-0 small">Qty: <?php echo $row['product_quantity']; ?></p>
                                        </div>
                                        <div class="col-md-2 text-center d-flex justify-content-center align-items-center">
                                            <p class="text-muted mb-0 small">₱<?php echo $row['product_price']; ?></p>
                                        </div>
                                    </div>
                                    </div>
                                </div>
                                    <!-- Break Point -->
                                    <hr class="mb-4" style="background-color: #e0e0e0; opacity: 1;">

                                    <div class="alert alert-warning" role="alert">
                                        Product is out of stock, you can't pay as of now. Contact support to proceed.
                                    </div>
                                    <a href="account.php" class="btn btn-primary">Go Back</a>
                                <?php } ?>
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

    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/2.0.0/js/dataTables.js"></script>
    <script src="https://cdn.datatables.net/2.0.0/js/dataTables.bootstrap5.js"></script>

    <script type="logout.js"></script>
</body>

</html>

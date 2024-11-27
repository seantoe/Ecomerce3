<?php
session_start();
require '../config/connection.php';

// Check if the user is not logged in
if (!isset($_SESSION['id'])) {
    header('location: login.php');
    exit(); // Stop further execution
}

$id = $_SESSION['id'];
$query = "SELECT * FROM users WHERE id='$id'";
$nav = mysqli_query($connection, $query);

// Check if $_GET['id'] is set before using it
$user_id = isset($_GET['id']) ? $_GET['id'] : null;

// Fetch user details
$query_user = "SELECT * FROM users WHERE id = $user_id";
$result_user = mysqli_query($connection, $query_user);
$user = mysqli_fetch_assoc($result_user);

// Fetch order items for the selected user
$query_order_items = "SELECT * FROM order_items WHERE user_id = $user_id";
$result_order_items = mysqli_query($connection, $query_order_items);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Order Items</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h1 class="text-center">Order Items for <?php echo $user['firstname'] . " " . $user['lastname']; ?></h1>
        <table class="table mt-3">
            <thead>
                <tr>
                    <th>Product Name</th>
                    <th>Quantity</th>
                    <!-- Add more columns if needed -->
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($result_order_items)) : ?>
                    <tr>
                        <td><?php echo $row['product_name']; ?></td>
                        <td><?php echo $row['product_quantity']; ?></td>
                        <!-- Add more cells if needed -->
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        <a href="reports_view.php" class="btn btn-primary">Go Back</a>
    </div>
</body>
</html>

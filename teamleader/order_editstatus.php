<?php
session_start();
require '../config/connection.php';

$id = $_SESSION['id'];
$query = "SELECT * FROM users WHERE id='$id'";
$nav = mysqli_query($connection, $query);

// Check if the form is submitted
if (isset($_POST['updateForm'])) {
    // Get the user ID from session
    $id = $_SESSION['id'];

    // Get the order ID and order status from the form
    $order_id = isset($_POST['order_id']) ? mysqli_real_escape_string($connection, $_POST['order_id']) : '';
    $order_status = isset($_POST['order_status']) ? htmlspecialchars($_POST['order_status']) : '';

    echo "Order ID: " . $order_id . "<br>";
    echo "Order Status: " . $order_status . "<br>";

    // Check if order ID and order status are not empty
    if (!empty($order_id) && !empty($order_status)) {
        // Update the order status in the database
        $updateQuery = "UPDATE orders SET order_status = ? WHERE order_id = ?";
        $updateStmt = mysqli_prepare($connection, $updateQuery);

        if ($updateStmt) {
            mysqli_stmt_bind_param($updateStmt, 'si', $order_status, $order_id);

            if (mysqli_stmt_execute($updateStmt)) {
                // Redirect back to order.php after successful update
                header('location: order.php');
                exit();
            } else {
                // Handle the error if update fails
                echo "Update failed: " . mysqli_error($connection);
            }
            mysqli_stmt_close($updateStmt);
        } else {
            // Handle prepare statement error
            echo "Prepare statement error: " . mysqli_error($connection);
        }
    } else {
        // Handle missing data error
        echo "Order ID and order status are required.";
    }
} else {
    // Handle form submission error
    echo "Form submission error.";
}

// Close the database connection
mysqli_close($connection);
?>
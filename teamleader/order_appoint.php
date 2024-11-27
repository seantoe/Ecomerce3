<?php
session_start();
require_once '..\config\connection.php';

// Check if the user is not logged in
if (!isset($_SESSION['id'])) {
    header('location: login.php');
    exit(); // Stop further execution
    }

$id = $_SESSION['id'];
$query = "SELECT * FROM users WHERE id='$id'";
$nav = mysqli_query($connection, $query);

$query = "SELECT * FROM settings";
$settings = mysqli_query($connection, $query);

$query = "SELECT * FROM orders";
$result = mysqli_query($connection, $query);

if (isset($_POST['appoint'])) {
    $order_id = $_POST['order_id'];
    $appointed_to = $_POST['appointed_to'];

    // Retrieve the selected user's name from the database
    $query = "SELECT id FROM users WHERE id = ?";
    $stmt = mysqli_prepare($connection, $query);
    mysqli_stmt_bind_param($stmt, 'i', $appointed_to);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $user = mysqli_fetch_assoc($result);
    if ($user) {
        $id_appointed = $user['id'];

        // Retrieve the selected user's name from the database
        $query_name = "SELECT firstname, lastname FROM users WHERE id = ?";
        $stmt_name = mysqli_prepare($connection, $query_name);
        mysqli_stmt_bind_param($stmt_name, 'i', $appointed_to);
        mysqli_stmt_execute($stmt_name);
        $result_name = mysqli_stmt_get_result($stmt_name);
        $user_name = mysqli_fetch_assoc($result_name);
        if ($user_name) {
            // If user is found, construct the full name of the appointed user
            $appointed_to_name = $user_name['firstname'] . " " . $user_name['lastname'];

            // Update the orders table to appoint the user to the order
            $updateQuery = "UPDATE orders SET appointed_to = ?, id_appoint = ? WHERE order_id = ?";
            $updateStmt = mysqli_prepare($connection, $updateQuery);
            mysqli_stmt_bind_param($updateStmt, 'sii', $appointed_to_name, $id_appointed, $order_id);

            if (mysqli_stmt_execute($updateStmt)) {
                // Update the order_items table to reflect the appointed user
                $updateOrderItemsQuery = "UPDATE order_items SET appointed_to = ?, id_appoint = ? WHERE order_id = ?";
                $updateOrderItemsStmt = mysqli_prepare($connection, $updateOrderItemsQuery);
                mysqli_stmt_bind_param($updateOrderItemsStmt, 'sii', $appointed_to_name, $id_appointed, $order_id);

                if (mysqli_stmt_execute($updateOrderItemsStmt)) {
                    header('location: order.php');
                    exit();
                } else {
                    // Handle the error
                    echo "Update failed for order_items: " . mysqli_error($connection);
                }
                mysqli_stmt_close($updateOrderItemsStmt);
            } else {
                // Handle the error
                echo "Update failed for orders: " . mysqli_error($connection);
            }
        }
    }
    mysqli_stmt_close($stmt);
}

mysqli_close($connection);
?>

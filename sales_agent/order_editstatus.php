<?php
session_start();
require '../config/connection.php';

$id = $_SESSION['id'];
$query = "SELECT * FROM users WHERE id='$id'";
$nav = mysqli_query($connection, $query);

$order_id = $_GET['order_id'];
$selectQuery = "SELECT * FROM orders WHERE order_id=?";
$stmt = mysqli_prepare($connection, $selectQuery);
mysqli_stmt_bind_param($stmt, 'i', $order_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (isset($_POST['updateForm'])) {
        $order_id = $_POST['order_id'];
        $order_status = htmlspecialchars($_POST['order_status']);

    $updateQuery = "UPDATE orders SET order_status= ? WHERE order_id='$order_id' ";

    $updateStmt = mysqli_prepare($connection, $updateQuery);
    mysqli_stmt_bind_param($updateStmt, 's', $order_status);
    if (mysqli_stmt_execute($updateStmt)) {
        header('location: order.php');
        exit();
    } else {
        // Handle the error, e.g., display an error message or log it
        echo "Update failed: " . mysqli_error($connection);
    }
    mysqli_stmt_close($updateStmt);
}

mysqli_close($connection);
?>
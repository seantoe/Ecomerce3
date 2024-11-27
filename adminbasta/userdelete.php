<?php 
require_once '..\config\connection.php';

$id = $_GET['id'];

// Delete order items related to the user
$sql_delete_order_items = "DELETE FROM order_items WHERE user_id = '$id'";
$result_order_items = mysqli_query($connection, $sql_delete_order_items);

// Delete orders related to the user
$sql_delete_orders = "DELETE FROM orders WHERE user_id = '$id'";
$result_orders = mysqli_query($connection, $sql_delete_orders);

// Delete the user
$sql_delete_user = "DELETE FROM users WHERE id = '$id'";
$result_user = mysqli_query($connection, $sql_delete_user);

if($result_user === TRUE){
	header('location: usermanage.php');
	exit();
} else {
    // Handle error if the user deletion fails
}
?>
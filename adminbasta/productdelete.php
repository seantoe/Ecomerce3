<?php
	require_once '..\config\connection.php';
	
	$id = $_GET['product_id'];
	
	$sql = "DELETE FROM products WHERE product_id='$id'";	
	$result = mysqli_query($connection, $sql);
	
	if ($result == TRUE) 
		{
			header('location: product.php');
			exit();
		}
?>
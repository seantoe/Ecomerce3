<?php 
require_once '..\config\connection.php';

$id = $_GET ['id'];
$sql_delete = "DELETE FROM users WHERE id = '$id'";
$result = mysqli_query($connection, $sql_delete);

if($result == TRUE){
	header('location: usermanage.php');
	exit();
}
?>
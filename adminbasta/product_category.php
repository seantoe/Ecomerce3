<?php 
session_start();
require_once '../config/connection.php';

$id = $_SESSION['id'];
$query = "SELECT * FROM users WHERE id='$id'";
$nav = mysqli_query($connection, $query);

if(isset($_POST['addBtn'])){
    // Get the category name from the form
    $category = $_POST['product_category'];

    // Prepare and execute the SQL query to insert the category into the database
    $insert_query = "INSERT INTO products_category (product_category) VALUES (?)";
    $stmt = mysqli_prepare($connection, $insert_query);

    // Get the category name from the form
    $category = $_POST['product_category'];

    mysqli_stmt_bind_param($stmt, "s", $category);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    header("Location: product.php");
}
?>
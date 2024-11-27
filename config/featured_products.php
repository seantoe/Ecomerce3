<?php

include('connection.php');

$stmt = $connection->prepare("SELECT * FROM products");

$stmt->execute();

$featured_products = $stmt->get_result();

?>
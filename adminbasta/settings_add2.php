<?php
session_start();
require_once '../config/connection.php';

// Check if the form is submitted
if (isset($_POST['submitBtn'])) {
    // Retrieve form data
    $about1 = $_POST['about1'];
    $about2 = $_POST['about2'];
    $about3 = $_POST['about3'];
    $about4 = $_POST['about4'];

    // Define the target directory for file uploads
    $targetDirectory = '../images/';

    // Define the file paths
    $image1 = $targetDirectory . basename($_FILES['image1']['name']);
    $image2 = $targetDirectory . basename($_FILES['image2']['name']);
    $image3 = $targetDirectory . basename($_FILES['image3']['name']);
    $image4 = $targetDirectory . basename($_FILES['image4']['name']);
    $image5 = $targetDirectory . basename($_FILES['image5']['name']);
    $image6 = $targetDirectory . basename($_FILES['image6']['name']);

    // Move the uploaded files to the target directory
    move_uploaded_file($_FILES['image1']['tmp_name'], $image1);
    move_uploaded_file($_FILES['image2']['tmp_name'], $image2);
    move_uploaded_file($_FILES['image3']['tmp_name'], $image3);
    move_uploaded_file($_FILES['image4']['tmp_name'], $image4);
    move_uploaded_file($_FILES['image5']['tmp_name'], $image5);
    move_uploaded_file($_FILES['image6']['tmp_name'], $image6);

    // Insert data into the database
    $query = "INSERT INTO settings2 (about1, about2, about3, about4, image1, image2, image3, image4, image5, image6) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($connection, $query);
    mysqli_stmt_bind_param($stmt, "ssssssssss", $about1, $about2, $about3, $about4, $image1, $image2, $image3, $image4, $image5, $image6);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    header("Location: settings.php");
    exit();
}
?>
<?php
session_start();
require_once '../config/connection.php';

// Check if the form is submitted
if (isset($_POST['submitBtn'])) {
    // Retrieve form data
    $title = $_POST['title'];
    $description = $_POST['description'];
    $contact1 = $_POST['contact1'];
    $contact2 = $_POST['contact2'];

    // Define the target directory for file uploads
    $targetDirectory = '../images/';

    // Define the file paths
    $logo = $targetDirectory . basename($_FILES['logo']['name']);
    $image1 = $targetDirectory . basename($_FILES['image1']['name']);
    $image2 = $targetDirectory . basename($_FILES['image2']['name']);

    // Move the uploaded files to the target directory
    move_uploaded_file($_FILES['logo']['tmp_name'], $logo);
    move_uploaded_file($_FILES['image1']['tmp_name'], $image1);
    move_uploaded_file($_FILES['image2']['tmp_name'], $image2);

    // Prepare the SQL statement
    $query = "INSERT INTO settings (title, description, logo, image1, image2, contact1, contact2) VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($connection, $query);

    // Check for errors in preparing the statement
    if (!$stmt) {
        die('Error: ' . mysqli_error($connection));
    }

    // Bind parameters and execute the statement
    mysqli_stmt_bind_param($stmt, "sssssss", $title, $description, $logo, $image1, $image2, $contact1, $contact2);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    // Redirect the user to the settings.php page after the operation is complete
    header("Location: settings.php");
    exit();
}
?>
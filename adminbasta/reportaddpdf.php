<?php
// Include the FPDF library
include('../fpdf/fpdf.php');
// Include the database connection
require('../config/connection.php');

// Set the timezone
date_default_timezone_set('Asia/Manila');

// Check if user ID is provided
if (isset($_POST['user_id'])) {
    $userId = $_POST['user_id'];

    // Fetch user's first name and last name based on user ID
    $queryUserName = "SELECT firstname, lastname FROM users WHERE id = $userId";
    $resultUserName = mysqli_query($connection, $queryUserName);
    $rowUserName = mysqli_fetch_assoc($resultUserName);
    $firstName = $rowUserName['firstname'];
    $lastName = $rowUserName['lastname'];

    // Add 's to user's name
    $userName = $firstName . ' ' . $lastName . "'s";

    // Create PDF object
    $pdf = new FPDF();
    $pdf->AddPage();

    // Set font
    $pdf->SetFont('Arial', 'B', 16);

    $pdf->Cell(0, 10, $userName . ' Team Report', 1, 1, 'C'); // Content with border

    // Set font
    $pdf->SetFont('Arial', '', 12);
    // Add current date and time
    $pdf->Cell(0, 10, 'Date: ' . date('Y-m-d H:i:s'), 0, 1, 'C'); // Add current date and time

    // Add a line break
    $pdf->Ln(10);

    // Output client details table
    $pdf->SetFont('Arial', '', 12);
    $pdf->Cell(0, 10, 'Client(s):', 0, 1, 'L');

    // Fetch client details from the database based on user ID
    $queryClient = "SELECT address FROM orders WHERE id_team = $userId GROUP BY address";
    $resultClient = mysqli_query($connection, $queryClient);
    if ($resultClient) {
        while ($row = mysqli_fetch_assoc($resultClient)) {
            $pdf->Cell(50, 10, $row['address'], 1, 0, 'C');
            $pdf->Ln();
        }
    } else {
        // If there is an error in the query, display the error message
        echo 'Error fetching client details: ' . mysqli_error($connection);
        exit; // Stop script execution
    }

    // Add a line break
    $pdf->Ln(10);

    $pdf->Cell(0, 10, 'Product(s):                         Quantity:', 0, 1, 'L');

    // Fetch product details from the database based on user ID
    $queryProduct = "SELECT product_name, SUM(product_quantity) AS total_quantity FROM order_items WHERE id_team = $userId GROUP BY product_name";
    $resultProduct = mysqli_query($connection, $queryProduct);
    while ($row = mysqli_fetch_assoc($resultProduct)) {
        $pdf->Cell(50, 10, $row['product_name'], 1, 0, 'C');
        $pdf->Cell(50, 10, $row['total_quantity'], 1, 0, 'C');
        $pdf->Ln();
    }

    // Add a line break
    $pdf->Ln(10);

    // Fetch total ordered quantity
    $queryTotalOrderedQuantity = "SELECT SUM(product_quantity) AS total_ordered_quantity FROM order_items WHERE id_team = $userId";
    $resultTotalOrderedQuantity = mysqli_query($connection, $queryTotalOrderedQuantity);
    $rowTotalOrderedQuantity = mysqli_fetch_assoc($resultTotalOrderedQuantity);
    $totalOrderedQuantity = $rowTotalOrderedQuantity['total_ordered_quantity'];

    // Output total ordered quantity
    $pdf->Cell(0, 10, 'Total Ordered Quantity: ' . $totalOrderedQuantity, 0, 1, 'L');

    // Fetch total price sold
    $queryTotalPrice = "SELECT SUM(order_cost) AS total_price_sold FROM orders WHERE id_team = $userId";
    $resultTotalPrice = mysqli_query($connection, $queryTotalPrice);
    $rowTotalPrice = mysqli_fetch_assoc($resultTotalPrice);
    $totalPriceSold = $rowTotalPrice['total_price_sold'];

    // Fetch total amount collected
    $queryTotalCollected = "SELECT SUM(order_cost) AS total_collected FROM orders WHERE id_team = $userId AND order_status ='Paid'";
    $resultTotalCollected = mysqli_query($connection, $queryTotalCollected);
    if ($resultTotalCollected) {
        $rowTotalCollected = mysqli_fetch_assoc($resultTotalCollected);
        $totalAmountCollected = $rowTotalCollected['total_collected'];
    } else {
        // If there is an error in the query, display the error message
        echo 'Error fetching total amount collected: ' . mysqli_error($connection);
        exit; // Stop script execution
    }

    // Calculate amount for collection
    $forCollection = $totalPriceSold - $totalAmountCollected;

    // Output total price sold
    $pdf->Cell(0, 10, 'Total Price Sold: ' . $totalPriceSold, 0, 1, 'L');

    // Output total amount collected
    $pdf->Cell(0, 10, 'Total Amount Collected: ' . $totalAmountCollected, 0, 1, 'L');

    // Output amount for collection
    $pdf->Cell(0, 10, 'For Collection: ' . $forCollection, 0, 1, 'L');

    // Output the PDF
    $pdf->Output();
} else {
    // If user ID is not provided, show an error message
    echo 'User ID is required';
}
?>

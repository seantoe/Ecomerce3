<?php
require_once '../config/connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['deleteBtn'])) {
    // Check if category_id is set and not empty
    if (isset($_POST['category_id']) && !empty($_POST['category_id'])) {
        $category_id = $_POST['category_id'];

        // Prepare and execute the SQL query to delete the category from the database
        $sql_delete_category = "DELETE FROM products_category WHERE id_category = ?";
        $stmt = mysqli_prepare($connection, $sql_delete_category);
        mysqli_stmt_bind_param($stmt, "i", $category_id);
        $success = mysqli_stmt_execute($stmt);
        
        // Check if deletion was successful
        if ($success) {
            // Category deleted successfully
            echo "Category deleted successfully!";
            // Redirect to the page where the delete action was performed
            header("Location: ".$_SERVER['HTTP_REFERER']);
            exit();
        } else {
            // Error deleting category
            echo "Error deleting category!";
        }

        // Close the prepared statement
        mysqli_stmt_close($stmt);
    } else {
        // Invalid category ID
        echo "Invalid category ID!";
    }
} else {
    // Invalid request method or delete button not clicked
    echo "Invalid request!";
}
?>
<?php 
require_once '../config/connection.php';

// Check if the ban button is clicked and if the required fields are set
if(isset($_POST['userId'])) {
    // Get the user ID from the form data
    $id = $_POST['userId'];

    // Retrieve the user's email from the database
    $query_email = "SELECT email FROM users WHERE id = '$id'";
    $result_email = mysqli_query($connection, $query_email);
    
    if ($result_email && mysqli_num_rows($result_email) > 0) {
        // Fetch the user's email
        $row_email = mysqli_fetch_assoc($result_email);
        $user_email = $row_email['email'];

        // Delete order items related to the user
        $sql_delete_order_items = "DELETE FROM order_items WHERE user_id = '$id'";
        $result_order_items = mysqli_query($connection, $sql_delete_order_items);

        // Delete orders related to the user
        $sql_delete_orders = "DELETE FROM orders WHERE user_id = '$id'";
        $result_orders = mysqli_query($connection, $sql_delete_orders);

        // Delete the user
        $sql_delete_user = "DELETE FROM users WHERE id = '$id'";
        $result_user = mysqli_query($connection, $sql_delete_user);

        // Insert the banned user's email into the users_ban table
        if ($result_user) {
            // Insert the email into the users_ban table
            $sql_insert_banned_user = "INSERT INTO users_ban (email) VALUES ('$user_email')";
            mysqli_query($connection, $sql_insert_banned_user);

            // Redirect back to the user management page
            header('location: usermanage.php');
            exit();
        } else {
            // Handle error if the user deletion fails
            echo "Error: Unable to ban user";
        }
    } else {
        // Handle error if user email is not found
        echo "Error: User email not found";
    }
} else {
    // Redirect back to the user management page if the ban button is not clicked
    header('location: usermanage.php');
    exit();
}
?>

<?php
// Include database connection
require_once '../config/connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if the banned user ID is set
    if (isset($_POST['bannedUserId'])) {
        $bannedUserId = $_POST['bannedUserId'];

        // SQL to delete the banned user from the users_ban table
        $delete_query = "DELETE FROM users_ban WHERE email = ?";

        // Prepare the SQL statement
        $stmt = $connection->prepare($delete_query);
        
        if ($stmt) {
            // Bind the parameter
            $stmt->bind_param('s', $bannedUserId);
            
            // Execute the statement
            if ($stmt->execute()) {
                // Unban successful
                // Redirect or display success message
                header('location: usermanage.php?unban_success=1');
                exit(); // Ensure script termination after redirection
            } else {
                // Unban failed
                // Redirect or display error message
                header('location: usermanage.php?error=Unban failed');
                exit(); // Ensure script termination after redirection
            }
        } else {
            // Unban failed due to SQL error
            // Redirect or display error message
            header('location: usermanage.php?error=SQL error');
            exit(); // Ensure script termination after redirection
        }
    } else {
        // Banned user ID is not set
        // Redirect or display error message
        header('location: usermanage.php?error=Banned user ID is not set');
        exit(); // Ensure script termination after redirection
    }
} else {
    // Invalid request method (not POST)
    // Redirect or display error message
    header('location: usermanage.php?error=Invalid request method');
    exit(); // Ensure script termination after redirection
}
?>
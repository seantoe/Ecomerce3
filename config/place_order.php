<?php
session_start();
require 'connection.php';

// Check if the user is not logged in
if (!isset($_SESSION['id'])) {
    header('location: ../login.php');
    exit(); // Stop further execution
}

if (isset($_POST['place_order'])) {

    //get user info and store it in database
    $email = $_POST['email'];
    $firstname = $_POST['firstname'];
    $lastname = $_POST['lastname'];
    $address = $_POST['address'];
    $contact = $_POST['contact'];
    $order_cost = $_SESSION['total'];
    $order_status = "Not Paid";
    $user_id = $_SESSION['id']; // getting the session user id
    $order_date = date('Y-m-d H:i:s');

    // Get the team leader's ID based on their address
    $stmt_team_leader = $connection->prepare("SELECT id, firstname, lastname FROM users WHERE address = ? AND access_level = 3");
    $stmt_team_leader->bind_param('s', $address);
    $stmt_team_leader->execute();
    $result_team_leader = $stmt_team_leader->get_result();

    if ($result_team_leader->num_rows > 0) {
        // Fetch the team leader's ID
        $team_leader_row = $result_team_leader->fetch_assoc();
        $id_team = $team_leader_row['id'];
        $id_appoint = $team_leader_row['id'];
        $appointed_to_firstname = $team_leader_row['firstname'];
        $appointed_to_lastname = $team_leader_row['lastname'];
        // Concatenate the firstname and lastname
        $appointed_to = $appointed_to_firstname . ' ' . $appointed_to_lastname;
    } else {
        // If no team leader found with the given address, set id_team to NULL or any default value as per your requirement
        $id_team = 0; // or any default value
        $id_appoint = 0;
        $appointed_to = null; // or any default value
    }


    // Store the team leader's ID along with other order information
	if ($appointed_to === null) {
	    $appointed_to = "No one appointed";
	}

	$stmt = $connection->prepare("INSERT INTO orders (firstname, lastname, address, contact, order_cost, order_status, user_id, order_date, id_team, id_appoint, appointed_to) VALUES (?,?,?,?,?,?,?,?,?,?,?)");

	if (!$stmt) {
	    die('Error preparing statement: ' . $connection->error);
	}

	$stmt->bind_param('ssssisisiis', $firstname, $lastname, $address, $contact, $order_cost, $order_status, $user_id, $order_date, $id_team, $id_appoint, $appointed_to);

	$stmt_status = $stmt->execute();

	if (!$stmt_status) {
	    die('Error executing statement: ' . $stmt->error);
	}

	if (!$stmt_status) {
	    die('Error executing statement: ' . $stmt->error);
	}

	$order_id = $stmt->insert_id;

    echo $order_id;

    // Update id_appoint and id_team in the users table
    $update_stmt = $connection->prepare("UPDATE users SET id_appoint = ?, id_team = ? WHERE id = ?");
    $update_stmt->bind_param('iii', $id_appoint, $id_team, $user_id);
    $update_stmt->execute();

    // Check if the user has 4 or more unpaid orders
    $stmt_unpaid_orders = $connection->prepare("SELECT COUNT(*) as num_unpaid_orders FROM orders WHERE user_id = ? AND order_status = 'Not Paid'");
    $stmt_unpaid_orders->bind_param('i', $user_id);
    $stmt_unpaid_orders->execute();
    $result_unpaid_orders = $stmt_unpaid_orders->get_result();
    $row_unpaid_orders = $result_unpaid_orders->fetch_assoc();
    $num_unpaid_orders = $row_unpaid_orders['num_unpaid_orders'];

    if ($num_unpaid_orders >= 3) {
    // User has 3 or more unpaid orders, update ban column to 1 and delete orders and order items
    $stmt_update_ban = $connection->prepare("UPDATE users SET ban = 1 WHERE id = ?");
    
    // Check if the statement was prepared successfully
    if (!$stmt_update_ban) {
        // Provide a warning to the user and log the error
        echo "Warning: Failed to prepare statement: " . $connection->error;
        error_log("Failed to prepare statement: " . $connection->error);
    } else {
        // Bind parameters and execute the statement
        $stmt_update_ban->bind_param('i', $user_id);
        $stmt_status = $stmt_update_ban->execute();

        // Check if the statement was executed successfully
        if (!$stmt_status) {
            // Provide a warning to the user and log the error
            echo "Warning: Failed to execute statement: " . $stmt_update_ban->error;
            error_log("Failed to execute statement: " . $stmt_update_ban->error);
        } else {
            // Delete orders and order items of the banned user
            $delete_orders_stmt = $connection->prepare("DELETE FROM orders WHERE user_id = ?");
            $delete_orders_stmt->bind_param('i', $user_id);
            $delete_orders_stmt->execute();

            $delete_order_items_stmt = $connection->prepare("DELETE FROM order_items WHERE user_id = ?");
            $delete_order_items_stmt->bind_param('i', $user_id);
            $delete_order_items_stmt->execute();

            // Destroy the session and redirect the user to the login page
            session_destroy();
            header('location: ../login.php?error=You are banned due to too many unpaid orders');
            exit(); // Stop further execution
        }
    }
}


    //get products from cart
    foreach ($_SESSION['cart'] as $key => $value) {

        $product = $_SESSION['cart'][$key]; // []
        $product_id = $product['product_id'];
        $product_name = $product['product_name'];
        $product_image = $product['product_image'];
        $product_price = $product['product_price'];
        $product_quantity = $product['product_quantity'];

        //store each single item in order_items database
        $stmt1 = $connection->prepare("INSERT INTO order_items (order_id, product_id, product_name, product_image, product_price, product_quantity, user_id, order_date, id_team, id_appoint, appointed_to) VALUES (?,?,?,?,?,?,?,?,?,?,?);");

        $stmt1->bind_param('iissiiisiis', $order_id, $product_id, $product_name, $product_image, $product_price, $product_quantity, $user_id, $order_date, $id_team, $id_appoint, $appointed_to);

        $stmt1->execute();
    }

    //remove everything from cart --> delay until payment is done
    unset($_SESSION['cart']);

    //inform user whether everything is fine or there is a problem
    header('location: ../client/account.php?order_status=Order Placed Successfully');
}

?>
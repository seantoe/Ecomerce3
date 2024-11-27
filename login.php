<?php
session_start();
require 'config\connection.php';

// Function to check if the user is banned
function isUserBanned($email, $connection) {
    $sql = "SELECT ban FROM users WHERE email=? LIMIT 1";
    $stmt = $connection->prepare($sql);
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    // Check if the query was successful
    if ($result === false) {
        // Handle the case where the query failed
        return false;
    }

    // Fetch the ban status from the result
    $row = $result->fetch_assoc();
    $ban = $row['ban'];

    // Free the result set
    $result->close();

    // Return the ban status
    return $ban;
}

if (isset($_POST['loginBTN'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Check if the user is banned
    $ban = isUserBanned($email, $connection);
    if ($ban === false) {
        // Handle the case where the query failed
        header('location: login.php?error=Error checking user ban status');
        exit(); // Stop further execution
    } elseif ($ban == 1) {
        // Handle the case where the user is banned
        header('location: login.php?error=This email is banned, Contact support');
        exit(); // Stop further execution
    }

    // Fetch user data after verifying that the user is not banned
    $sql = "SELECT * FROM users WHERE email=? LIMIT 1";
    $stmt = $connection->prepare($sql);
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    // Proceed with regular login if the email is not banned
    if (hash('md5', $password) == $user['password'] && $user['access_level'] == "1") {
        $_SESSION['id'] = $user['id'];
        $_SESSION['email'] = $user['email'];
        header('location: adminbasta/index.php');
        exit();
    } elseif (hash('md5', $password) == $user['password'] && $user['access_level'] == "3") {
        $_SESSION['id'] = $user['id'];
        $_SESSION['email'] = $user['email'];
        header('location: teamleader/index.php');
        exit();
    } elseif (hash('md5', $password) == $user['password'] && $user['access_level'] == "4") {
        $_SESSION['id'] = $user['id'];
        $_SESSION['email'] = $user['email'];
        header('location: sales_agent/index.php');
        exit();
    } elseif (hash('md5', $password) == $user['password'] && $user['access_level'] == "5") {
        $_SESSION['id'] = $user['id'];
        $_SESSION['email'] = $user['email'];
        header('location: client/homepage.php');
        exit();
    } else {
        header('location: login.php?error=Invalid Username / Password');
        exit();
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Amatic+SC:wght@700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/2.0.0/css/dataTables.bootstrap5.css">
    <link rel="stylesheet" href="assets\css\sign.css">

</head>
<body>
    <section class="h-100 bg-secondary">
        <div class="container py-5 h-100">
            <div class="row d-flex justify-content-center align-items-center h-100 ">
                <div class="col">
                    <div class="card card-registration my-4 custom-shadow ">
                        <div class="row g-0">
                            <div class="col-xl-6 d-none d-xl-block d-flex align-items-center justify-content-center">
                                <img src="images/vape.jpg" alt="Sample photo" class="img-fluid h-100"
                                    style="border-top-left-radius: .25rem; border-bottom-left-radius: .25rem;" />
                            </div>
                            <div class="col-xl-6">
                                <div class="card-body p-md-5 text-black">
                                    <div class="row">
                                        <h3 class="mb-5 text-uppercase">Login</h3>
                                        <p style="color:red"><?php if(isset($_GET['error'])){ echo $_GET['error']; } ?></p>
                                        <!-- Form starts here -->
                                        <form action="login.php" method="POST">
                                            <div class="mt-5">
                                                <div class="form-outline mt-5 mb-4">
                                                    <label class="form-label" for="form3Example90">Email</label>
                                                    <input type="text" name="email" id="email" class="form-control form-control-lg" required/>
                                                </div>
                                                <div class="form-outline mb-4">
                                                    <label class="form-label" for="form3Example90">Password</label>
                                                    <input type="password" name="password" id="password" class="form-control form-control-lg" required/>
                                                </div>
                                                <div class="d-flex justify-content-end pt-3">
                                                    <button type="submit" name="loginBTN"
                                                        class="btn btn-primary btn-lg ms-2 custom-shadow">Login</button>
                                                </div>
                                            </div>
                                        </form>
                                        <!-- Form ends here -->
                                    </div>
                                    <p class="text-center text-muted mt-5 mb-0">
                                        Don't have an account yet? <a href="signup.php"
                                            class="fw-bold text-body"><u>Sign up here</u></a>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/2.0.0/js/dataTables.js"></script>
    <script src="https://cdn.datatables.net/2.0.0/js/dataTables.bootstrap5.js"></script>
    <script src="./assets/js/script.js"></script>
    <script src="./assets/js/custom-admin-template.js"></script>
</body>
</html>
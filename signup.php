<?php
session_start();
require 'config\connection.php';

if(isset($_POST['signupBtn'])) {
    $firstname = $_POST['firstname'];
    $lastname = $_POST['lastname'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $address = $_POST['address'];
    $contact = $_POST['contact'];
    $accesslevel = "5";
    $position = "Client";

    // Check if a file is uploaded
    if(isset($_FILES['valid_id']) && $_FILES['valid_id']['error'] === UPLOAD_ERR_OK) {
        // Define the target directory for file uploads
        $targetDirectory = '../images/';

        // Define the file path
        $valid_id = $targetDirectory . basename($_FILES['valid_id']['name']);

        // Move the uploaded file to the target directory
        move_uploaded_file($_FILES['valid_id']['tmp_name'], $valid_id);
    } else {
        // Handle the case when no file is uploaded
        // For example, set a default value or show an error message
        $valid_id = ""; // Set a default value or handle the case accordingly
    }

    // Hash the password
    $password = hash('md5', $password);

    // Prepare the insert query
    $insertQuery = "INSERT INTO users (valid_id, firstname, lastname, email, password, address, contact, access_level, position) VALUES (?,?,?,?,?,?,?,?,?)";

    // Bind parameters to the prepared statement
    $stmt = $connection->prepare($insertQuery);
    $stmt->bind_param('ssssssiss', $valid_id, $firstname, $lastname, $email, $password, $address, $contact, $accesslevel, $position);

    // Execute the statement
    if($stmt->execute()) {
        // Handle successful execution
        // Redirect or display success message
        $_SESSION['valid_id'] = $valid_id;
        $_SESSION['id'] = $id;
        $_SESSION['email'] = $email;
        $_SESSION['firstname'] = $firstname;
        $_SESSION['lastname'] = $lastname;
        $_SESSION['address'] = $address;
        $_SESSION['contact'] = $contact;
        $_SESSION['access_level'] = $accesslevel;
        $_SESSION['position'] = $position;
        $_SESSION['logged_in'] = true;
        header('location: login.php');
        exit(); // Ensure script termination after redirection
    } else {
        // Handle execution failure
        // Redirect or display error message
        header('location: signup.php?error=Email has been used');
        exit(); // Ensure script termination after redirection
    }
}

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Account</title>
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
        <div class="card card-registration my-5 custom-shadow ">
          <div class="row g-0">
            <div class="col-xl-6 d-none d-xl-block d-flex align-items-center justify-content-center">
              <img src="images/vape.jpg"
                alt="Sample photo" class="img-fluid h-100"
                style="border-top-left-radius: .25rem; border-bottom-left-radius: .25rem;" />
            </div>
            <div class="col-xl-6">
                <form action="signup.php" method="POST" enctype="multipart/form-data">
              <div class="card-body p-md-5 text-black">
                <div class="row">
                <h3 class="mb-5 text-uppercase">Sign Up</h3>
                <p style="color:red"><?php if(isset($_GET['error'])){ echo $_GET['error']; } ?></p>
                <label class="form-label" for="form3Example1m">Valid ID*</label>
                <div class="imgholder">                        
                    <img id="previewImage" id="currentProfilePicture" width="200" height="150" class="img">
                        <label for="uploadimg" class="upload">
                            <input type="file" name="valid_id" id="uploadimg" class="picture" onchange="previewFile()" required>
                            <i class="fa-solid fa-plus"></i>
                        </label>
                    </div>
                  </div>

                <div class="row">
                  <div class="col-md-6 mb-4">
                    <div class="form-outline">
                      <label class="form-label" for="form3Example1m">First name</label>
                      <input type="text" name="firstname" id="form3Example1m" class="form-control form-control-lg" required/>
                      
                    </div>
                  </div>
                  <div class="col-md-6 mb-4">
                    <div class="form-outline">
                      <label class="form-label" for="form3Example1n">Last name</label>
                      <input type="text" name="lastname" id="form3Example1n" class="form-control form-control-lg" required/>
                      
                    </div>
                  </div>
                </div>                

                <div class="form-outline mb-4">
                  <label class="form-label" for="form3Example8">Address</label>
                  <input type="text" name="address" id="form3Example8" class="form-control form-control-lg" required/>
                  
                </div>                                

                <div class="form-outline mb-4">
                  <label class="form-label" for="form3Example9">Contact</label>
                  <input type="text" name="contact" id="form3Example9" class="form-control form-control-lg" required/>
                  
                </div>

                <div class="form-outline mb-4">
                  <label class="form-label" for="form3Example90">Email</label>
                  <input type="text" name="email" id="form3Example90" class="form-control form-control-lg" required/>
                  
                </div>

                <div class="row">
                  <div class="col-md-6 mb-4">
                    <div class="form-outline">
                      <label class="form-label" for="form3Example1m">Password</label>
                      <input type="password" name="password" id="form3Example1m" class="form-control form-control-lg" required/>
                      
                    </div>
                  </div>
                  <div class="col-md-6 mb-4">
                    <div class="form-outline">
                      <label class="form-label" for="form3Example1n">Confirm Password</label>
                      <input type="password" name="confirmPassword" id="form3Example1n" class="form-control form-control-lg" required/>
                      
                    </div>
                  </div>
                </div>

                <div class="d-flex justify-content-end pt-3">                  
                  <button type="submit" name="signupBtn" class="btn btn-primary btn-lg ms-2 custom-shadow">Sign Up</button>
                </div>
          

                <p class="text-center text-muted mt-5 mb-0">Have already an account? <a href="login.php"
                    class="fw-bold text-body"><u>Login here</u></a></p>
              </div>
          </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<script>
    function previewFile() {
        const preview = document.getElementById('previewImage');
        const currentProfilePicture = document.getElementById('currentProfilePicture');
        const file = document.querySelector('input[type=file]').files[0];
        const reader = new FileReader();

        reader.onloadend = function () {
            preview.src = reader.result;
        };

        if (file) {
            reader.readAsDataURL(file);
        } else {
            // If no file is selected, display the current profile picture
            preview.src = currentProfilePicture.src;
        }
    }
</script>
    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/2.0.0/js/dataTables.js"></script>
    <script src="https://cdn.datatables.net/2.0.0/js/dataTables.bootstrap5.js"></script>
    <script src="./assets/js/script.js"></script>
    <script src="./assets/js/custom-admin-template.js"></script>
</body>
</html>
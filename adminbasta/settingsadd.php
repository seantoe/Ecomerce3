<?php 
session_start();
$page_title = 'Add Product';
require_once '../config/connection.php';

$id = $_SESSION['id'];
$query = "SELECT * FROM users WHERE id='$id'";
$nav = mysqli_query($connection, $query);

if(isset($_POST['submit'])){
    // Prepare and execute the query to insert data into the settings table
    $insert_query = "INSERT INTO settings (title, description, logo, image1, image2, contact1, contact2) VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($connection, $insert_query);

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

    // Bind parameters and execute the statement
    mysqli_stmt_bind_param($stmt, "sssssss", $title, $description, $logo, $image1, $image2, $contact1, $contact2);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    header("Location: settings.php");
}


?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Add</title>
	<link href="https://fonts.googleapis.com/icon?family=Material+Icons+Sharp" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
</head>
<body>
<div class="container">
        <!-- Sidebar Section -->
        <aside>
            <div class="toggle">
                <div class="logo">
                    <img src="images/logo.png">
                    <h2>Admin<span class="danger">page</span></h2>
                </div>
                <div class="close" id="close-btn">
                    <span class="material-icons-sharp">
                        close
                    </span>
                </div>
            </div>

            <div class="sidebar">
                <!-- Users widget -->
                <a href="usermanage.php">
                    <span class="material-icons-sharp">
                        person_outline
                    </span>
                    <h3>Users</h3>
                </a>
                <!-- History widget -->
                <a href="order.php">
                    <span class="material-icons-sharp">
                        receipt_long
                    </span>
                    <h3>History</h3>
                </a>
                <!-- DashBoard widget naka active to para makita ko lang ano itsura bat clinick eto rin yung default page-->
                <a href="index.php">
                    <span class="material-icons-sharp">
                        insights
                    </span>
                    <h3>Dash Board</h3>
                </a>                                
                  <!-- Ano dapat to sample number pero gusto ko sana 
                  mag bago per email so php js yata to query 
                    <span class="message-count">27</span> -->
                </a>
                <!-- Inventory Widget-->
                <a href="product.php">
                    <span class="material-icons-sharp">
                        inventory
                    </span>
                    <h3>Product List</h3>
                </a>
                <!-- Report Widget -->
                <a href="reports.php">
                    <span class="material-icons-sharp">
                        report_gmailerrorred
                    </span>
                    <h3>Reports</h3>
                </a>
                <!-- Settings widget -->
                <a href="settings.php" class="active">
                    <span class="material-icons-sharp">
                        settings
                    </span>
                    <h3>Settings</h3>
                </a>
                <!-- New Login widget -->
                <a href="accountmanage.php">
                    <span class="material-icons-sharp">
                        add
                    </span>
                    <h3>Account Management</h3>
                </a>
                <!-- Logout widget -->
                <a href="logout.php">
                    <span class="material-icons-sharp">
                        logout
                    </span>
                    <h3>Logout</h3>
                </a>
            </div>
        </aside>
        <!-- End of Sidebar Section -->
<main>
        <!-- Content ng settings -->

	<h1>Add</h1>

		<form action="settingsadd.php" method="POST" enctype="multipart/form-data">
			<br>
			<label>Title: </label>
				<input placeholder="" type="text" name="title" required>
			<br>
			<label>URL / Domain: </label>
				<input type="text" name="slug" required>
			<br>
			<label>Description: </label>
				<input type="text" name="description" required>
			<br>
            <label>Logo: </label>
            	<input type="file" name="logo"  accept="image/*">
            <br>
            <label>Image 1: </label>
            	<input type="file" name="image1"  accept="image/*">
            <br>
            <label>Image 2: </label>
            	<input type="file" name="image2"  accept="image/*">
            <br>
            <label>Contact 1: </label>
				<input type="text" name="contact1" required>
			<br>
			<label>Contact 2: </label>
				<input type="text" name="contact2" required>
			<br>
            <button><a href="settings.php">Back</a></button>

            <button type="submit" value="submit" name="submit" href="settings.php">Add</button>
        </form>
</main>

<?php while($rows = mysqli_fetch_array($nav)){ ?>
        <!-- Right Section -->
        <div class="right-section">
            <div class="nav">
                <button id="menu-btn">
                    <span class="material-icons-sharp">
                        menu
                    </span>
                </button>
                <div class="dark-mode">
                    <span class="material-icons-sharp active">
                        light_mode
                    </span>
                    <span class="material-icons-sharp">
                        dark_mode
                    </span>
                </div>

                <div class="profile">
                    <div class="info">
                        <!-- Ganda sana kung kaya niya iquery name ng admin page-->
                        <p>Hey, <b><?php echo $rows['firstname'] ." ". $rows['lastname']; ?></b></p>
                        <small class="text-muted">Admin</small>
                    </div>
                    <!-- Same thing here Ganda sana kung kaya niya iquery pic profile na inupload ng admin sa admin page-->
                    <div class="profile-photo">
                        <img src="<?php echo $rows['pfp'] ?>">
                    </div>
                </div>

            </div>
            <!-- End of Nav -->
                    <?php } ?>

<script src="assets/js/darkmode.js"></script>
<script src="assets/js/index.js"></script>
</body>
</html>

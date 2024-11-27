<?php 
session_start();
require_once '../config/connection.php';

$id = $_SESSION['id'];
$query = "SELECT * FROM users WHERE id='$id'";
$nav = mysqli_query($connection, $query);

if(isset($_POST['submit'])){

    //prepared statemt to prevent sql injection
    $insert_query = "INSERT INTO users (firstname, lastname, email, password, access_level, address, contact, position) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($connection, $insert_query);

    $firstname = $_POST['firstname'];
    $lastname = $_POST['lastname'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $access_level = $_POST['access_level'];
    $address = $_POST['address'];
    $contact = $_POST['contact'];
    $position = $_POST['position'];

    // Set position based on access level
            switch ($access_level) {
                case 1:
                    $position = 'Admin';
                    break;
                case 2:
                    $position = 'Country Manager';
                    break;
                case 3:
                    $position = 'Team Leader';
                    break;
                case 4:
                    $position = 'Sales Agent';
                    break;
                case 5:
                    $position = 'Client';
                    break;
                default:
                    $position = '';
                }

    $stmt1 = $connection->prepare("SELECT COUNT(*) FROM users where email = ?");
        $stmt1->bind_param('s', $email);
        $stmt1->execute();
        $stmt1->bind_result($num_rows);
        $stmt1->store_result();
        $stmt1->fetch();

        if($num_rows != 0){
            $stmt1->close(); // Close the SELECT query result set
            header('location: signup.php?error=Email already exists');
            }

        $stmt1->close(); // Close the SELECT query result set
    

    mysqli_stmt_bind_param($stmt, "ssssisss", $firstname, $lastname, $email, md5($password), $access_level, $address, $contact, $position);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    header("Location: usermanage.php");
}

    /*if($email != '' || $password != '' || $firstname != '' || $lastname != '' || $access_level != '' || $address != '' || $contact != '')
    {
        //prepared statemt to prevent sql injection
    $insert_query = "INSERT INTO users (email, password, access_level, firstname, lastname, address, contact) VALUES (?, ?, ?, ?, ?, ?, ?)";
    $result = mysqli_prepare($connection, $insert_query);
    mysqli_stmt_bind_param($result, "ssissss", $email, $password, $access_level, $firstname, $lastname, $address, $contact);
    mysqli_stmt_execute($result);
    mysqli_stmt_close($result);
        if($result){
            redirect('usermanage.php', 'User/Admin added successfully');
    }else
    {
        redirect('useradd.php','Please fill all the input fields');
    }
}
}*/

?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Add New Product</title>
	<link href="https://fonts.googleapis.com/icon?family=Material+Icons+Sharp" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
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
                <a href="usermanage.php" class="active">
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
                <a href="settings.php">
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
	<h1>Add User</h1>
    <center><p style="color:red"><?php if(isset($_GET['error'])){ echo $_GET['error']; } ?></p></center>
		<form action="useradd.php" method="POST" enctype="multipart/form-data">
			<br>
            <label>Email: </label>
                <input type="text" name="email" required>
            <br>
            <label for="password">Password: </label>
                <input type="password" name="password" required>
            <br>
			<label>First Name: </label>
				<input type="text" name="firstname" required>
			<br>
			<label>Last Name: </label>
				<input type="text" name="lastname" required>
			<br>
            <label>Access Level:</label>
                <select name="access_level" required>
                        <option value="">--Access Level--</option>
                                <option value="1">Admin</option>
                                <option value="2">Country Manager</option>
                                <option value="3">Team Leader</option>
                                <option value="4">Sales Agent</option>
                                <option value="5">Client</option>
                </select><br>
            <input type="hidden" name="position" value="<?php echo $row['position']; ?>">
            <label>Address: </label>
                <input type="text" name="address" required>
            <br>
            <label>Contact: </label>
				<input type="text" name="contact" required>
			<br>
            <a href="usermanage.php">Back</a>
            <button type="submit" name="submit" href="usermanage.php">Add User</button>
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

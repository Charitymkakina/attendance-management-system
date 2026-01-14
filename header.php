<?php

// Ensure the user is a lecturer and is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit();
}

// Include the database connection
include('../config/db_connection.php');
$admin_id = $_SESSION['user_id'];

$query = "SELECT username FROM users WHERE id = ? AND role = 'admin'";
$stmt = $conn->prepare($query);

if (!$stmt) {
    die("Error preparing the query: " . $conn->error);
}

$stmt->bind_param("i", $admin_id);
$stmt->execute();
$stmt->bind_result($admin_username);
$stmt->fetch();
$stmt->close(); 
?>
<head>
<link href="../assets/fontawesome/css/all.css" rel="stylesheet" />
<link rel="stylesheet" href="../assets/bootstrap/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">

<link rel="stylesheet" href="css/style.css">
<link rel="icon" type="image/x-icon" href="../assets/images/fav.png">
</head>
<header>
            <div class="ul">
                <div>
                    <img src="../assets/images/logo1.png" alt="">

                </div>
                <div class="dashboard-options">
                    <h4><?php echo htmlspecialchars($admin_username); ?></h4>
            
                    <a href="index.php" class="btn btn-primary"><i class="fas fa-house"></i>home</a>
                    <a href="students.php" class="btn btn-primary"><i class="fas fa-user-group"></i>students</a>
                    <a href="lecturers.php" class="btn btn-primary"><i class="fas fa-user-group"></i>lecturers</a>
                    <a href="profile.php" class="btn btn-primary"><i class="fas fa-user"></i>profile</a>
                    <a href="login.php" class="btn btn-primary"><i class="fas fa-sign-out"></i>logout</a>
                </div>
            </div>
            
        </header>
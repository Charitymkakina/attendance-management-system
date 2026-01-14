<?php


// Ensure the user is a lecturer and is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'lecturer') {
    header("Location: login.php");
    exit();
}

// Include the database connection
include('../config/db_connection.php');
$lecturer_id = $_SESSION['user_id'];

$query = "SELECT username FROM users WHERE id = ?";
$stmt = $conn->prepare($query);

if (!$stmt) {
    die("Error preparing the query: " . $conn->error);
}

$stmt->bind_param("i", $lecturer_id);
$stmt->execute();
$stmt->bind_result($lecturer_username);
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
                    <h4>Lecturer <?php echo htmlspecialchars($lecturer_username); ?></h4>
            
                    <a href="index.php" class="btn btn-primary"><i class="fas fa-house"></i>home</a>
                    <a href="add_lesson.php" class="btn btn-primary"><i class="fas fa-plus-square"></i>add Lesson</a>
                
                    <a href="profile.php" class="btn btn-primary"><i class="fas fa-user"></i>profile</a>
                    <a href="login.php" class="btn btn-primary"><i class="fas fa-sign-out"></i>logout</a>
                </div>
            </div>
            
        </header>
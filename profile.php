<?php
session_start();

// Ensure the user is a lecturer and is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit();
}

// Include the database connection
include('../config/db_connection.php');
$adm_id = $_SESSION['user_id'];

// Fetch lecturer details
$query = "SELECT id, username, email, password AS adm_passwd FROM users WHERE id = ? AND role = 'admin'";
$stmt = $conn->prepare($query);

if (!$stmt) {
    die("Error preparing the query: " . $conn->error);
}

$stmt->bind_param("i", $adm_id);
$stmt->execute();
$result = $stmt->get_result();
$admin = $result->fetch_assoc();
$stmt->close();

if (!$admin) {
    die("user not found.");
}

// Update profile
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['adm_passwd'];
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Update email and password in the database
    $update_query = "UPDATE users SET email = ?, password = ? WHERE id = ? AND role = 'admin'";
    $update_stmt = $conn->prepare($update_query);

    if ($update_stmt) {
        $update_stmt->bind_param("ssi", $email, $hashed_password, $adm_id);
        if ($update_stmt->execute()) {
            header("Location: profile.php"); // Reload after update
            exit();
        } else {
            $error = "Error updating profile!";
        }
        $update_stmt->close();
    } else {
        $error = "Error preparing update query!";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile - BIDII School</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

    <?php include('header.php'); ?> 
    <div class="dashboard-container">
        <div class="cards">
            <h2>Admin Profile</h2>
            <?php if (isset($error)) { echo "<p class='error'>$error</p>"; } ?>
            <form action="profile.php" method="POST">
                
                <label for="email">Email</label>
                <input type="email" name="email" id="email" value="<?php echo htmlspecialchars($admin['email']); ?>" required>

                <label for="password">New Password</label>
                <input type="password" name="password" id="password" placeholder="Enter new password" required>

                <button type="submit">Update Profile</button>
            </form>
        </div>
        <?php include('statistics.php'); ?> 
    </div>
</body>
</html>

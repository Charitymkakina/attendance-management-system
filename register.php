<?php
// Start the session
session_start();

// Include database connection
include('../config/db_connection.php');

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Initialize error message variable
    $error = '';

    // Sanitize user inputs
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // Validate the form inputs
    if (empty($username) || empty($email) || empty($password)) {
        $error = "All fields are required!";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format!";
    } else {
        // Hash the password securely
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Prepare SQL query to insert user into database
        $stmt = $conn->prepare("INSERT INTO users (username, password, email, role) VALUES (?, ?, ?, 'lecturer')");
        $stmt->bind_param("sss", $username, $hashed_password, $email);

        // Check if query executed successfully
        if ($stmt->execute()) {
            // Redirect to login page after successful registration
            $_SESSION['success_message'] = "Registration successful! You can now log in.";
            header("Location: login.php");
            exit();
        } else {
            // Database error
            $error = "Error during registration. Please try again!";
        }

        // Close prepared statement and database connection
        $stmt->close();
        $conn->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lecturer Registration - BIDII School</title>
    <style>
        body {
    font-family: 'Poppins', sans-serif;
    text-align: center;
    margin: 0;
    padding: 0;
    background-image: url('../assets/images/bg.png');
    background-size: cover;
    background-position: center;
}
    </style>
    <link rel="stylesheet" href="../assets/bootstrap/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">

    <link rel="stylesheet" href="css/style.css">
    <link rel="icon" type="image/x-icon" href="../assets/images/fav.png">
</head>
<body>
<div class="register-container card-white">
        <div class="card-header">
            <img src="../assets/images/logo2.png" alt="" style="max-width: 200px; height: auto; border-radius: 5px;">
        <h2>Lecturer registration</h2> 
        </div>

        <?php
        // Display success or error messages
        if (isset($error)) {
            echo "<p class='error'>$error</p>";
        } elseif (isset($_SESSION['success_message'])) {
            echo "<p class='success'>{$_SESSION['success_message']}</p>";
            unset($_SESSION['success_message']);
        }
        ?>
     
        <form action="register.php" method="POST">
            <label for="username">Name</label>
            <input type="text" name="username" id="username" value="<?php echo isset($username) ? htmlspecialchars($username) : ''; ?>" required>

            <label for="email">Email</label>
            <input type="email" name="email" id="email" value="<?php echo isset($email) ? htmlspecialchars($email) : ''; ?>" required>

            <label for="password">Password</label>
            <input type="password" name="password" id="password" required>

            <input type="submit" value="Register"></input>
            <p>Already have an account? <a href="login.php">Login here</a></p>
        </form>
    </div>
</body>
</html>

<?php
session_start();  // Start the session to store user data upon successful login

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $registration_number = $_POST['registration_number'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Database connection
    include('../config/db_connection.php');

    // Prepare SQL query to check if student exists with the provided registration number and email
    $stmt = $conn->prepare("SELECT registration_number, username, password FROM students WHERE registration_number = ? AND email = ?");
    $stmt->bind_param("ss", $registration_number, $email); // Bind registration number and email parameters
    $stmt->execute();

    // Store result
    $stmt->store_result();
    $stmt->bind_result($db_registration_number, $username, $hashed_password);
    $stmt->fetch();

    // Check if student exists and password is correct
    if ($stmt->num_rows > 0) {
        // Password verification
        if (password_verify($password, $hashed_password)) {
            // Successful login, store user data in session
            $_SESSION['registration_number'] = $db_registration_number;
            $_SESSION['username'] = $username;

            // Redirect to the student dashboard
            header("Location: index.php");
            exit();
        } else {
            $error = "Invalid password!";
        }
    } else {
        $error = "Invalid registration number, email, or student not found!";
    }

    // Close the statement and database connection
    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Login - BIDII School</title>
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
<div class="form-container card-white">
        <div class="card-header">
            <img src="../assets/images/logo2.png" alt="" style="max-width: 200px; height: auto; border-radius: 5px;">
        <h2>Student Login</h2> 
        </div>
        <?php if (isset($error)) { echo "<p class='error'>$error</p>"; } ?>
        <form action="login.php" method="POST">
            <label for="registration_number">Registration Number</label>
            <input type="text" name="registration_number" id="registration_number" required>

            <label for="email">Email</label>
            <input type="email" name="email" id="email" required>

            <label for="password">Password</label>
            <input type="password" name="password" id="password" required>

            <input type="submit" value="Login"></input>
            <p>Don't have an account? <a href="register.php">Register here</a></p>
            <a href="../index.php">Go back</a>
        </form>
    </div>
</body>
</html>

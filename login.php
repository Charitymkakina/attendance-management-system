<?php
session_start();  // Start the session to store user data upon successful login

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Database connection
    include('../config/db_connection.php');

    // Prepare SQL query to check if user exists with provided email
    $stmt = $conn->prepare("SELECT id, username, password, role FROM users WHERE email = ? AND role = 'lecturer'");
    $stmt->bind_param("s", $email); // Bind email parameter
    $stmt->execute();

    // Store result
    $stmt->store_result();
    $stmt->bind_result($id, $username, $hashed_password, $role);
    $stmt->fetch();

    // Debugging: Check the values fetched from the database
    // echo "Fetched data: ID: $id, Username: $username, Password: $hashed_password, Role: $role";

    // Check if user exists and password is correct
    if ($stmt->num_rows > 0) {
        // Password verification
        if (password_verify($password, $hashed_password)) {
            // Successful login, store user data in session
            $_SESSION['user_id'] = $id;
            $_SESSION['username'] = $username;
            $_SESSION['role'] = $role; // Store the user's role

            // Redirect to the lecturer dashboard
            header("Location: index.php");
            exit();
        } else {
            $error = "Invalid password!";
        }
    } else {
        $error = "Invalid email or user not found!";
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
    <title>Lecturer Login - BIDII School</title>
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
        <h2>Lecturer Login</h2> 
        </div>
        <?php if (isset($error)) { echo "<p class='error'>$error</p>"; } ?>
        <form action="login.php" method="POST">
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

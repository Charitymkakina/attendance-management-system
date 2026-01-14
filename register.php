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
    $registration_number = trim($_POST['registration_number']);
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $course = trim($_POST['course']);
    $password = trim($_POST['password']);
    $phone = trim($_POST['phone']);

    // Validate the form inputs
    if (empty($registration_number) || empty($username) || empty($email) || empty($course) || empty($password) || empty($phone)) {
        $error = "All fields are required!";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format!";
    } else {
        // Hash the password securely
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Prepare SQL query to insert student into database
        $stmt = $conn->prepare("INSERT INTO students (registration_number, username, email, course, password, phone) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssss", $registration_number, $username, $email, $course, $hashed_password, $phone);

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
    <title>Student Registration - BIDII School</title>
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
        <h2>Student registration</h2> 
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
            <label for="registration_number">Registration Number</label>
            <input type="text" name="registration_number" id="registration_number" value="<?php echo isset($registration_number) ? htmlspecialchars($registration_number) : ''; ?>" required>

            <label for="username">Name</label>
            <input type="text" name="username" id="username" value="<?php echo isset($username) ? htmlspecialchars($username) : ''; ?>" required>

            <label for="email">Email</label>
            <input type="email" name="email" id="email" value="<?php echo isset($email) ? htmlspecialchars($email) : ''; ?>" required>

            <label for="course">Course</label>
            <input type="text" name="course" id="course" value="<?php echo isset($course) ? htmlspecialchars($course) : ''; ?>" required>

            <label for="password">Password</label>
            <input type="password" name="password" id="password" required>

            <label for="phone">Phone</label>
            <input type="text" name="phone" id="phone" value="<?php echo isset($phone) ? htmlspecialchars($phone) : ''; ?>" required>

            <input type="submit" value="Register"></input>
            <p>Already have an account? <a href="login.php">Login here</a></p>
        </form>
    </div>
</body>
</html>

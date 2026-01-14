<?php
session_start(); // Start the session

// Ensure the lecturer is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'lecturer') {
    header("Location: login.php"); // Redirect to login if not logged in
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Sanitize and validate the form input
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $enrollment_key = trim($_POST['enrollment_key']);
    $lecturer_id = $_SESSION['user_id']; // Use session user_id for the lecturer's ID

    // Include database connection
    include('../config/db_connection.php');

    // Prepare SQL query using prepared statements to prevent SQL injection
    $stmt = $conn->prepare("INSERT INTO lessons (title, description, lecturer_id, enrollment_key) 
                            VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $title, $description, $lecturer_id, $enrollment_key);

    // Execute the query and check if it was successful
    if ($stmt->execute()) {
        header("Location: index.php"); // Redirect to the main page after success
        exit();
    } else {
        $error = "Error adding lesson! Please try again.";
    }

    // Close the statement and the database connection
    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Lesson - BIDII School</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">

    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <?php include('header.php'); ?> 
    <div class="dashboard-container">
    <div class="cards">
        <h2>Add New Lesson</h2>

        <?php if (isset($error)) { echo "<p class='error'>$error</p>"; } ?>

        <!-- Form to add new lesson -->
        <form action="add_lesson.php" method="POST">
            <label for="title">Title</label>
            <input type="text" name="title" id="title" required>

            <label for="description">Description</label>
            <textarea name="description" id="description" required></textarea>

            <label for="enrollment_key">Enrollment Key</label>
            <input type="text" name="enrollment_key" id="enrollment_key" required>

            <button type="submit">Add Lesson</button>
        </form>
    </div>
    <?php include('statistics.php'); ?>          
</div>
</body>
</html>

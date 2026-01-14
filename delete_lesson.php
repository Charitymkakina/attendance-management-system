<?php
session_start();

// Ensure the user is a lecturer and is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'lecturer') {
    header("Location: login.php");
    exit();
}

include('../config/db_connection.php');

// Get the lesson ID from the URL
if (isset($_GET['lesson_id'])) {
    $lesson_id = $_GET['lesson_id'];

    // Secure SQL query to delete the lesson, ensuring the lecturer only deletes their own lessons
    $delete_query = "DELETE FROM lessons WHERE lesson_id = ? AND lecturer_id = ?";
    $delete_stmt = $conn->prepare($delete_query);
    $delete_stmt->bind_param("ii", $lesson_id, $_SESSION['user_id']);
    
    if ($delete_stmt->execute()) {
        header("Location: index.php"); // Redirect after successful deletion
        exit();
    } else {
        $error = "Error deleting lesson! Please try again.";
    }
    
    $delete_stmt->close();
} else {
    // Redirect if no lesson ID is provided
    header("Location: index.php");
    exit();
}

$conn->close();
?>
 
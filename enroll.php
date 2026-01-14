<?php
session_start();
include('../config/db_connection.php');

// Ensure the request is a POST and the student is logged in
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['registration_number'])) {
    $registration_number = $_SESSION['registration_number'];
    $lesson_id = $_POST['lesson_id'];
    $entered_key = $_POST['enrollment_key'];

    // Check if the provided enrollment key matches the lesson's key
    $query = "SELECT enrollment_key FROM lessons WHERE lesson_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $lesson_id);
    $stmt->execute();
    $stmt->bind_result($correct_key);
    $stmt->fetch();
    $stmt->close();

    if ($entered_key === $correct_key) {
        // Verify if the student is already enrolled
        $check_query = "SELECT * FROM enrollments WHERE lesson_id = ? AND student_id = ?";
        $check_stmt = $conn->prepare($check_query);
        $check_stmt->bind_param("is", $lesson_id, $registration_number);
        $check_stmt->execute();
        $check_stmt->store_result();

        if ($check_stmt->num_rows === 0) {
            // Insert enrollment record if the student is not already enrolled
            $enroll_query = "INSERT INTO enrollments (lesson_id, student_id) VALUES (?, ?)";
            $enroll_stmt = $conn->prepare($enroll_query);
            $enroll_stmt->bind_param("is", $lesson_id, $registration_number);

            if ($enroll_stmt->execute()) {
                echo "success";
            } else {
                echo "Error:";
            }
            $enroll_stmt->close();
        } else {
            echo "You are already enrolled in this lesson.";
        }
        $check_stmt->close();
    } else {
        echo "Invalid enrollment key.";
    }
} else {
    echo "Unauthorized access.";
}

$conn->close();
?>

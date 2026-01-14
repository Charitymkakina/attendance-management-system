<?php
session_start();
include('../config/db_connection.php');

// Ensure the user is a student and is logged in
if (!isset($_SESSION['registration_number'])) {
    echo "Please log in first.";
    exit();
}

// Get the logged-in student's registration number
$studentId = $_SESSION['registration_number'];

// Get the lesson ID from the POST request
if (isset($_POST['lesson_id'])) {
    $lessonId = $_POST['lesson_id'];

    // Check if the student is enrolled in the lesson
    $enrollmentQuery = "SELECT * FROM enrollments WHERE student_id = ? AND lesson_id = ?";
    $stmt = $conn->prepare($enrollmentQuery);
    $stmt->bind_param("ii", $studentId, $lessonId);
    $stmt->execute();
    $enrollmentResult = $stmt->get_result();

    if ($enrollmentResult->num_rows > 0) {
        // Check if today's attendance has already been marked
        $attendanceQueryToday = "SELECT * FROM attendance WHERE student_id = ? AND lesson_id = ? AND DATE(attendance_date) = CURDATE()";
        $stmt = $conn->prepare($attendanceQueryToday);
        $stmt->bind_param("ii", $studentId, $lessonId);
        $stmt->execute();
        $attendanceResultToday = $stmt->get_result();

        // Prevent marking attendance twice for the same day
        if ($attendanceResultToday->num_rows > 0) {
            echo "You have already marked attendance for today.";
        } else {
            // Get the last attendance date for this student and lesson
            $lastAttendanceQuery = "SELECT MAX(attendance_date) as last_date FROM attendance WHERE student_id = ? AND lesson_id = ?";
            $stmt = $conn->prepare($lastAttendanceQuery);
            $stmt->bind_param("ii", $studentId, $lessonId);
            $stmt->execute();
            $lastAttendanceResult = $stmt->get_result();
            $lastAttendanceRow = $lastAttendanceResult->fetch_assoc();

            // Determine the last attendance date
            $lastDate = $lastAttendanceRow['last_date'] ? new DateTime($lastAttendanceRow['last_date']) : null;
            $currentDate = new DateTime();
            $currentDate->setTime(0, 0); // Reset time to midnight for comparison
            $yesterday = (clone $currentDate)->modify('-1 day');

            // If there's a gap, fill the days in between as 'absent'
            if ($lastDate) {
                $lastDate = new DateTime($lastDate->format('Y-m-d')); // Ensure only the date part is used
                $lastDate->modify('+1 day'); // Start from the day after the last attendance

                // Mark each day from the last attendance up to yesterday as 'absent'
                while ($lastDate <= $yesterday) {
                    $fillDate = $lastDate->format('Y-m-d');
                    $insertAbsentQuery = "INSERT INTO attendance (student_id, lesson_id, status, attendance_date) VALUES (?, ?, 'absent', ?)";
                    $stmt = $conn->prepare($insertAbsentQuery);
                    $stmt->bind_param("iis", $studentId, $lessonId, $fillDate);
                    $stmt->execute();
                    $lastDate->modify('+1 day');
                }
            }

            // Mark today's attendance with both date and time as 'present'
            $insertPresentQuery = "INSERT INTO attendance (student_id, lesson_id, status, attendance_date) VALUES (?, ?, 'present', NOW())";
            $stmt = $conn->prepare($insertPresentQuery);
            $stmt->bind_param("ii", $studentId, $lessonId);
            $stmt->execute();

            // Return success message
            echo 'success';
        }
    } else {
        echo "You are not enrolled in this lesson.";
    }
} else {
    echo "Lesson ID is missing.";
}

// Close statement and connection
$stmt->close();
$conn->close();
?>

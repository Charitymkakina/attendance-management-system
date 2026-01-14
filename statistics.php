<?php

// Ensure the user is a student and is logged in

if (!isset($_SESSION['registration_number'])) {
    header("Location: login.php");
    exit();
}

// Fetching the student's registration number
include('../config/db_connection.php');
$student_id = $_SESSION['registration_number'];

// Fetch the total number of lessons available
$total_lessons_query = "SELECT COUNT(*) FROM lessons";
$total_lessons_stmt = $conn->prepare($total_lessons_query);
$total_lessons_stmt->execute();
$total_lessons_stmt->bind_result($total_lessons);
$total_lessons_stmt->fetch();
$total_lessons_stmt->close();

// Fetch total enrollments for the student
$total_enrollments_query = "SELECT COUNT(*) FROM enrollments WHERE student_id = ?";
$total_enrollments_stmt = $conn->prepare($total_enrollments_query);
$total_enrollments_stmt->bind_param("i", $student_id);
$total_enrollments_stmt->execute();
$total_enrollments_stmt->bind_result($total_enrollments);
$total_enrollments_stmt->fetch();
$total_enrollments_stmt->close();

// Fetch the percentage attendance per lesson
$attendance_per_lesson_query = "
    SELECT 
        lessons.title, 
        COUNT(CASE WHEN attendance.status = 'present' THEN 1 END) AS attendance_count, 
        (SELECT COUNT(*) FROM attendance WHERE student_id = ? AND lesson_id = lessons.lesson_id) AS total_attendance_records
    FROM lessons 
    LEFT JOIN attendance ON lessons.lesson_id = attendance.lesson_id AND attendance.student_id = ?
    WHERE lessons.lesson_id IN (SELECT lesson_id FROM enrollments WHERE student_id = ?)
    GROUP BY lessons.lesson_id
";
$attendance_per_lesson_stmt = $conn->prepare($attendance_per_lesson_query);
$attendance_per_lesson_stmt->bind_param("iii", $student_id, $student_id, $student_id);
$attendance_per_lesson_stmt->execute();
$attendance_per_lesson_result = $attendance_per_lesson_stmt->get_result();

$conn->close(); // Close connection

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard - Statistics</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<div class="stats-section">
    <div class="stats">
        <h3>Statistics</h3>
        <ul class="list-group">
            <li class="list-group-item"><strong>Total Lessons Available:</strong> <?php echo $total_lessons; ?></li>
            <li class="list-group-item"><strong>Total Enrollments:</strong> <?php echo $total_enrollments; ?></li>
        </ul>
        <strong>ATTENDANCE</strong>
        <ul class="list-group">
            <?php while ($row = $attendance_per_lesson_result->fetch_assoc()) {
                // Calculate the percentage of attendance for each lesson
                $attendance_percentage = $row['total_attendance_records'] > 0 ? ($row['attendance_count'] / $row['total_attendance_records']) * 100 : 0;
            ?>
                <li class="list-group-item">
                    <strong><?php echo htmlspecialchars($row['title']); ?>:</strong>
                    <?php echo number_format($attendance_percentage, 2); ?>%
                </li>
            <?php } ?>
        </ul>
    </div>
</div>
</body>
</html>

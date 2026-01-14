<?php

// Ensure the user is a lecturer and is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'lecturer') {
    header("Location: login.php");
    exit();
}

// Fetching the lecturer's username based on user_id
include('../config/db_connection.php');
$lecturer_id = $_SESSION['user_id'];

// Fetch the total number of lessons for the logged-in lecturer
$total_lessons_query = "SELECT COUNT(*) FROM lessons WHERE lecturer_id = ?";
$total_lessons_stmt = $conn->prepare($total_lessons_query);
$total_lessons_stmt->bind_param("i", $lecturer_id);
$total_lessons_stmt->execute();
$total_lessons_stmt->bind_result($total_lessons);
$total_lessons_stmt->fetch();
$total_lessons_stmt->close();

// Fetch total enrollments for each lesson
$total_enrollments_query = "
    SELECT lessons.title, lessons.lesson_id, COUNT(enrollments.student_id) AS total_enrollments
    FROM lessons
    LEFT JOIN enrollments ON lessons.lesson_id = enrollments.lesson_id
    WHERE lessons.lecturer_id = ?
    GROUP BY lessons.lesson_id";
$total_enrollments_stmt = $conn->prepare($total_enrollments_query);
$total_enrollments_stmt->bind_param("i", $lecturer_id);
$total_enrollments_stmt->execute();
$total_enrollments_result = $total_enrollments_stmt->get_result();

// Initialize arrays to store enrollments and attendance data for each lesson
$lessons_data = [];
while ($row = $total_enrollments_result->fetch_assoc()) {
    $lessons_data[$row['lesson_id']] = [
        'title' => $row['title'],
        'total_enrollments' => $row['total_enrollments'],
        'total_attendance' => 0, // Placeholder for attendance to be updated
        'total_present' => 0 // Placeholder for present count to be updated
    ];
}
$total_enrollments_stmt->close();

// Fetch total attendance and count of "present" status for each lesson
$total_attendance_query = "
    SELECT lessons.lesson_id, COUNT(attendance.student_id) AS total_attendance, 
           SUM(CASE WHEN attendance.status = 'present' THEN 1 ELSE 0 END) AS total_present
    FROM lessons
    LEFT JOIN attendance ON lessons.lesson_id = attendance.lesson_id
    WHERE lessons.lecturer_id = ?
    GROUP BY lessons.lesson_id";
$total_attendance_stmt = $conn->prepare($total_attendance_query);
$total_attendance_stmt->bind_param("i", $lecturer_id);
$total_attendance_stmt->execute();
$total_attendance_result = $total_attendance_stmt->get_result();

// Update the attendance counts in the lessons_data array
while ($row = $total_attendance_result->fetch_assoc()) {
    if (isset($lessons_data[$row['lesson_id']])) {
        $lessons_data[$row['lesson_id']]['total_attendance'] = $row['total_attendance'];
        $lessons_data[$row['lesson_id']]['total_present'] = $row['total_present'];
    }
}
$total_attendance_stmt->close();

$conn->close(); // Close connection

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lecturer Dashboard - Statistics</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<div class="stats-section">
    <div class="stats">
        <h3>Statistics</h3>
        <ul class="list-group">
            <li class="list-group-item"><strong>Total Lessons:</strong> <?php echo $total_lessons; ?></li>
            <?php foreach ($lessons_data as $lesson_id => $data) { 
                $attendance_percentage = $data['total_attendance'] > 0 
                    ? ($data['total_present'] / $data['total_attendance']) * 100 
                    : 0;
            ?>
                <li class="list-group-item">
                    <strong><?php echo htmlspecialchars($data['title']); ?></strong><br>
                    Total Enrollments: <?php echo $data['total_enrollments']; ?><br>
                    Total Attendance Records: <?php echo $data['total_attendance']; ?><br>
                    Total Present: <?php echo $data['total_present']; ?><br>
                    Percentage Attendance: <?php echo number_format($attendance_percentage, 2); ?>%
                </li>
            <?php } ?>
        </ul>
    </div>
</div>
</body>
</html>

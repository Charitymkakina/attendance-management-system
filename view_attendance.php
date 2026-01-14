<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'lecturer') {
    header("Location: login.php");
    exit();
}

include('../config/db_connection.php');
$lesson_id = $_GET['lesson_id'];



// Fetch lesson details
$query = "SELECT * FROM lessons WHERE lesson_id = '$lesson_id'";
$lesson_result = mysqli_query($conn, $query);
$lesson = mysqli_fetch_assoc($lesson_result);

// Fetch enrolled students
$query = "SELECT students.registration_number AS student_id, students.username
          FROM students 
          JOIN enrollments ON students.registration_number = enrollments.student_id
          WHERE enrollments.lesson_id = '$lesson_id'";
$students_query = mysqli_query($conn, $query);

$students_data = [];
while ($student = mysqli_fetch_assoc($students_query)) {
    $student_id = $student['student_id'];
    
    // Calculate total attendances and attended lessons for each student
    $attendance_query = "SELECT COUNT(*) AS total_attended 
                         FROM attendance 
                         WHERE student_id = '$student_id' AND lesson_id = '$lesson_id' AND status = 'Present'";
    $attendance_result = mysqli_query($conn, $attendance_query);
    $attendance_data = mysqli_fetch_assoc($attendance_result);
    $total_attended = $attendance_data['total_attended'];

    // Count total lessons the student is enrolled in
    $enrollment_query = "SELECT COUNT(*) AS total_lessons FROM attendance WHERE student_id = '$student_id' AND lesson_id = '$lesson_id'";
    $enrollment_result = mysqli_query($conn, $enrollment_query);
    $enrollment_data = mysqli_fetch_assoc($enrollment_result);
    $total_lessons = $enrollment_data['total_lessons'];

    // Calculate attendance percentage
    $attendance_percentage = ($total_lessons > 0) ? round(($total_attended / $total_lessons) * 100, 2) : 0;

    $students_data[] = [
        'username' => $student['username'],
        'attendance_percentage' => $attendance_percentage,
        'total_lessons' => $total_lessons,
        'total_attended' => $total_attended
    ];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View attendance - BIDII School</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<?php include('header.php'); ?> 
    <div class="dashboard-container">
    <div class="cards">
        <h2><?php echo $lesson['title']; ?> attendance</h2>
        
        <table>
            <thead>
                <tr>
                    <th>Student Name</th>
                    <th>Total Lessons</th>
                    <th>Lessons Attended</th>
                    <th>Attendance Percentage</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($students_data as $student) { ?>
                    <tr>
                        <td><?php echo $student['username']; ?></td>
                        <td><?php echo $student['total_lessons']; ?></td>
                        <td><?php echo $student['total_attended']; ?></td>
                        <td><?php echo $student['attendance_percentage']; ?>%</td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
    <?php include('statistics.php'); ?> 
</body>
</html>

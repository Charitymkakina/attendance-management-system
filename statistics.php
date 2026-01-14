<?php

// Ensure the user is a lecturer and is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit();
}

// Fetching the lecturer's username based on user_id
include('../config/db_connection.php');
$lecturer_id = $_SESSION['user_id'];

// Fetch the total number of lessons for the logged-in lecturer
$total_lessons_query = "SELECT COUNT(*) FROM lessons";
$total_lessons_stmt = $conn->prepare($total_lessons_query);
$total_lessons_stmt->execute();
$total_lessons_stmt->bind_result($total_lessons);
$total_lessons_stmt->fetch();
$total_lessons_stmt->close();

// Fetch total enrollments for each lesson
$total_enrollments_query = "
    SELECT lessons.title, COUNT(enrollments.student_id) AS total_enrollments
    FROM lessons
    LEFT JOIN enrollments ON lessons.lesson_id = enrollments.lesson_id
    GROUP BY lessons.lesson_id";
$total_enrollments_stmt = $conn->prepare($total_enrollments_query);
$total_enrollments_stmt->execute();
$total_enrollments_result = $total_enrollments_stmt->get_result();

// Initialize arrays to store enrollments and attendance data for each lesson
$lessons_data = [];
while ($row = $total_enrollments_result->fetch_assoc()) {
    $lessons_data[$row['title']] = [
        'total_enrollments' => $row['total_enrollments'],
        'present_count' => 0,
        'total_attendance_records' => 0
    ];
}
$total_enrollments_stmt->close();

// Fetch attendance data for each lesson
$attendance_query = "
    SELECT lessons.title, 
           SUM(CASE WHEN attendance.status = 'present' THEN 1 ELSE 0 END) AS present_count,
           COUNT(attendance.student_id) AS total_attendance_records
    FROM lessons
    LEFT JOIN attendance ON lessons.lesson_id = attendance.lesson_id
    GROUP BY lessons.lesson_id";
$attendance_stmt = $conn->prepare($attendance_query);
$attendance_stmt->execute();
$attendance_result = $attendance_stmt->get_result();

// Update the attendance counts
while ($row = $attendance_result->fetch_assoc()) {
    $lessons_data[$row['title']]['present_count'] = $row['present_count'];
    $lessons_data[$row['title']]['total_attendance_records'] = $row['total_attendance_records'];
}
$attendance_stmt->close();

$conn->close();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lecturer Dashboard - Statistics</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .print-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .print-table th, .print-table td {
            border: 2px solid #000;
            padding: 10px;
            text-align: left;
        }
        .print-table th {
            background-color: #f4f4f4;
        }
        .print-button {
            margin: 20px 0;
        }
        .print-button button {
            padding: 10px 20px;
            background-color: #007bff;
            color: #fff;
            border: none;
            cursor: pointer;
            border-radius: 5px;
        }
        .print-button button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>

<div class="stats-section">
    <div class="stats">
        <h3>Statistics</h3>
        <div class="print-button">
    <button onclick="printReport()">Print Report</button>
    </div>
        <ul class="list-group">
            <li class="list-group-item"><strong>Total Lessons:</strong> <?php echo $total_lessons; ?></li>
            <?php foreach ($lessons_data as $title => $data) { 
                $attendance_percentage = $data['total_attendance_records'] > 0 
                    ? ($data['present_count'] / $data['total_attendance_records']) * 100 
                    : 0;
            ?>
                <li class="list-group-item">
                    <strong><?php echo htmlspecialchars($title); ?></strong><br>
                    Total Enrollments: <?php echo $data['total_enrollments']; ?><br>
                    Present: <?php echo $data['present_count']; ?><br>
                    Total Attendance Records: <?php echo $data['total_attendance_records']; ?><br>
                    Attendance Percentage: <?php echo number_format($attendance_percentage, 2); ?>%
                </li>
            <?php } ?>
        </ul>
    </div>
</div>



<script>
function printReport() {
    // Create a new window for printing
    const printWindow = window.open('', '_blank');
    const content = `
        <html>
        <head>
            <title>Print Report</title>
            <style>
                body { font-family: Arial, sans-serif; margin: 20px; }
                .print-table {
                    width: 100%;
                    border-collapse: collapse;
                    margin-top: 20px;
                }
                .print-table th, .print-table td {
                    border: 1px solid #000;
                    padding: 10px;
                    text-align: left;
                }
                .print-table th {
                    background-color: #f4f4f4;
                }
            </style>
        </head>
        <body>
            <h3>Complete Statistics Report</h3>
            <p><strong>Total Lessons:</strong> <?php echo $total_lessons; ?></p>
            
            <table class="print-table">
                <thead>
                    <tr>
                        <th>Lesson Title</th>
                        <th>Total Enrollments</th>
                        <th>Present Count</th>
                        <th>Total Attendance Records</th>
                        <th>Attendance Percentage</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($lessons_data as $title => $data) {
                        $attendance_percentage = $data['total_attendance_records'] > 0 
                            ? ($data['present_count'] / $data['total_attendance_records']) * 100 
                            : 0;
                    ?>
                    <tr>
                        <td><?php echo htmlspecialchars($title); ?></td>
                        <td><?php echo $data['total_enrollments']; ?></td>
                        <td><?php echo $data['present_count']; ?></td>
                        <td><?php echo $data['total_attendance_records']; ?></td>
                        <td><?php echo number_format($attendance_percentage, 2); ?>%</td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </body>
        </html>`;

    // Write content to the new window
    printWindow.document.write(content);
    printWindow.document.close();
    printWindow.focus();
    // Wait for the content to load, then print
    printWindow.print();
    printWindow.close();
}
</script>

</body>
</html>

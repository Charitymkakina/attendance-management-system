<?php
session_start();

// Ensure the user is a lecturer and is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit();
}

// Include the database connection
include('../config/db_connection.php');
$lecturer_id = $_SESSION['user_id'];

$query = "SELECT username FROM users WHERE id = ?";
$stmt = $conn->prepare($query);

if (!$stmt) {
    die("Error preparing the query: " . $conn->error);
}

$stmt->bind_param("i", $admin_id);
$stmt->execute();
$stmt->bind_result($admin_username);
$stmt->fetch();
$stmt->close(); // Close after fetching the username


// Check if lesson_id is provided in the query string
if (!isset($_GET['lesson_id']) || empty($_GET['lesson_id'])) {
    echo "Invalid lesson ID.";
    exit();
}

$lesson_id = $_GET['lesson_id'];


// Prepare a SQL query to verify that the lesson belongs to the logged-in lecturer
$query = "SELECT title FROM lessons WHERE lesson_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $lesson_id);
$stmt->execute();
$result = $stmt->get_result();

// Check if the lesson exists
if ($result->num_rows === 0) {
    echo "Lesson not found or you don't have permission to view this lesson.";
    exit();
}

// Fetch the lesson title
$lesson = $result->fetch_assoc();
$lesson_title = $lesson['title'];

// Fetch enrolled students for this lesson
$query = "
    SELECT students.*
    FROM enrollments
    JOIN students ON enrollments.student_id = students.registration_number
    WHERE enrollments.lesson_id = ?
";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $lesson_id);
$stmt->execute();
$students_result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Enrollment - BIDII School</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<?php include('header.php'); ?> 
        <div class="dashboard-container">
        <div class="cards">

        <h2><?php echo htmlspecialchars($lesson_title); ?> Enrollments</h2>

        <?php if ($students_result->num_rows > 0) { ?>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Registration Number</th>
                        <th>Student Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($student = $students_result->fetch_assoc()) { ?>
                        <tr>
                            <td><?php echo htmlspecialchars($student['registration_number']); ?></td>
                            <td><?php echo htmlspecialchars($student['username']); ?></td>
                            <td><?php echo htmlspecialchars($student['email']); ?></td>
                            <td><?php echo htmlspecialchars($student['phone']); ?></td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        <?php } else { ?>
            <p>No students are enrolled in this lesson.</p>
        <?php } ?>


    </div>
    <?php include('statistics.php'); ?> 
    </div>
</body>
</html>



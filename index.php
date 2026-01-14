<?php
session_start();

// Ensure the user is a lecturer and is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'lecturer') {
    header("Location: login.php");
    exit();
}

// Fetching the lecturer's username based on user_id
include('../config/db_connection.php');
$lecturer_id = $_SESSION['user_id'];

// Secure SQL query to fetch the username for the logged-in lecturer
$query = "SELECT username FROM users WHERE id = ?";
$stmt = $conn->prepare($query);

if (!$stmt) {
    die("Error preparing the query: " . $conn->error);
}

$stmt->bind_param("i", $lecturer_id);
$stmt->execute();
$stmt->bind_result($lecturer_username);
$stmt->fetch();
$stmt->close(); // Close after fetching the username

// Fetch lessons associated with the logged-in lecturer
$query = "SELECT * FROM lessons WHERE lecturer_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $lecturer_id);
$stmt->execute();
$lessons_result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lecturer Dashboard - BIDII School</title>
    <script src="https://kit.fontawesome.com/4f5347b228.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <link rel="stylesheet" href="css/style.css">
    <style>
.description-overlay {
            display: none;
            position: absolute;
            width: 250px;
            height: auto;
            background-color: rgba(0, 0, 0, 0.692);
            margin-left: 0px;
            margin-top: -50px;
            color: #fff;
            border: none;
            border-radius: 5px;
            padding: 5px;
            z-index: 100;
            overflow-y: auto;
        }
        .lesson-card {
            display: block;
            margin-bottom: 15px;
        }
        .searchbox{
            width: 35%;
            margin-left: 63%;
        }
    </style>
</head>
<body>
    
<?php include('header.php'); ?> 

<div class="dashboard-container">    
    <div class="cards">
        <h2>Your Lessons</h2>
        <!-- Search Bar -->
        <div class="searchbox">
        <input type="text" id="searchInput" class="searchbox" placeholder="Search lessons..." style="border: solid 2px #1794a5;">
        </div>
        <?php if ($lessons_result->num_rows > 0) { 
            while ($lesson = $lessons_result->fetch_assoc()) { ?>
                <div class="card bg-info bg-gradient position-relative lesson-card">
                    <div class="card-header">
                        <strong class="lesson-title"><?php echo htmlspecialchars($lesson['title']); ?></strong>
                    </div>
                    <p class="description" data-full-description="<?php echo htmlspecialchars($lesson['description']); ?>">
                        <?php echo htmlspecialchars(substr($lesson['description'], 0, 20)) . (strlen($lesson['description']) > 50 ? "..." : ""); ?>
                    </p>
                    <div class="description-overlay"></div>
                    <p><strong>Enrollment Key:</strong> <?php echo htmlspecialchars($lesson['enrollment_key']); ?></p>
                    <div class="lesson-actions">
                        <a href="edit_lesson.php?lesson_id=<?php echo $lesson['lesson_id']; ?>" class="edit-btn btn-warning"><i class="fas fa-pencil"></i></a>
                        <a href="delete_lesson.php?lesson_id=<?php echo $lesson['lesson_id']; ?>" class="delete-btn btn-danger"><i class="fas fa-trash"></i></a>
                    </div>
                    <div class="att">
                        <a href="view_attendance.php?lesson_id=<?php echo $lesson['lesson_id']; ?>" class="btn btn-primary view-attendance-btn">Attendance</a>
                        <a href="view_enrollment.php?lesson_id=<?php echo $lesson['lesson_id']; ?>" class="btn btn-secondary view-enrollment-btn">Enrollment</a>
                    </div>
                </div>
            <?php }
        } else { ?>
            <p>No lessons found. <a href="add_lesson.php">Create a new lesson</a></p>
        <?php } ?>
    </div>
   
    <?php include('statistics.php'); ?>         
</div>

<script src="../assets/js/jquery-3.7.1.min.js"></script>
<script>
// JavaScript for handling overlay on hover
$(document).ready(function() {
    $('.description').hover(function() {
        var fullDescription = $(this).data('full-description');
        $(this).next('.description-overlay').html(fullDescription).show();
    }, function() {
        $(this).next('.description-overlay').hide();
    });

        // Dynamic search functionality
    $('#searchInput').on('input', function() {
        var searchText = $(this).val().toLowerCase();
        $('.lesson-card').each(function() {
            var lessonTitle = $(this).find('.lesson-title').text().toLowerCase();
            if (lessonTitle.includes(searchText)) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
    });
});
</script>

<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
</body>
</html>

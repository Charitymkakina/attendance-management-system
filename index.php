<?php
session_start();

// Ensure the user is a student and is logged in
if (!isset($_SESSION['registration_number'])) {
    header("Location: login.php");
    exit();
}

// Database connection
include('../config/db_connection.php');

// Get logged-in student's registration number
$studentId = $_SESSION['registration_number'];

// Fetch all available lessons with lecturer names
$query = "SELECT lessons.lesson_id, lessons.title, lessons.description, users.username AS lecturer_name 
          FROM lessons 
          JOIN users ON lessons.lecturer_id = users.id";
$lessons_result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard - BIDII School</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/style.css">
    <script src="../assets/js/jquery-3.6.0.min.js"></script>

    <style>
        .description-overlay {
            display: none;
            position: absolute;
            width: 250px;
            height: auto;
            background-color: rgba(0, 0, 0, 0.692);
            margin-left: -20px;
            margin-top: -30px;
            color: #fff;
            border: none;
            border-radius: 5px;
            padding: 5px;
            z-index: 100;
            overflow-y: auto;
        }
        /* Circle and Checkmark animation */


        .success-text {
            margin-top: 20px;
            font-size: 1.2em;
            color: #333;
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
        
    <h2>Available Lessons</h2>
    <!-- Search Bar -->
    <div class="searchbox">
        <input type="text" id="searchInput" class="searchbox" placeholder="Search lessons..." style="border: solid 2px #1794a5;">
    </div>
    <div class="row">
        <?php while ($lesson = $lessons_result->fetch_assoc()) { 
            $lessonId = $lesson['lesson_id'];
            $isEnrolled = $conn->query("SELECT * FROM enrollments WHERE student_id = '$studentId' AND lesson_id = '$lessonId'")->num_rows > 0;
        ?>
            <div class="col-md-4">
                <div class="card bg-info text-white mb-4 position-relative lesson-card">
                    <div class="card-header">
                        <strong class="lesson-title"><?php echo htmlspecialchars($lesson['title']); ?></strong>
                    </div>
                    <div class="card-body">
                        <p class="description" data-full-description="<?php echo htmlspecialchars($lesson['description']); ?>">
                            <?php echo htmlspecialchars(substr($lesson['description'], 0, 20)) . (strlen($lesson['description']) > 50 ? "..." : ""); ?>
                        </p>
                        <div class="description-overlay"></div>
                        <p><strong>Lecturer:</strong> <?php echo htmlspecialchars($lesson['lecturer_name']); ?></p>
                        <div class="text-center">
                            <?php if ($isEnrolled) { ?>
                                <div id="lesson-<?php echo $lessonId; ?>-status" class="mt-2">
                                    <button class="btn btn-success mark-attendance-btn" data-lesson-id="<?php echo $lessonId; ?>">
                                        Mark Attendance
                                    </button>
                                </div>
                            <?php } else { ?>
                                <button class="btn btn-primary enroll-btn" data-lesson-id="<?php echo $lessonId; ?>">
                                    Enroll
                                </button>
                                <div id="lesson-<?php echo $lessonId; ?>-status" class="mt-2"></div>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php } ?>
    </div>
</div>

<!-- Enrollment Key Modal -->
<div class="modal fade" id="enrollModal" tabindex="-1" role="dialog" aria-labelledby="enrollModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="enrollModalLabel">Enter Enrollment Key</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="enrollForm">
                <div class="modal-body">
                    <input type="hidden" name="lesson_id" id="lesson_id">
                    <div class="form-group">
                        <label for="enrollment_key">Enrollment Key</label>
                        <input type="text" class="form-control" id="enrollment_key" name="enrollment_key" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Enroll</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Attendance Success Modal -->
<div class="modal fade" id="attendanceSuccessModal" tabindex="-1" role="dialog" aria-labelledby="attendanceSuccessModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="attendanceSuccessModalLabel">Attendance Marked</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- GIF for Circle and Tick -->
                <div class="text-center">
                    <img src="../assets/images/tick.gif" alt="Success" width="100" height="100"/>
                </div>
                <div class="success-text">Attendance marked successfully!</div>
            </div>
        </div>
    </div>
</div>

<?php include('statistics.php'); ?>
<script src="../assets/js/jquery-3.6.0.min.js"></script>

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

    // Open modal and set lesson ID for enrollment
    $('.enroll-btn').click(function() {
        const lessonId = $(this).data('lesson-id');
        $('#lesson_id').val(lessonId);
        $('#enrollModal').modal('show');
    });

    // Enrollment form submission
    $('#enrollForm').submit(function(e) {
        e.preventDefault();
        
        const lessonId = $('#lesson_id').val();
        const enrollmentKey = $('#enrollment_key').val();
        
        // Send AJAX request to enroll
        $.post("enroll.php", { lesson_id: lessonId, enrollment_key: enrollmentKey }, function(data) {
            if (data === 'success') {
                $('#enrollModal').modal('hide');
                $(`#lesson-${lessonId}-status`).html('<p class="text-success">Enrolled</p>');
                addAttendanceButtonListener(lessonId);
                window.location.href = "index.php"; 
            } else {
                alert("Enrollment failed: " + data);
            }
        });
    });
    $('.mark-attendance-btn').click(function() {
        const lessonId = $(this).data('lesson-id');
        
        $.post("mark_attendance.php", { lesson_id: lessonId }, function(data) {
            if (data === 'success') {
                // Show the modal
                $('#attendanceSuccessModal').modal('show');
                
                // Update the lesson status after successful attendance
                $(`#lesson-${lessonId}-status`).html('<p class="text-success">Attendance marked for today</p>');
                
                // Close the modal after 3 seconds (optional)
                setTimeout(function() {
                    $('#attendanceSuccessModal').modal('hide');
                    window.location.href = 'index.php';
                }, 1000);
            } else {
                alert("Failed to mark attendance: " + data);
            }
        });
    });

    // Function to handle Mark Attendance button clicks
    function addAttendanceButtonListener(lessonId) {
        $(`#lesson-${lessonId}-status`).html(`
            <button class="btn btn-success mark-attendance-btn" data-lesson-id="${lessonId}">
                Mark Attendance
            </button>
        `);
    }
});
</script>

<script src="../assets/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>

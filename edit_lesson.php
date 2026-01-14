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

    // Fetch lesson details
    $query = "SELECT * FROM lessons WHERE lesson_id = ? AND lecturer_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $lesson_id, $_SESSION['user_id']); // Ensure the lesson belongs to the logged-in lecturer
    $stmt->execute();
    $lesson_result = $stmt->get_result();

    if ($lesson_result->num_rows > 0) {
        $lesson = $lesson_result->fetch_assoc();
    } else {
        // Lesson not found or unauthorized access
        header("Location: index.php");
        exit();
    }
    
    // If form is submitted, update the lesson
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $title = trim($_POST['title']);
        $description = trim($_POST['description']);
        $enrollment_key = trim($_POST['enrollment_key']);
        
        $update_query = "UPDATE lessons SET title = ?, description = ?, enrollment_key = ? WHERE lesson_id = ? AND lecturer_id = ?";
        $update_stmt = $conn->prepare($update_query);
        $update_stmt->bind_param("ssssi", $title, $description, $enrollment_key, $lesson_id, $_SESSION['user_id']);
        
        if ($update_stmt->execute()) {
            header("Location: index.php"); // Redirect after successful update
            exit();
        } else {
            $error = "Error updating lesson! Please try again.";
        }
    }

    $stmt->close();
} else {
    // Redirect if no lesson ID is provided
    header("Location: index.php");
    exit();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Lesson - BIDII School</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<?php include('header.php'); ?> 
<div class="dashboard-container">
    <div class="cards">
        <h2>Edit Lesson</h2>
        <?php if (isset($error)) { echo "<p class='error'>$error</p>"; } ?>
        
        <form action="edit_lesson.php?lesson_id=<?php echo $lesson['lesson_id']; ?>" method="POST">
            <label for="title">Lesson Title</label>
            <input type="text" name="title" id="title" value="<?php echo htmlspecialchars($lesson['title']); ?>" required>

            <label for="description">Description</label>
            <textarea name="description" id="description" required><?php echo htmlspecialchars($lesson['description']); ?></textarea>

            <label for="enrollment_key">Enrollment Key</label>
            <input type="text" name="enrollment_key" id="enrollment_key" value="<?php echo htmlspecialchars($lesson['enrollment_key']); ?>" required>

            <button type="submit">Update Lesson</button>
        </form>
    </div>
    <?php include('statistics.php'); ?>
</div>
</body>
</html>

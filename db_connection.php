<?php
// db_connection.php

$servername = "localhost";
$dbusername = "root"; // default XAMPP username
$dbpassword = ""; // default XAMPP password
$dbname = "BIDII_school";

// Create connection
$conn = new mysqli($servername, $dbusername, $dbpassword, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>

<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Include database connection
require_once('db_connection.php');

// Prepare and execute the query to check if the user is an admin
$user_id = $_SESSION['user_id'];
$query = "SELECT is_admin FROM UserInformation WHERE unique_user_ID = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

// Check if the user has administrative privileges
if (!$row || $row['is_admin'] != 1) {
    header("Location: welcome.php");
    exit();
}
?>

<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Include database connection
require_once('db_connection.php');

// Check if user already has a CheckingSavingsAccount
$query = "SELECT COUNT(*) AS count FROM CheckingSavingsAccount WHERE user_ID = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

// If user already has an account, redirect back to welcome page
if ($row['count'] > 0) {
    header("Location: welcome.php");
    exit();
}

// If user doesn't have an account, create one
$query = "INSERT INTO CheckingSavingsAccount (user_ID, acct_ID) VALUES (?, ?)";
$stmt = $conn->prepare($query);
$stmt->bind_param("ss", $_SESSION['user_id'], $acct_id);
$acct_id = uniqid(); // Generate a unique account ID
$stmt->execute();

// Redirect back to welcome page
header("Location: welcome.php");
exit();
?>

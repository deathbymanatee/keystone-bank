<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Include database connection
require_once('db_connection.php');

// Include navbar
require_once('navbar.php');

// Get user's information
$user_id = $_SESSION['user_id'];
$query = "SELECT * FROM UserInformation WHERE unique_user_ID = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user_info = $result->fetch_assoc();

// Check if user is admin
$is_admin = $user_info['is_admin'];

// If user is not admin and trying to view someone else's profile, redirect to welcome.php
if (!$is_admin && $user_id != $_GET['user_id']) {
    header("Location: welcome.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Profile</title>
</head>
<body>
    <h1>View Profile</h1>
    <p><strong>Email:</strong> <?php echo $user_info['email']; ?></p>
    <p><strong>First Name:</strong> <?php echo $user_info['first_name']; ?></p>
    <p><strong>Last Name:</strong> <?php echo $user_info['last_name']; ?></p>
    <p><strong>SSN:</strong> <?php echo $user_info['SSN']; ?></p>
    
    <?php if ($is_admin || $user_id == $_GET['user_id']): ?>
    <p><a href="edit_profile.php">Edit Profile</a></p>
    <?php endif; ?>
    
    <p><a href="welcome.php">Go Back</a></p>
</body>
</html>

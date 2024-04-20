<?php
session_start();

require_once('auth_check.php');

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
    
    <p><a href="edit_profile.php">Edit Profile</a></p>
    
    <p><a href="welcome.php">Go Back</a></p>
</body>
</html>

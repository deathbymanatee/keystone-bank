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

// Update profile information if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = htmlspecialchars($_POST['email']);
    $firstName = htmlspecialchars($_POST['first_name']);
    $lastName = htmlspecialchars($_POST['last_name']);
    $password = htmlspecialchars($_POST['password']);

    // Check if password is provided and update password if necessary
    if (!empty($password)) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $updatePasswordQuery = "UPDATE UserInformation SET password = ? WHERE unique_user_ID = ?";
        $stmt = $conn->prepare($updatePasswordQuery);
        $stmt->bind_param("ss", $hashedPassword, $user_id);
        $stmt->execute();
    }

    // Update profile information
    $updateQuery = "UPDATE UserInformation SET email = ?, first_name = ?, last_name = ? WHERE unique_user_ID = ?";
    $stmt = $conn->prepare($updateQuery);
    $stmt->bind_param("ssss", $email, $firstName, $lastName, $user_id);
    $stmt->execute();

    // Redirect back to view_profile.php after updating profile
    header("Location: view_profile.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Profile</title>
</head>
<body>
    <h1>Edit Profile</h1>
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" value="<?php echo $user_info['email']; ?>" required><br>
        <label for="first_name">First Name:</label>
        <input type="text" id="first_name" name="first_name" value="<?php echo $user_info['first_name']; ?>" required><br>
        <label for="last_name">Last Name:</label>
        <input type="text" id="last_name" name="last_name" value="<?php echo $user_info['last_name']; ?>" required><br>
        <label for="password">New Password:</label>
        <input type="password" id="password" name="password"><br>
        <input type="submit" value="Save Changes">
    </form>
    <p><a href="view_profile.php">Cancel</a></p>
</body>
</html>

<?php

session_start();

// Check if user is already logged in
if (isset($_SESSION['user_id'])) {
    // Redirect to the welcome page if the user is already logged in
    header("Location: welcome.php");
    exit(); // Don't forget to call exit() after header().
}

$errorMessage = '';

require_once('db_connection.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = htmlspecialchars($_POST['email']);
    $password = htmlspecialchars($_POST['password']);
    
    $sql = "SELECT unique_user_ID, password FROM UserInformation WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->bind_result($userID, $hashedPassword);
    
    if ($stmt->fetch() && password_verify($password, $hashedPassword)) {
        $_SESSION['user_id'] = $userID;
        header("Location: welcome.php");
        exit;
    } else {
        $errorMessage = 'Invalid email or password.';
    }
    
    $stmt->close();
    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
</head>
<body>
    <h1>Login</h1>
    <form method="post" action="login.php">
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required><br>
        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required><br>
        <input type="submit" value="Login">
        <?php if ($errorMessage) { echo "<p>$errorMessage</p>"; } ?>
    </form>
    <p>Don't have an account? <a href="signup.php">Sign up here</a></p>
</body>
</html>

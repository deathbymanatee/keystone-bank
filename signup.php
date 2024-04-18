<?php

require_once('db_connection.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = htmlspecialchars($_POST['email']);
    $firstName = htmlspecialchars($_POST['first_name']);
    $lastName = htmlspecialchars($_POST['last_name']);
    $ssn = htmlspecialchars($_POST['SSN']);
    $password = htmlspecialchars($_POST['password']);
    $confirmPassword = htmlspecialchars($_POST['confirm_password']);
    $is_admin = isset($_POST['is_admin']) ? 1 : 0;
    
    if (!preg_match("/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/", $email)) {
        $errorMessage = "Invalid email format.";
    } elseif (!preg_match("/^(?=.*\d)(?=.*[a-zA-Z])(?=.*[^\w\d\s:])(\S{8,})$/", $password)) {
        $errorMessage = "Password must be at least 8 characters long, include a number, a special character, and an uppercase letter.";
    } elseif ($password !== $confirmPassword) {
        $errorMessage = "Passwords do not match.";
    } elseif (!preg_match("/^[0-9]{9}$/", $ssn)) {
        $errorMessage = "SSN must be exactly 9 digits.";
    } else {
        // Database connection
        $conn = new mysqli("localhost", "root", "", "keystone");
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        // Check if email already exists
        $checkEmail = $conn->prepare("SELECT email FROM UserInformation WHERE email = ?");
        $checkEmail->bind_param("s", $email);
        $checkEmail->execute();
        if ($checkEmail->fetch()) {
            $errorMessage = "Email already in use.";
            $checkEmail->close();
        } else {
            $checkEmail->close();
            // Insert the new user
            $sql = "INSERT INTO UserInformation (unique_user_ID, email, first_name, last_name, SSN, password, is_admin) VALUES (UUID(), ?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $stmt->bind_param("sssssi", $email, $firstName, $lastName, $ssn, $hashedPassword, $is_admin);
            if ($stmt->execute()) {
                header("Location: login.php");
                exit;
            } else {
                $errorMessage = "Error registering user.";
            }
            $stmt->close();
        }
        $conn->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sign Up</title>
</head>
<body>
    <h1>Sign Up</h1>
    <form method="post" action="signup.php">
        <label for="first_name">First Name:</label>
        <input type="text" id="first_name" name="first_name" required><br>
        <label for="last_name">Last Name:</label>
        <input type="text" id="last_name" name="last_name" required><br>
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required><br>
        <label for="SSN">SSN:</label>
        <input type="text" id="SSN" name="SSN" required><br>
        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required><br>
        <label for="confirm_password">Confirm Password:</label>
        <input type="password" id="confirm_password" name="confirm_password" required><br>
        <label for="is_admin">Admin:</label>
        <input type="checkbox" id="is_admin" name="is_admin"><br>
        <input type="submit" value="Sign Up">
        <?php if (isset($errorMessage)) { echo "<p>$errorMessage</p>"; } ?>
    </form>
</body>
</html>

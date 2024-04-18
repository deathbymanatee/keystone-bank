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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Process form data when form is submitted
    $amount = $_POST['amount'];
    $user_id = $_SESSION['user_id'];

    // Check if sufficient balance is available
    $query = "SELECT savings_balance FROM CheckingSavingsAccount WHERE user_ID = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    if ($row['savings_balance'] >= $amount) {
        // Update savings balance
        $query = "UPDATE CheckingSavingsAccount SET savings_balance = savings_balance - ? WHERE user_ID = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ds", $amount, $user_id);
        $stmt->execute();

        // Redirect to welcome page
        header("Location: welcome.php");
        exit();
    } else {
        $error = "Insufficient balance!";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Withdraw from Savings Account</title>
</head>
<body>
    <h1>Withdraw from Savings Account</h1>
    <?php
    if (isset($error)) {
        echo "<p>$error</p>";
    }
    ?>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
        Amount: <input type="text" name="amount" required><br>
        <input type="submit" value="Withdraw">
    </form>
    <button onclick="window.location.href='welcome.php'">Go Back</button>
</body>
</html>

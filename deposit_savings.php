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
    $amount = filter_input(INPUT_POST, 'amount', FILTER_VALIDATE_INT);
    $user_id = $_SESSION['user_id'];


    if ($amount === false || $amount === null) {
        $error_message = "Amount must be an integer.";
    } else {
		// Update savings balance
	    $query = "UPDATE CheckingSavingsAccount SET savings_balance = savings_balance + ? WHERE user_ID = ?";
	    $stmt = $conn->prepare($query);
	    $stmt->bind_param("ds", $amount, $user_id);
	    $stmt->execute();
	}

    // Redirect to welcome page
    header("Location: welcome.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Deposit to Savings Account</title>
</head>
<body>
    <h1>Deposit to Savings Account</h1>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
        Amount: <input type="text" name="amount" required><br>
        <input type="submit" value="Deposit">
    </form>
    <button onclick="window.location.href='welcome.php'">Go Back</button>
</body>
</html>

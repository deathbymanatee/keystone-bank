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

// Initialize variables
$error_message = "";

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Process form data when form is submitted
    $amount = filter_input(INPUT_POST, 'amount', FILTER_VALIDATE_INT);
    $user_id = $_SESSION['user_id'];
    $transaction_type = $_POST['transaction_type'];

    if ($amount === false || $amount === null) {
        $error_message = "Amount must be an integer.";
    } else {
        // Perform transaction based on transaction type
        switch ($transaction_type) {
            case 'deposit_checking':
                $query = "UPDATE CheckingSavingsAccount SET checking_balance = checking_balance + ? WHERE user_ID = ?";
                break;
            case 'withdraw_checking':
                $query = "SELECT checking_balance FROM CheckingSavingsAccount WHERE user_ID = ?";
                break;
            case 'deposit_savings':
                $query = "UPDATE CheckingSavingsAccount SET savings_balance = savings_balance + ? WHERE user_ID = ?";
                break;
            case 'withdraw_savings':
                $query = "SELECT savings_balance FROM CheckingSavingsAccount WHERE user_ID = ?";
                break;
            default:
                // Invalid transaction type
                exit("Invalid transaction type");
        }

        if (strpos($transaction_type, 'withdraw') !== false) {
            // For withdrawal, check if sufficient balance
            $stmt = $conn->prepare($query);
            $stmt->bind_param("s", $user_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();

            if ($row[$transaction_type] < $amount) {
                $error_message = "Insufficient balance!";
            } else {
                // Update balance for withdrawal
                $query = "UPDATE CheckingSavingsAccount SET " . str_replace('withdraw_', '', $transaction_type) . " = " . str_replace('withdraw_', '', $transaction_type) . " - ? WHERE user_ID = ?";
                $stmt = $conn->prepare($query);
                $stmt->bind_param("ds", $amount, $user_id);
                $stmt->execute();
            }
        } else {
            // For deposit, directly update balance
            $stmt = $conn->prepare($query);
            $stmt->bind_param("ds", $amount, $user_id);
            $stmt->execute();
        }
    }
}

// Redirect to welcome page
header("Location: welcome.php");
exit();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Transaction</title>
</head>
<body>
    <h1>Perform Transaction</h1>
    <?php if (!empty($error_message)) { ?>
        <p><?php echo $error_message; ?></p>
    <?php } ?>
    <form action="consolidated_transaction.php" method="post">
        <input type="hidden" name="transaction_type" value="deposit_checking">
        <label for="amount">Amount:</label>
        <input type="number" id="amount" name="amount" required><br>
        <input type="submit" value="Deposit to Checking Account">
    </form>
    <form action="consolidated_transaction.php" method="post">
        <input type="hidden" name="transaction_type" value="withdraw_checking">
        <label for="amount">Amount:</label>
        <input type="number" id="amount" name="amount" required><br>
        <input type="submit" value="Withdraw from Checking Account">
    </form>
    <form action="consolidated_transaction.php" method="post">
        <input type="hidden" name="transaction_type" value="deposit_savings">
        <label for="amount">Amount:</label>
        <input type="number" id="amount" name="amount" required><br>
        <input type="submit" value="Deposit to Savings Account">
    </form>
    <form action="consolidated_transaction.php" method="post">
        <input type="hidden" name="transaction_type" value="withdraw_savings">
        <label for="amount">Amount:</label>
        <input type="number" id="amount" name="amount" required><br>
        <input type="submit" value="Withdraw from Savings Account">
    </form>
    <button onclick="window.location.href='welcome.php'">Go Back</button>
</body>
</html>

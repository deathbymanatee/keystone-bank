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

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $amount = $_POST['amount'];
    $receiver_email = $_POST['receiver_email'];

    // Get sender's user ID
    $sender_id = $_SESSION['user_id'];

    // Get sender's account ID
    $sender_account_query = "SELECT acct_ID FROM CheckingSavingsAccount WHERE user_ID = ?";
    $stmt = $conn->prepare($sender_account_query);
    $stmt->bind_param("s", $sender_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $sender_account = $result->fetch_assoc()['acct_ID'];

    // Get receiver's user ID
    $receiver_query = "SELECT unique_user_ID FROM UserInformation WHERE email = ?";
    $stmt = $conn->prepare($receiver_query);
    $stmt->bind_param("s", $receiver_email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $receiver_id = $result->fetch_assoc()['unique_user_ID'];

        // Get receiver's account ID
        $receiver_account_query = "SELECT acct_ID FROM CheckingSavingsAccount WHERE user_ID = ?";
        $stmt = $conn->prepare($receiver_account_query);
        $stmt->bind_param("s", $receiver_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $receiver_account = $result->fetch_assoc()['acct_ID'];

            // Check if sender has sufficient balance
            $sender_balance_query = "SELECT checking_balance FROM CheckingSavingsAccount WHERE user_ID = ?";
            $stmt = $conn->prepare($sender_balance_query);
            $stmt->bind_param("s", $sender_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $sender_balance = $result->fetch_assoc()['checking_balance'];

            if ($sender_balance >= $amount) {
                // Perform transaction
                $sender_new_balance = $sender_balance - $amount;
                $receiver_new_balance = $amount;

                // Update sender's balance
                $update_sender_query = "UPDATE CheckingSavingsAccount SET checking_balance = ? WHERE user_ID = ?";
                $stmt = $conn->prepare($update_sender_query);
                $stmt->bind_param("ds", $sender_new_balance, $sender_id);
                $stmt->execute();

                // Update receiver's balance
                $update_receiver_query = "UPDATE CheckingSavingsAccount SET checking_balance = checking_balance + ? WHERE user_ID = ?";
                $stmt = $conn->prepare($update_receiver_query);
                $stmt->bind_param("ds", $receiver_new_balance, $receiver_id);
                $stmt->execute();

                // Insert into UserTransactionTable
                $insert_transaction_query = "INSERT INTO UserTransactionTable (send_acctID, receive_acctID, amount_transferred, transaction_date) VALUES (?, ?, ?, CURDATE())";
                $stmt = $conn->prepare($insert_transaction_query);
                $stmt->bind_param("sss", $sender_account, $receiver_account, $amount);
                $stmt->execute();

                // Redirect to welcome page
                header("Location: welcome.php");
                exit();
            } else {
                $error_message = "Insufficient balance.";
            }
        } else {
            $error_message = "Receiver account not found.";
        }
    } else {
        $error_message = "Receiver not found.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Payment</title>
</head>
<body>
    <h1>Payment</h1>
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
        <label for="receiver_email">Receiver's Email:</label>
        <input type="email" id="receiver_email" name="receiver_email" required><br>
        <label for="amount">Amount:</label>
        <input type="text" id="amount" name="amount" required><br>
        <input type="submit" value="Send Payment">
    </form>
    <?php if (isset($error_message)) { echo "<p>$error_message</p>"; } ?>
    <button onclick="window.location.href='welcome.php'">Go Back</button>
</body>
</html>

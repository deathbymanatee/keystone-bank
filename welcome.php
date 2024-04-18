<!DOCTYPE html>
<html>
<head>
    <title>Welcome</title>
</head>
    <?php
    session_start();

    // Check if user is logged in
    if (!isset($_SESSION['user_id'])) {
        header("Location: login.php");
        exit();
    }

    // Include database connection
    require_once('db_connection.php');

    // Include navigation bar
    require_once('navbar.php');
    ?>

<body>
	<h1>Welcome</h1>
	<?php
    // Function to get user account type
    function getUserType($conn, $userId) {
        $query = "SELECT is_admin FROM UserInformation WHERE unique_user_ID = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        return $row['is_admin'] ? 'admin' : 'standard';
    }

    // Get user type
    $userType = getUserType($conn, $_SESSION['user_id']);

    // Display appropriate content based on user type
    if ($userType === 'standard') {
        // Check if CheckingSavingsAccount exists for the user
        $query = "SELECT COUNT(*) AS count FROM CheckingSavingsAccount WHERE user_ID = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $_SESSION['user_id']);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();

        if ($row['count'] == 0) {
            // If no CheckingSavingsAccount exists, display create account button
            echo '<form action="create_account.php" method="post">';
            echo '<input type="submit" value="Create Checking / Savings Account">';
            echo '</form>';
        } else {
            // If CheckingSavingsAccount exists, display balance and transaction buttons
            $query = "SELECT checking_balance, savings_balance FROM CheckingSavingsAccount WHERE user_ID = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("s", $_SESSION['user_id']);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();

            echo '<h2>Checking Account Balance: $' . $row['checking_balance'] . '</h2>';
            echo '<h2>Savings Account Balance: $' . $row['savings_balance'] . '</h2>';

            // Display transaction buttons
            echo '<button onclick="window.location.href=\'deposit_checking.php\'">Deposit to Checking Account</button>';
            echo '<button onclick="window.location.href=\'deposit_savings.php\'">Deposit to Savings Account</button>';
            if ($row['checking_balance'] > 0) {
                echo '<button onclick="window.location.href=\'withdraw_checking.php\'">Withdraw from Checking Account</button>';
            }
            if ($row['savings_balance'] > 0) {
                echo '<button onclick="window.location.href=\'withdraw_savings.php\'">Withdraw from Savings Account</button>';
            }
            echo '<button onclick="window.location.href=\'payment.php\'">Pay Another User</button>';
        }
    } elseif ($userType === 'admin') {
        // Display admin-specific content
        echo '<h2>Admin Dashboard</h2>';
        // Display admin actions/buttons
        echo '<button onclick="window.location.href=\'edit_user.php\'">Edit User Information</button>';
        echo '<button onclick="window.location.href=\'delete_user.php\'">Delete User</button>';
    }
    ?>
</body>
</html>

<?php
session_start();

require_once('db_connection.php');

require_once('admin_check.php');

// Delete a transaction if delete button is clicked
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_transaction_id'])) {
    $deleteTransactionId = $_POST['delete_transaction_id'];
    $stmt = $conn->prepare("DELETE FROM UserTransactionTable WHERE transaction_id = ?");
    $stmt->bind_param("i", $deleteTransactionId);
    $stmt->execute();
    $stmt->close();
}

// Fetch transaction data
$result = $conn->query("SELECT * FROM UserTransactionTable");

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Transaction List</title>
</head>
<body>
    <h1>User Transaction List</h1>
    <table border="1">
        <thead>
            <tr>
                <th>Sender Account ID</th>
                <th>Receiver Account ID</th>
                <th>Amount Transferred</th>
                <th>Transaction Date</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($row['send_acctID']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['receive_acctID']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['amount_transferred']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['transaction_date']) . "</td>";
                    echo "<td>
                        <form method='post' action=''>
                            <input type='hidden' name='delete_transaction_id' value='" . $row['transaction_id'] . "'>
                            <input type='submit' value='Delete'>
                        </form>
                        </td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='6'>No transactions found</td></tr>";
            }
            ?>
        </tbody>
    </table>
    <a href="welcome.php">Back to Dashboard</a>
</body>
</html>
<?php
$conn->close();
?>

<?php
session_start();

require_once('db_connection.php');

require_once('admin_check.php');

// Delete a user if delete button is clicked
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_user_id'])) {
    $deleteUserId = $_POST['delete_user_id'];
    $stmt = $conn->prepare("DELETE FROM UserInformation WHERE unique_user_ID = ?");
    $stmt->bind_param("s", $deleteUserId);
    $stmt->execute();
    $stmt->close();
}

// Fetch user data
$result = $conn->query("SELECT unique_user_ID, email, first_name, last_name FROM UserInformation");

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User List</title>
</head>
<body>
    <h1>User List</h1>
    <table border="1">
        <thead>
            <tr>
                <th>User ID</th>
                <th>Email</th>
                <th>First Name</th>
                <th>Last Name</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($row['unique_user_ID']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['email']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['first_name']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['last_name']) . "</td>";
                    echo "<td>
                        <form method='post' action=''>
                            <input type='hidden' name='delete_user_id' value='" . $row['unique_user_ID'] . "'>
                            <input type='submit' value='Delete'>
                        </form>
                        </td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='5'>No users found</td></tr>";
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

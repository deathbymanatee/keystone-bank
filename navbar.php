<!-- logout.php -->
<?php
session_start();

if (isset($_POST['logout'])) {
    // Unset all of the session variables
    $_SESSION = array();
    
    // Destroy the session.
    session_destroy();
    
    // Redirect to logout success page
    header("Location: logout_success.php");
    exit();
}
?>

<form action="" method="post">
    <button type="submit" name="logout">Logout</button>
</form>
<form action="view_profile.php" method="get">
    <button type="submit">View Profile</button>
</form>

<?php
// Database configuration
$host = "localhost"; // Change this if your database is hosted elsewhere
$username = "root"; // Your MariaDB username
$password = ""; // Your MariaDB password (leave empty if no password set)
$database = "keystone"; // Your database name

// Create connection
$conn = new mysqli($host, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>

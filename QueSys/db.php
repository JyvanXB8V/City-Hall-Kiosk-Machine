<?php
$host = "localhost";
$user = "root";   // default for XAMPP
$pass = "";       // default is empty
$db   = "queue_system";

$conn = new mysqli($host, $user, $pass, $db);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>

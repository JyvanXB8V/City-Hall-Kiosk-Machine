<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: admin_login.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; }
        .menu a {
            display: block;
            margin: 10px 0;
            padding: 10px;
            background: #007bff;
            color: white;
            text-decoration: none;
            width: 200px;
            border-radius: 5px;
            text-align: center;
        }
        .menu a:hover { background: #0056b3; }
    </style>
</head>
<body>
    <h1>Welcome, Admin</h1>
    <div class="menu">
        <a href="admin_departments.php">Manage Departments</a>
        <a href="admin_reports.php">View Reports</a>
        <a href="admin_logout.php">Logout</a>
    </div>
</body>
</html>

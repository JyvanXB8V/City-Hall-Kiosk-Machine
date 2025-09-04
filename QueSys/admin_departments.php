<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: admin_login.php");
    exit;
}
include "db.php";

// Handle add department
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_department'])) {
    $name = trim($_POST['name']);
    $prefix = strtoupper(trim($_POST['prefix']));

    if (!empty($name) && !empty($prefix)) {
        $stmt = $conn->prepare("INSERT INTO departments (name, prefix) VALUES (?, ?)");
        $stmt->bind_param("ss", $name, $prefix);
        $stmt->execute();
    }
    header("Location: admin_departments.php");
    exit;
}

// Handle delete department
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $stmt = $conn->prepare("DELETE FROM departments WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    header("Location: admin_departments.php");
    exit;
}

// Get all departments
$departments = $conn->query("SELECT * FROM departments ORDER BY id ASC");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin - Departments</title>
    <style>
        table { border-collapse: collapse; width: 60%; margin: auto; }
        th, td { border: 1px solid black; padding: 8px; text-align: center; }
        form { text-align: center; margin: 20px; }
        input, button { padding: 8px; margin: 5px; }
    </style>
</head>
<body>
    <h1 style="text-align:center;">Manage Departments</h1>

    <form method="post">
        <input type="text" name="name" placeholder="Department Name" required>
        <input type="text" name="prefix" placeholder="Prefix (1-2 letters)" maxlength="2" required>
        <button type="submit" name="add_department">Add Department</button>
    </form>

    <table>
        <tr>
            <th>ID</th>
            <th>Department</th>
            <th>Prefix</th>
            <th>Action</th>
        </tr>
        <?php while($row = $departments->fetch_assoc()) { ?>
            <tr>
                <td><?php echo $row['id']; ?></td>
                <td><?php echo htmlspecialchars($row['name']); ?></td>
                <td><?php echo htmlspecialchars($row['prefix']); ?></td>
                <td>
                    <a href="?delete=<?php echo $row['id']; ?>" onclick="return confirm('Delete this department?')">🗑️ Delete</a>
                </td>
            </tr>
        <?php } ?>
    </table>

    <div style="text-align:center; margin-top:20px;">
        <a href="admin_dashboard.php">⬅ Back to Dashboard</a>
    </div>
</body>
</html>

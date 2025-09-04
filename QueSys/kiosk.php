<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include "db.php";

// Step 1 → Department chosen, go to Standard/Priority selection
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['department_id']) && !isset($_POST['priority'])) {
    $dept_id = intval($_POST['department_id']);

    // Get department info
    $stmt = $conn->prepare("SELECT * FROM departments WHERE id=?");
    $stmt->bind_param("i", $dept_id);
    $stmt->execute();
    $dept = $stmt->get_result()->fetch_assoc();

    ?>
    <h1 style="text-align:center;"><?php echo htmlspecialchars($dept['name']); ?></h1>
    <form method="post" style="text-align:center;">
        <input type="hidden" name="department_id" value="<?php echo $dept['id']; ?>">
        <button type="submit" name="priority" value="Standard">Standard</button>
        <button type="submit" name="priority" value="Priority">Priority</button>
    </form>
    <form method="get" action="kiosk.php" style="text-align:center;">
        <button type="submit">⬅️ Back</button>
    </form>
    <?php
    exit;
}

// Step 2 → Priority chosen, generate ticket & show receipt
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['department_id']) && isset($_POST['priority'])) {
    $dept_id = intval($_POST['department_id']);
    $priority = $_POST['priority'];

    // Get department info
    $stmt = $conn->prepare("SELECT * FROM departments WHERE id=?");
    $stmt->bind_param("i", $dept_id);
    $stmt->execute();
    $dept = $stmt->get_result()->fetch_assoc();

    if (!$dept) {
        die("Invalid department.");
    }

    // Prefix comes from DB
    $prefix = strtoupper($dept['prefix']) . ($priority == "Priority" ? "P" : "S");

    // Get last ticket for today
    $date = date("Y-m-d");
    $sql = "SELECT number FROM tickets 
            WHERE department_id=? AND priority=? 
              AND DATE(created_at)=? 
            ORDER BY id DESC LIMIT 1";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iss", $dept_id, $priority, $date);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $lastNumber = $result->fetch_assoc()['number'];
        $num = intval(substr($lastNumber, 2)) + 1; // extract digits after prefix
    } else {
        $num = 1;
    }

    $ticketNumber = $prefix . str_pad($num, 3, "0", STR_PAD_LEFT);

    // Save ticket
    $stmt = $conn->prepare("INSERT INTO tickets (department_id, priority, number) VALUES (?, ?, ?)");
    $stmt->bind_param("iss", $dept_id, $priority, $ticketNumber);
    $stmt->execute();
    ?>
    <div style="border:2px dashed black; padding:20px; width:300px; margin:auto; text-align:center;">
        <h2>🎟️ Your Ticket</h2>
        <h1><?php echo $ticketNumber; ?></h1>
        <p><strong>Department:</strong> <?php echo htmlspecialchars($dept['name']); ?></p>
        <p><strong>Type:</strong> <?php echo $priority; ?></p>
        <p><small><?php echo date("Y-m-d H:i:s"); ?></small></p>
    </div>
    <br>
    <form method="get" action="kiosk.php" style="text-align:center;">
        <button type="submit">⬅️ Back to Kiosk</button>
    </form>
    <?php
    exit;
}

// Default → Show list of departments
$departments = $conn->query("SELECT * FROM departments ORDER BY id ASC");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Kiosk - Get Ticket</title>
    <style>
        button {
            display: block;
            width: 220px;
            margin: 10px auto;
            padding: 15px;
            font-size: 18px;
        }
    </style>
</head>
<body>
    <h1 style="text-align:center;">Kiosk</h1>
    <form method="post" style="text-align:center;">
        <?php while($dept = $departments->fetch_assoc()) { ?>
            <button type="submit" name="department_id" value="<?php echo $dept['id']; ?>">
                <?php echo htmlspecialchars($dept['name']); ?>
            </button>
        <?php } ?>
    </form>
</body>
</html>

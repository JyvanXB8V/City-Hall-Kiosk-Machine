<?php
include "db.php";

// Set department
$departmentName = "Certificates"; // Change for each display

// Get department ID
$deptResult = $conn->query("SELECT id FROM departments WHERE name='$departmentName'");
$dept = $deptResult->fetch_assoc();
$deptId = $dept ? $dept['id'] : 0;

// Get current serving ticket
$currentSql = "SELECT number FROM tickets 
               WHERE department_id=$deptId AND status='serving' 
               ORDER BY called_at DESC LIMIT 1";
$currentResult = $conn->query($currentSql);
$current = ($currentResult && $currentResult->num_rows > 0) ? $currentResult->fetch_assoc()['number'] : "None";

// Get next 5 waiting tickets
$queueSql = "SELECT number FROM tickets 
             WHERE department_id=$deptId AND status='waiting' 
             ORDER BY FIELD(priority,'Priority','Standard'), created_at ASC 
             LIMIT 5";
$queueResult = $conn->query($queueSql);
$queue = [];
while ($row = $queueResult->fetch_assoc()) {
    $queue[] = $row['number'];
}
?>
<!DOCTYPE html>
<html>
<head>
    <title><?php echo htmlspecialchars($departmentName); ?> Display</title>
    <meta http-equiv="refresh" content="5"> <!-- Auto-refresh every 5s -->
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #111;
            color: #fff;
            text-align: center;
            padding: 50px;
        }
        h1 { font-size: 48px; margin-bottom: 20px; }
        .current { font-size: 100px; font-weight: bold; margin: 40px 0; color: #00ff99; }
        .queue { font-size: 36px; margin-top: 30px; }
        .queue span { display: inline-block; margin: 10px; padding: 10px 20px; background: #333; border-radius: 10px; }
    </style>
</head>
<body>
    <h1><?php echo htmlspecialchars($departmentName); ?></h1>
    <div class="current">Now Serving: <?php echo $current; ?></div>
    <div class="queue">
        Next:
        <?php if (count($queue) > 0): ?>
            <?php foreach ($queue as $num): ?>
                <span><?php echo $num; ?></span>
            <?php endforeach; ?>
        <?php else: ?>
            <span>No waiting tickets</span>
        <?php endif; ?>
    </div>
</body>
</html>

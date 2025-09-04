<?php
include "db.php";

// Map counters → department IDs
$counters = [
    "Counter 1" => 1, // Paying Tax
    "Counter 2" => 2, // Documents
    "Counter 3" => 3, // Certificates
    "Counter 4" => 4  // Others
];

// Handle actions
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $counter = $_POST['counter'];
    $deptId = $counters[$counter];

    if (isset($_POST['call_next'])) {
        // Call next ticket (Priority first)
        $sql = "SELECT id, number FROM tickets
                WHERE department_id=$deptId AND status='waiting'
                ORDER BY FIELD(priority,'Priority','Standard'), created_at ASC
                LIMIT 1";
        $result = $conn->query($sql);

        if ($result && $result->num_rows > 0) {
            $ticket = $result->fetch_assoc();
            $ticketId = $ticket['id'];

            $update = "UPDATE tickets
                       SET status='serving', counter='$counter', called_at=NOW()
                       WHERE id=$ticketId";
            $conn->query($update);
        }
    }

    if (isset($_POST['done'])) {
        // Mark current serving ticket as done
        $update = "UPDATE tickets
                   SET status='done', completed_at=NOW()
                   WHERE counter='$counter' AND status='serving'";
        $conn->query($update);
    }
}

// Get current ticket for display
function getCurrentTicket($conn, $counter) {
    $sql = "SELECT number FROM tickets
            WHERE counter='$counter' AND status='serving'
            ORDER BY called_at DESC LIMIT 1";
    $result = $conn->query($sql);
    return ($result && $result->num_rows > 0) ? $result->fetch_assoc()['number'] : "None";
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Staff Panel</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f0f0f0;
            margin: 20px;
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
        }
        .counter-box {
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.2);
            text-align: center;
        }
        h2 { margin-bottom: 15px; }
        .ticket { font-size: 28px; margin: 15px 0; }
        button {
            padding: 10px 20px;
            font-size: 18px;
            margin: 5px;
            cursor: pointer;
            border: none;
            border-radius: 8px;
        }
        .call-btn { background: #007bff; color: #fff; }
        .done-btn { background: #28a745; color: #fff; }
    </style>
</head>
<body>
    <?php foreach ($counters as $counter => $deptId): ?>
        <div class="counter-box">
            <h2><?php echo htmlspecialchars($counter); ?></h2>
            <div class="ticket">Current: <?php echo getCurrentTicket($conn, $counter); ?></div>
            <form method="post">
                <input type="hidden" name="counter" value="<?php echo htmlspecialchars($counter); ?>">
                <button class="call-btn" type="submit" name="call_next">Call Next</button>
                <button class="done-btn" type="submit" name="done">Done</button>
            </form>
        </div>
    <?php endforeach; ?>
</body>
</html>

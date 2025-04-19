<?php
require 'config.php';
// require_once 'functions.php';
// session_start();
redirectIfNotLoggedIn();

$user_id = $_SESSION['user_id'];

$stmt = $pdo->prepare("SELECT action, timestamp FROM user_logs WHERE user_id = ? ORDER BY timestamp DESC");
$stmt->execute([$user_id]);
$logs = $stmt->fetchAll();
?>

<link rel="stylesheet" href="style.css">
<?php include 'navbar.php'; ?>
<?php include 'sidebar.php'; ?>
<div style="margin-left: 220px; padding: 20px;">
    
<h2>Your Activity Log</h2>
<table style="border-collapse: collapse; width: 100%;" border="1">
    <thead>
        <tr >
            <th style="width: 50%;text-align: left;">Action</th>
            <th style="width: 50%;text-align: left;">Timestamp</th>
        </tr>
    </thead>
    <tbody >
        <?php foreach ($logs as $log): ?>
            <tr>
                <td style="width: 50%;"><?= htmlspecialchars($log['action']) ?></td>
                <td style="width: 50%;"><?= $log['timestamp'] ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
</div>
<?php include 'footer.php'; ?>
<!-- <a href="dashboard.php">← Back to Dashboard</a> -->

<?php
require 'config.php';
// require_once 'functions.php';
// session_start();
redirectIfNotLoggedIn();

$user_id = $_SESSION['user_id'];
$logs_per_page = 20;

$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int) $_GET['page'] : 1;
$offset = ($page - 1) * $logs_per_page;

// Total log count
$countStmt = $pdo->prepare("SELECT COUNT(*) FROM user_logs WHERE user_id = ?");
$countStmt->execute([$user_id]);
$total_logs = $countStmt->fetchColumn();
$total_pages = ceil($total_logs / $logs_per_page);

// Fetch logs with pagination
$stmt = $pdo->prepare("SELECT action, timestamp FROM user_logs WHERE user_id = ? ORDER BY timestamp DESC LIMIT ? OFFSET ?");
$stmt->bindValue(1, $user_id, PDO::PARAM_INT);
$stmt->bindValue(2, $logs_per_page, PDO::PARAM_INT);
$stmt->bindValue(3, $offset, PDO::PARAM_INT);
$stmt->execute();
$logs = $stmt->fetchAll();
?>

<link rel="stylesheet" href="style.css">
<style>
    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background-color: #f5f7fa;
        color: #333;
    }

    .container {
        margin-left: 220px;
        padding: 20px;
    }

    h2 {
        margin-bottom: 20px;
        font-size: 28px;
        color: #4A90E2;
    }

    .log-table {
        width: 100%;
        border-collapse: collapse;
        background-color: #fff;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }

    .log-table th, .log-table td {
        padding: 12px 15px;
        text-align: left;
    }

    .log-table th {
        background-color: #4A90E2;
        color: #fff;
        font-weight: 600;
    }

    .log-table tr:nth-child(even) {
        background-color: #f9f9f9;
    }

    .log-table tr:hover {
        background-color: #e6f2ff;
        transition: background-color 0.3s ease;
    }

    .pagination {
        margin-top: 20px;
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 10px;
    }

    .pagination a, .pagination span {
        padding: 8px 12px;
        border-radius: 5px;
        text-decoration: none;
        color: #4A90E2;
        border: 1px solid #4A90E2;
        transition: background-color 0.3s ease, color 0.3s ease;
    }

    .pagination a:hover {
        background-color: #4A90E2;
        color: #fff;
    }

    .pagination .current-page {
        background-color: #4A90E2;
        color: #fff;
        cursor: default;
    }

    .no-logs {
        margin-top: 20px;
        text-align: center;
        color: #999;
    }
</style>

<?php include 'navbar.php'; ?>
<?php include 'sidebar.php'; ?>

<div class="container">
    <h2>Your Activity Log</h2>

    <?php if ($logs): ?>
        <table class="log-table">
            <thead>
                <tr>
                    <th>Action</th>
                    <th>Timestamp</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($logs as $log): ?>
                    <tr>
                        <td><?= htmlspecialchars($log['action']) ?></td>
                        <td><?= date('F j, Y, g:i a', strtotime($log['timestamp'])) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- Pagination -->
        <div class="pagination">
            <?php if ($page > 1): ?>
                <a href="?page=<?= $page - 1 ?>">&laquo; Previous</a>
            <?php endif; ?>

            <span class="current-page">Page <?= $page ?> of <?= $total_pages ?></span>

            <?php if ($page < $total_pages): ?>
                <a href="?page=<?= $page + 1 ?>">Next &raquo;</a>
            <?php endif; ?>
        </div>
    <?php else: ?>
        <div class="no-logs">
            No activity logs found.
        </div>
    <?php endif; ?>
</div>

<?php include 'footer.php'; ?>

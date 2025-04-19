<?php
session_start();
require_once 'db.php';
require_once 'functions.php';
if (!function_exists('log_action')) {
    function log_action($pdo, $user_id, $action) {
        $stmt = $pdo->prepare("INSERT INTO user_logs (user_id, action) VALUES (?, ?)");
        $stmt->execute([$user_id, $action]);
    }
}
?>

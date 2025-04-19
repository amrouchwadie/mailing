<?php
require 'config.php';
session_start();
session_destroy();
if (isset($_SESSION['user_id'])) {
    log_action($pdo, $_SESSION['user_id'], 'Logged out');
    session_destroy();
}
header("Location: login.php");
exit;

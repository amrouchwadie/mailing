<?php
require 'config.php';
// require 'functions.php';

$token = $_GET['token'] ?? null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newPassword = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $token = $_POST['token'];

    $stmt = $pdo->prepare("SELECT id FROM users WHERE reset_token = ? AND reset_expires > ?");
    $stmt->execute([$token, time()]);
    $user = $stmt->fetch();

    if ($user) {
        $stmt = $pdo->prepare("UPDATE users SET password = ?, reset_token = NULL, reset_expires = NULL WHERE id = ?");
        $stmt->execute([$newPassword, $user['id']]);
        echo "Password updated! <a href='login.php'>Login</a>";
        log_action($pdo, $user_id, 'Reset password successfully', 'reset_password.php');
    } else {
        echo "Invalid or expired token.";
    }
}
?>
<link rel="stylesheet" href="style.css">
<h2>Reset Password</h2>
<form method="POST">
    <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">
    <input type="password" name="password" required placeholder="New Password">
    <button type="submit">Reset</button>
</form>

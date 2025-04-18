<?php
require 'config.php';
// require 'functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = sanitize($_POST['email']);
    $token = bin2hex(random_bytes(16));
    $expires = time() + 3600;

    $stmt = $pdo->prepare("UPDATE users SET reset_token = ?, reset_expires = ? WHERE email = ?");
    $stmt->execute([$token, $expires, $email]);

    $resetLink = "http://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . "/reset_password.php?token=$token";
    $subject = "Password Reset Request";
    $message = "Click the following link to reset your password:\n\n$resetLink";
    $headers = "From: no-reply@gmail.com";

    if (mail($email, $subject, $message, $headers)) {
        echo "Reset link sent to your email.";
    } else {
        echo "Failed to send email.";
    }
}
?>
<link rel="stylesheet" href="style.css">
<h2>Forgot Password</h2>
<form method="POST">
    <input type="email" name="email" required placeholder="Your Email">
    <button type="submit">Send Reset Link</button>
</form>

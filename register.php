<?php
require 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = sanitize($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $stmt = $pdo->prepare("INSERT INTO users (email, password) VALUES (?, ?)");
    
    try {
        $stmt->execute([$email, $password]);
        echo "Registered! <a href='login.php'>Login</a>";
    } catch (PDOException $e) {
        echo "Error: E-mail already exists or password is lacking.";
    }
}
?>
<link rel="stylesheet" href="style.css">
<h2>Register</h2>
<form method="POST">
    <input type="email" name="email" required placeholder="Email">
    <input type="password" name="password" required placeholder="Password">
    <button type="submit">Register</button>
</form>

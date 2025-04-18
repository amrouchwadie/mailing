<?php
require 'config.php';
redirectIfNotLoggedIn();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name       = $_POST['name'] ?? '';
    $host       = $_POST['host'] ?? '';
    $port       = $_POST['port'] ?? '';
    $encryption = $_POST['encryption'] ?? '';
    $username   = $_POST['username'] ?? '';
    $password   = $_POST['password'] ?? '';

    if ($name && $host && $port && $encryption && $username && $password) {
        $stmt = $pdo->prepare("
            INSERT INTO smtp_credentials (name, host, port, encryption, username, password)
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        if ($stmt->execute([$name, $host, $port, $encryption, $username, $password])) {
            header("Location: select_config.php?added=1");
            exit;
        } else {
            echo "❌ Failed to save credentials.";
        }
    } else {
        echo "⚠️ Please fill in all fields.";
    }
}
<?php
require 'config.php';
redirectIfNotLoggedIn();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">    
    <link rel="stylesheet" href="style.css">
    <title>Document</title>
</head>
<body>
<?php include 'navbar.php'; ?>
<?php include 'sidebar.php'; ?>
<div style="margin-left: 220px; padding: 20px;">
<h2>Welcome to your dashboard!</h2>
<p><a href="logout.php">Logout</a></p>
</div>
<?php include 'footer.php'; ?>
</body>
</html>


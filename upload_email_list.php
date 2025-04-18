<?php
require 'config.php';
redirectIfNotLoggedIn();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['email_list'])) {
    // Check if file is uploaded and valid
    if ($_FILES['email_list']['error'] === 0) {
        // Read file contents
        $fileContent = file_get_contents($_FILES['email_list']['tmp_name']);
        $emails = explode("\n", $fileContent);
        $emails = array_filter(array_map('trim', $emails));  // Clean up the list

        // Insert the email list name into the database
        $listName = $_POST['list_name'];
        $stmt = $pdo->prepare("INSERT INTO email_lists (name) VALUES (?)");
        $stmt->execute([$listName]);
        $emailListId = $pdo->lastInsertId();  // Get the last inserted email list ID

        // Insert the emails into the database
        $stmt = $pdo->prepare("INSERT INTO email_list_entries (email_list_id, email) VALUES (?, ?)");
        foreach ($emails as $email) {
            $stmt->execute([$emailListId, $email]);
        }

        echo "✅ Email list uploaded successfully!";
    } else {
        echo "❌ Error uploading file.";
    }
}
?>
<?php
// Example of a page using navbar and sidebar
include 'navbar.php';
include 'sidebar.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Data</title>
</head>
<body>
<div style="margin-left: 220px; padding: 40px;">

<form method="POST" enctype="multipart/form-data">
    <label for="list_name">List Name:</label>
    <input type="text" name="list_name" required>
    
    <label for="email_list">Upload Email List:</label>
    <input type="file" name="email_list" accept=".txt" required>

    <button type="submit">Upload Email List</button>
</form>
</div>
<?php include 'footer.php'; ?>
</body>
</html>
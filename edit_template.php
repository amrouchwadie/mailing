<?php
require 'config.php';
redirectIfNotLoggedIn();
$templatePath = 'template/Home Security.html';
$fromTitleField = 'from_title.txt';
$subjectField = 'subject.txt';

// Save the template, from title, and subject
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Save HTML template content
    file_put_contents($templatePath, $_POST['html_content']);
    
    // Save the From Title and Subject content
    file_put_contents($fromTitleField, $_POST['from_title']);
    file_put_contents($subjectField, $_POST['subject']);
    
    // Redirect back to select_config.php with a "saved" message
    header('Location: select_config.php?saved=1');
    exit;
}

// Get current values for the template, from title, and subject
$currentHtml = file_get_contents($templatePath);
$fromTitleValue = file_exists($fromTitleField) ? file_get_contents($fromTitleField) : 'Home Safety';  // Default From title
$subjectValue = file_exists($subjectField) ? file_get_contents($subjectField) : 'Pending - Order #88254';  // Default subject

$saved = isset($_GET['saved']);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Email Template</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 30px; background-color: #f4f4f4; }
        textarea { width: 100%; height: 500px; padding: 10px; font-family: monospace; }
        input, button { padding: 10px; margin-top: 10px; width: 100%; max-width: 400px; }
        .message { padding: 10px; margin-bottom: 20px; border-radius: 5px; background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
    </style>
</head>
<body>
<?php
// Example of a page using navbar and sidebar
include 'navbar.php';
include 'sidebar.php';
?>
<div style="margin-left: 220px; padding: 20px;">
    <h2>Edit Email Template: <code>template/Template.html</code></h2>

    <?php if ($saved): ?>
        <div class="message">✅ Template saved successfully!</div>
    <?php endif; ?>

    <form method="POST">
        <label for="from_title">From Title:</label>
        <input type="text" name="from_title" id="from_title" value="<?= htmlspecialchars($fromTitleValue) ?>" required>
        <br>
        <label for="subject">Email Subject:</label>
        <input type="text" name="subject" id="subject" value="<?= htmlspecialchars($subjectValue) ?>" required>
        <br><br>
        <label for="html_content">Email Template Content:</label>
        <textarea name="html_content"><?= htmlspecialchars($currentHtml) ?></textarea>
        
        <br>
        <button type="submit">💾 Save Template</button>
    </form>
</div>
    <?php include 'footer.php'; ?>
</body>
</html>

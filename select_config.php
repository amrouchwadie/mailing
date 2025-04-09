<?php
require 'db.php';
$template = file_get_contents('template/Home Security.html');
$added = isset($_GET['added']) && $_GET['added'] == 1;
$sent = isset($_GET['sent']) && $_GET['sent'] == 1;
$error = $_GET['error'] ?? null;

$stmt = $pdo->prepare("SELECT * FROM email_lists");
$stmt->execute();
$emailLists = $stmt->fetchAll();

// Get the SMTP configurations
$stmt = $pdo->prepare("SELECT * FROM smtp_credentials");
$stmt->execute();
$smtpConfigs = $stmt->fetchAll();
?>


<!DOCTYPE html>
<html>
<head>
    <title>Select SMTP Config</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body { font-family: Arial; padding: 30px; background: #f4f4f4; }
        select, button { padding: 10px; margin: 10px 0; }
        .message { padding: 10px; margin-bottom: 15px; border-radius: 5px; }
        .success { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .error { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .preview-container { border: 1px solid #ccc; padding: 15px; background: #fff; margin-top: 20px; border-radius: 8px; max-height: 500px; overflow: auto; }
        body { font-family: Arial; padding: 30px; background: #f4f4f4; }
        select, button { padding: 10px; margin: 10px 0; }
        .message { padding: 10px; margin-bottom: 15px; border-radius: 5px; }
        .success { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .error { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .preview-container { border: 1px solid #ccc; padding: 15px; background: #fff; margin-top: 20px; border-radius: 8px; max-height: 500px; overflow: auto; }
        .btn-link {
        display: inline-block;
        padding: 10px 20px;
        background-color: #007bff;
        color: white;
        text-decoration: none;
        border-radius: 4px;
        font-size: 14px;
        transition: background-color 0.2s ease;
    }

    .btn-link:hover {
        background-color: #0056b3;
    }
</style>
</head>

        
<body>
<?php
// Example of a page using navbar and sidebar
include 'navbar.php';
include 'sidebar.php';
?>
<div style="margin-left: 220px; padding: 20px;">
<?php if ($added): ?>
    <div class="message success">✅ SMTP configuration added successfully!</div>
<?php endif; ?>

<?php if ($sent): ?>
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: 'success',
            title: 'Emails sent successfully!',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true
        });
    });
</script>
<?php endif; ?>

<?php if ($error): ?>
    <div class="message error">❌ Error: <?= htmlspecialchars($error) ?></div>
<?php endif; ?>
<a href="edit_template.php" class="btn-link">Edit Template</a>

<h1>Select SMTP Configuration and Email List</h1>




<form method="POST" action="index.php">
    <label for="smtp_id">Choose SMTP Configuration:</label>
    <select name="smtp_id" id="smtp_id" required>
        <?php foreach ($smtpConfigs as $config): ?>
            <option value="<?php echo $config['id']; ?>">
                <?php echo htmlspecialchars($config['name']); ?>
            </option>
        <?php endforeach; ?>
    </select>
<br>
    <label for="email_list_id">Choose Email List:</label>
    <select name="email_list_id" id="email_list_id" required>
        <?php foreach ($emailLists as $list): ?>
            <option value="<?php echo $list['id']; ?>">
                <?php echo htmlspecialchars($list['name']); ?>
            </option>
        <?php endforeach; ?>
    </select>
<br>
    <label for="start_range">Start Range (1):</label>
    <input type="number" name="start_range" min="1" required>

    <label for="end_range">End Range (2000):</label>
    <input type="number" name="end_range" min="1" required>
<br>
    <button type="submit">Send Emails</button>
</form>



<div class="preview-container">
    <h3>📧 Email Preview </h3>
    <?= $template ?>
</div>

</div>

<?php include 'footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</body>
</html>

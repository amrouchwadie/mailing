<?php
require 'config.php';
redirectIfNotLoggedIn();
log_action($pdo, $_SESSION['user_id'], 'Viewed Select Config', 'select_config.php');
ob_start();
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}


// Load email template
$templatePath = 'template/Home Security.html';
$template = file_exists($templatePath) ? file_get_contents($templatePath) : '<p>Error: Email template not found</p>';

// Handle URL parameters
$added = isset($_GET['added']) && $_GET['added'] == 1;
$sent = isset($_GET['sent']) && $_GET['sent'] == 1;
$error = $_GET['error'] ?? null;

try {
    // Fetch email lists
    $stmt = $pdo->prepare("SELECT * FROM email_lists");
    $stmt->execute();
    $emailLists = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Fetch SMTP configurations
    $stmt = $pdo->prepare("SELECT * FROM smtp_credentials");
    $stmt->execute();
    $smtpConfigs = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
} catch (PDOException $e) {
    $error = 'Database error: ' . $e->getMessage();
    log_action($pdo, $_SESSION['user_id'], 'fetching error', 'select_config.php');
    $emailLists = [];
    $smtpConfigs = [];
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Select Email Configuration</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body { font-family: Arial; padding: 30px; background: #f4f4f4; }
        select, button, input { padding: 10px; margin: 10px 0; }
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
        .smtp-section { display: none; }
    </style>
</head>
<body>
<?php include 'navbar.php'; ?>
<?php include 'sidebar.php'; ?>
<div style="margin-left: 220px; padding: 20px;">
    <?php if ($added): ?>
        <div class="message success">SMTP configuration added successfully!</div>
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
        <script>
        document.addEventListener('DOMContentLoaded', function () {
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'error',
                title: 'Error: <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>',
                showConfirmButton: false,
                timer: 5000,
                timerProgressBar: true
            });
        });
        </script>
    <?php endif; ?>

    <a href="edit_template.php" class="btn-link">Edit Template</a>
    <h1>Select Sending Method and Email List</h1>
    <form method="POST" action="index.php">
        <label>Sending Method:</label><br>
        <input type="radio" name="send_method" value="smtp" id="send_smtp" required onchange="toggleSMTP()">
        <label for="send_smtp">SMTP</label>
        <input type="radio" name="send_method" value="gmail_api" id="send_gmail" onchange="toggleSMTP()">
        <label for="send_gmail">Gmail API</label><br>

        <div id="smtp_section" class="smtp-section">
            <label for="smtp_id">Choose SMTP Configuration:</label>
            <select name="smtp_id" id="smtp_id">
                <option value="">Select SMTP</option>
                <?php foreach ($smtpConfigs as $config): ?>
                    <option value="<?= htmlspecialchars($config['id']) ?>">
                        <?= htmlspecialchars($config['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select><br>
        </div>

        <label for="email_list_id">Choose Email List:</label>
        <select name="email_list_id" id="email_list_id" required>
            <option value="">Select Email List</option>
            <?php foreach ($emailLists as $list): ?>
                <option value="<?= htmlspecialchars($list['id']) ?>">
                    <?= htmlspecialchars($list['name']) ?>
                </option>
            <?php endforeach; ?>
        </select><br>

        <label for="start_range">Start Range:</label>
        <input type="number" name="start_range" min="1" value="1" required>
        <label for="end_range">End Range:</label>
        <input type="number" name="end_range" min="1" value="5" required><br>

        <button type="submit">Send Emails</button>
    </form>

    <div class="preview-container">
        <h3>Email Preview</h3>
        <?= $template ?>
    </div>
</div>

<?php include 'footer.php'; ?>

<script>
function toggleSMTP() {
    const smtpSection = document.getElementById('smtp_section');
    const smtpRadio = document.getElementById('send_smtp');
    const smtpSelect = document.getElementById('smtp_id');
    smtpSection.style.display = smtpRadio.checked ? 'block' : 'none';
    smtpSelect.required = smtpRadio.checked;
}
</script>
<?php ob_end_flush(); ?>
</body>
</html>
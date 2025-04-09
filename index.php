<?php
require 'db.php';
require __DIR__ . '/vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Ensure required parameters are provided
$smtp_id = $_POST['smtp_id'] ?? null;
$email_list_id = $_POST['email_list_id'] ?? null;
$start_range = $_POST['start_range'] ?? null;
$end_range = $_POST['end_range'] ?? null;

if (!$smtp_id || !$email_list_id || !$start_range || !$end_range) {
    die("❌ Missing parameters.");
}

// Get SMTP configuration from database
$stmt = $pdo->prepare("SELECT * FROM smtp_credentials WHERE id = ?");
$stmt->execute([$smtp_id]);
$config = $stmt->fetch();

if (!$config) {
    die("❌ SMTP configuration not found.");
}

// Sanitize and calculate LIMIT/OFFSET
$offset = (int) ($start_range - 1);
$limit = (int) ($end_range - $start_range + 1);

// Get email list entries from the database
$stmt = $pdo->prepare("SELECT email FROM email_list_entries WHERE email_list_id = ? LIMIT $offset, $limit");
$stmt->execute([$email_list_id]);
$emails = $stmt->fetchAll(PDO::FETCH_COLUMN);

if (empty($emails)) {
    die("❌ No emails found in the selected range.");
}

// Get From Title and Subject
$fromTitle = file_get_contents('from_title.txt') ?: 'Default Title';
$subject = file_get_contents('subject.txt') ?: 'Default Subject';

// Get HTML email template
$templatePath = 'template/Home Security.html';
$templateContent = file_get_contents($templatePath);

if (!$templateContent) {
    die("❌ Email template is missing.");
}

// Create PHPMailer instance
$mail = new PHPMailer(true);

try {
    // SMTP Settings
    $mail->isSMTP();
    $mail->Host       = $config['host'];
    $mail->SMTPAuth   = true;
    $mail->Username   = $config['username'];
    $mail->Password   = $config['password'];
    $mail->SMTPSecure = $config['encryption'] === 'ssl' ? PHPMailer::ENCRYPTION_SMTPS : PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = $config['port'];

    // Email Sender
    $mail->setFrom($config['username'], $fromTitle);
    $mail->Subject = $subject;

    // BCC all recipients from DB
    foreach ($emails as $email) {
        $mail->addBCC(trim($email));
    }

    // Email body
    $mail->isHTML(true);
    $mail->Body = $templateContent;

    // Send email
    $mail->send();

    echo "✅ Email sent to <strong>" . count($emails) . "</strong> recipients using SMTP: <strong>" . htmlspecialchars($config['name']) . "</strong>";
    header('Location: select_config.php?sent=1');
    exit;
} catch (Exception $e) {
    echo "❌ Mail error: " . $mail->ErrorInfo;
}
?>

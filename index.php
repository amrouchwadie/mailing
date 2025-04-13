<?php
ob_start();
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
require 'db.php';
require __DIR__ . '/vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Google\Service\Gmail as Google_Service_Gmail;
use Google\Service\Gmail\Message as Google_Service_Gmail_Message;

// Ensure required parameters
$send_method = $_POST['send_method'] ?? null;
$smtp_id = $_POST['smtp_id'] ?? null;
$email_list_id = $_POST['email_list_id'] ?? null;
$start_range = $_POST['start_range'] ?? null;
$end_range = $_POST['end_range'] ?? null;

if (!$send_method || !$email_list_id || !is_numeric($start_range) || !is_numeric($end_range) || ($send_method === 'smtp' && !$smtp_id)) {
    ob_end_clean();
    header('Location: select_config.php?error=' . urlencode('Invalid or missing parameters'));
    exit;
}

// Validate and sanitize range
$start_range = (int) $start_range;
$end_range = (int) $end_range;
if ($start_range < 1 || $end_range < $start_range) {
    ob_end_clean();
    header('Location: select_config.php?error=' . urlencode('Invalid range values'));
    exit;
}

// Calculate LIMIT/OFFSET
$offset = $start_range - 1;
$limit = min($end_range - $start_range + 1, 5);

// Debug: Log query parameters
file_put_contents('query_debug.log', "email_list_id: $email_list_id, offset: $offset, limit: $limit\n", FILE_APPEND);

try {
    // Get email list entries
    $stmt = $pdo->prepare("SELECT email FROM email_list_entries WHERE email_list_id = ? LIMIT ?, ?");
    $stmt->bindValue(1, (int) $email_list_id, PDO::PARAM_INT);
    $stmt->bindValue(2, (int) $offset, PDO::PARAM_INT);
    $stmt->bindValue(3, (int) $limit, PDO::PARAM_INT);
    $stmt->execute();
    $emails = $stmt->fetchAll(PDO::FETCH_COLUMN);

    if (empty($emails)) {
        ob_end_clean();
        header('Location: select_config.php?error=' . urlencode('No emails found in the selected range'));
        exit;
    }

    // Get From Title and Subject
    $fromTitle = file_get_contents('from_title.txt') ?: 'Default Title';
    $subject = file_get_contents('subject.txt') ?: 'Default Subject';

    // Get HTML email template
    $templatePath = 'template/Home Security.html';
    $templateContent = file_exists($templatePath) ? file_get_contents($templatePath) : null;

    if (!$templateContent) {
        ob_end_clean();
        header('Location: select_config.php?error=' . urlencode('Email template is missing'));
        exit;
    }

    if ($send_method === 'smtp') {
        $stmt = $pdo->prepare("SELECT * FROM smtp_credentials WHERE id = ?");
        $stmt->execute([(int) $smtp_id]);
        $config = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$config) {
            ob_end_clean();
            header('Location: select_config.php?error=' . urlencode('SMTP configuration not found'));
            exit;
        }

        $mail = new PHPMailer(true);
        $mail->isSMTP();
        $mail->Host = $config['host'];
        $mail->SMTPAuth = true;
        $mail->Username = $config['username'];
        $mail->Password = $config['password'];
        $mail->SMTPSecure = $config['encryption'] === 'ssl' ? PHPMailer::ENCRYPTION_SMTPS : PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = $config['port'];
        $mail->setFrom($config['username'], $fromTitle);
        $mail->Subject = $subject;

        foreach ($emails as $email) {
            $mail->addBCC(trim($email));
        }

        $mail->isHTML(true);
        $mail->Body = $templateContent;
        $mail->send();

        ob_end_clean();
        header('Location: select_config.php?sent=1');
        exit;
    } elseif ($send_method === 'gmail_api') {
        $client = new Google_Client();
        $client->setAuthConfig('credentials.json');
        $client->addScope(Google_Service_Gmail::GMAIL_SEND);
        $client->setRedirectUri('http://localhost/phpmail/phpMailer/auth-callback.php');
        $client->setAccessType('offline');
        $client->setPrompt('select_account consent');

        // Temporary: Disable SSL verification
        $httpClient = new GuzzleHttp\Client(['verify' => false]);
        $client->setHttpClient($httpClient);

        if (!isset($_SESSION['access_token']) || empty($_SESSION['access_token'])) {
            $_SESSION['pending_email_data'] = [
                'send_method' => $send_method,
                'smtp_id' => $smtp_id,
                'email_list_id' => $email_list_id,
                'start_range' => $start_range,
                'end_range' => $end_range
            ];
            $authUrl = $client->createAuthUrl();
            ob_end_clean();
            header('Location: ' . filter_var($authUrl, FILTER_SANITIZE_URL));
            exit;
        }

        $client->setAccessToken($_SESSION['access_token']);

        if ($client->isAccessTokenExpired()) {
            $refreshToken = $client->getRefreshToken();
            if ($refreshToken) {
                $client->fetchAccessTokenWithRefreshToken($refreshToken);
                $_SESSION['access_token'] = $client->getAccessToken();
            } else {
                $_SESSION['pending_email_data'] = [
                    'send_method' => $send_method,
                    'smtp_id' => $smtp_id,
                    'email_list_id' => $email_list_id,
                    'start_range' => $start_range,
                    'end_range' => $end_range
                ];
                $authUrl = $client->createAuthUrl();
                ob_end_clean();
                header('Location: ' . filter_var($authUrl, FILTER_SANITIZE_URL));
                exit;
            }
        }

        $service = new Google_Service_Gmail($client);
        $fromEmail = 'jaredeblackwood@gmail.com';
        foreach ($emails as $email) {
            $rawMessage = 
                "From: $fromTitle <$fromEmail>\r\n" .
                "To: " . trim($email) . "\r\n" .
                "Subject: $subject\r\n" .
                "MIME-Version: 1.0\r\n" .
                "Content-Type: text/html; charset=UTF-8\r\n\r\n" .
                $templateContent;

            $mime = rtrim(strtr(base64_encode($rawMessage), '+/', '-_'), '=');
            $message = new Google_Service_Gmail_Message();
            $message->setRaw($mime);
            $service->users_messages->send('me', $message);
        }

        ob_end_clean();
        header('Location: select_config.php?sent=1');
        exit;
    }
} catch (Exception $e) {
    ob_end_clean();
    header('Location: select_config.php?error=' . urlencode($e->getMessage()));
    exit;
}
?>
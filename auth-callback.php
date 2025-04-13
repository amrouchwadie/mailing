<?php
ob_start();
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
require __DIR__ . '/vendor/autoload.php';
use Google\Service\Gmail as Google_Service_Gmail;

$client = new Google_Client();
$client->setAuthConfig('credentials.json');
$client->addScope(Google_Service_Gmail::GMAIL_SEND);
$client->setRedirectUri('http://localhost/phpmail/phpMailer/auth-callback.php');
$client->setAccessType('offline');
$client->setPrompt('select_account consent');

// Temporary: Disable SSL verification for localhost
$httpClient = new GuzzleHttp\Client(['verify' => false]);
$client->setHttpClient($httpClient);

// Debug: Log OAuth details
file_put_contents('oauth_debug.log', "Received code: " . ($_GET['code'] ?? 'none') . "\n", FILE_APPEND);

if (isset($_GET['code'])) {
    try {
        $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
        file_put_contents('oauth_debug.log', "Token response: " . print_r($token, true) . "\n", FILE_APPEND);
        if (!isset($token['error'])) {
            $_SESSION['access_token'] = $token;
            ob_end_clean();
            // Output HTML with JavaScript to refresh after 3 seconds
            echo <<<HTML
<!DOCTYPE html>
<html>
<head>
    <title>Processing OAuth</title>
    <style>
        body { font-family: Arial, sans-serif; text-align: center; padding: 50px; }
        .loader { border: 8px solid #f3f3f3; border-top: 8px solid #3498db; border-radius: 50%; width: 50px; height: 50px; animation: spin 1s linear infinite; margin: 20px auto; }
        @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
    </style>
</head>
<body>
    <h2>Processing your request...</h2>
    <div class="loader"></div>
    <p>Please wait, you will be redirected shortly.</p>
    <script>
        setTimeout(function() {
            window.location.reload();
        }, 3000); // Refresh after 3 seconds
    </script>
</body>
</html>
HTML;
            exit;
            // Redirect to index.php with POST data
            if (isset($_SESSION['pending_email_data'])) {
                $postData = $_SESSION['pending_email_data'];
                unset($_SESSION['pending_email_data']);
                $url = 'http://localhost/phpmail/phpMailer/index.php';
                $ch = curl_init($url);
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
                curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_exec($ch);
                curl_close($ch);
                exit;
            }
            header('Location: select_config.php');
            exit;
        } else {
            ob_end_clean();
            header('Location: select_config.php?error=' . urlencode('Token error: ' . $token['error']));
            exit;
        }
    } catch (Exception $e) {
        ob_end_clean();
        file_put_contents('oauth_debug.log', "Exception: " . $e->getMessage() . "\n", FILE_APPEND);
        header('Location: select_config.php?error=' . urlencode('OAuth error: ' . $e->getMessage()));
        exit;
    }
} else {
    ob_end_clean();
    header('Location: select_config.php?error=' . urlencode('No authorization code received'));
    exit;
}
?>
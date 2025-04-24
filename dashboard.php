<?php
require 'config.php';
redirectIfNotLoggedIn();
log_action($pdo, $_SESSION['user_id'], 'Viewed dashboard', 'dashboard.php');


// Path to your query_debug.log file
$log_file = 'query_debug.log';

// Check if the log file exists
if (file_exists($log_file)) {
    $log_contents = file_get_contents($log_file);

    // Count the number of occurrences of "Email sent via Gmail API" and "Email sent via SMTP"
    $gmail_sent = substr_count($log_contents, 'Email sent via Gmail API');
    $smtp_sent = substr_count($log_contents, 'Email sent via SMTP');
} else {
    // If log file doesn't exist, set counts to zero
    $gmail_sent = 0;
    $smtp_sent = 0;
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <title>Dashboard</title>
    <style>
        /* Flex container for placing the cards side by side */
        .charts-container {
            display: flex;
            justify-content: space-between; /* Space between the cards */
            gap: 20px; /* Space between the cards */
            margin-top: 20px;
        }

        /* Card Styling */
        .card {
            border: 1px solid #ddd;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 20px;
            width: 45%; /* Set width to 45% so two cards fit side by side */
            text-align: center;
        }

        .card h4 {
            margin-bottom: 15px;
        }

        canvas {
            width: 100% !important; /* Make the canvas responsive */
            height: 200px !important; /* Increase the height of the charts */
            background-color: white; /* Set the background color of the charts to white */
        }
    </style>
</head>
<body>
<?php include 'navbar.php'; ?>
<?php include 'sidebar.php'; ?>
<div style="margin-left: 220px; padding: 20px;">
    <h2>Welcome to your dashboard!</h2>

    <h3>Email Sent Statistics:</h3>

    <!-- Flexbox container for the graphs -->
    <div class="charts-container">
        <!-- Card for Gmail API Graph -->
        <div class="card">
            <h4>Gmail API Emails Sent</h4>
            <canvas id="gmailChart"></canvas> <!-- Gmail API Chart -->
        </div>

        <!-- Card for SMTP Graph -->
        <div class="card">
            <h4>SMTP Emails Sent</h4>
            <canvas id="smtpChart"></canvas> <!-- SMTP Chart -->
        </div>
    </div>
</div>
<?php include 'footer.php'; ?>

<script>
    // Get the data from PHP
    var gmailSent = <?php echo $gmail_sent; ?>;
    var smtpSent = <?php echo $smtp_sent; ?>;

    // Create the Gmail API chart
    var ctxGmail = document.getElementById('gmailChart').getContext('2d');
    var gmailChart = new Chart(ctxGmail, {
        type: 'bar', // Bar chart type
        data: {
            labels: ['Gmail API'], // Single label for Gmail API
            datasets: [{
                label: 'Emails Sent via Gmail API',
                data: [gmailSent], // Gmail API data
                backgroundColor: '#4e73df', // Color for Gmail API bar
                borderColor: '#4e73df',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    // Create the SMTP chart
    var ctxSmtp = document.getElementById('smtpChart').getContext('2d');
    var smtpChart = new Chart(ctxSmtp, {
        type: 'bar', // Bar chart type
        data: {
            labels: ['SMTP'], // Single label for SMTP
            datasets: [{
                label: 'Emails Sent via SMTP',
                data: [smtpSent], // SMTP data
                backgroundColor: '#1cc88a', // Color for SMTP bar
                borderColor: '#1cc88a',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
</script>

</body>
</html>

    <?php
    require 'config.php';
    redirectIfNotLoggedIn();

    $message = '';
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $name       = $_POST['name'] ?? '';
        $host       = $_POST['host'] ?? '';
        $port       = $_POST['port'] ?? '';
        $encryption = $_POST['encryption'] ?? '';
        $username   = $_POST['username'] ?? '';
        $password   = $_POST['password'] ?? '';

    }

    // Fetch existing SMTP records
    $stmt = $pdo->query("SELECT * FROM smtp_credentials ORDER BY id DESC");
    $smtp_credentials = $stmt->fetchAll(PDO::FETCH_ASSOC);
    ?>

    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>SMTP Configuration</title>
        <style>
            body {
                font-family: 'Segoe UI', sans-serif;
                background-color: #f3f4f6;
                margin: 0;
                padding: 20px;
            }

            .container {
                max-width: 1200px;
                margin: auto;
                background: white;
                padding: 30px;
                border-radius: 12px;
                box-shadow: 0 4px 10px rgba(0,0,0,0.1);
            }

            h1 {
                font-size: 1.8rem;
                margin-bottom: 20px;
                color: #111827;
            }

            form {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
                gap: 15px;
                margin-bottom: 30px;
            }

            input, select {
                padding: 10px;
                border: 1px solid #d1d5db;
                border-radius: 8px;
                font-size: 1rem;
            }

            button {
                grid-column: span 2;
                padding: 12px;
                background-color: #2563eb;
                color: white;
                border: none;
                border-radius: 8px;
                font-size: 1rem;
                cursor: pointer;
                transition: background-color 0.2s ease;
            }

            button:hover {
                background-color: #1d4ed8;
            }

            .message {
                margin-bottom: 20px;
                padding: 12px;
                border-radius: 8px;
                font-weight: bold;
            }

            .success {
                background-color: #d1fae5;
                color: #065f46;
            }

            .error {
                background-color: #fee2e2;
                color: #991b1b;
            }

            table {
                width: 100%;
                border-collapse: collapse;
                background-color: white;
            }

            th, td {
                padding: 12px 16px;
                border-bottom: 1px solid #e5e7eb;
                text-align: left;
            }

            th {
                background-color: #f9fafb;
                color: #374151;
            }

            tr:hover {
                background-color: #f1f5f9;
            }
        </style>
    </head>
    <body>
    <?php include 'navbar.php'; ?>
    <?php include 'sidebar.php'; ?>
    <div style="margin-left: 220px; padding: 20px;">
    <div class="container">
        <h1>SMTP Configuration</h1>

        <?php if ($message): ?>
            <div class="message <?= strpos($message, '✅') !== false ? 'success' : 'error' ?>">
                <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>



        <!-- <h2>📋 Saved SMTP Configurations</h2> -->
        <div style="overflow-x: auto;">
        <table style="min-width: 900px;">
            <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Host</th>
                <th>Port</th>
                <th>Encryption</th>
                <th>Username</th>
                <th>Password</th>
                <th>Action</th>
            </tr>
            </thead>
            <tbody>
            <?php if ($smtp_credentials): ?>
                <?php foreach ($smtp_credentials as $index => $smtp): ?>
                    <tr>
                        <td><?= htmlspecialchars($smtp['id']) ?></td>
                        <td><?= htmlspecialchars($smtp['name']) ?></td>
                        <td><?= htmlspecialchars($smtp['host']) ?></td>
                        <td><?= htmlspecialchars($smtp['port']) ?></td>
                        <td><?= htmlspecialchars($smtp['encryption']) ?></td>
                        <td><?= htmlspecialchars($smtp['username']) ?></td>
                        <td>
                <div style="display: flex; align-items: center;">
                    <input 
                        type="password" 
                        id="password_<?= $index ?>" 
                        value="<?= htmlspecialchars($smtp['password']) ?>" 
                        readonly 
                        style="border: none; background: transparent; font-family: monospace;"
                    />
                    <button 
                        type="button" 
                        onclick="togglePassword('password_<?= $index ?>', this)"
                        style="margin-left: 8px; border: none; background: none; cursor: pointer;"
                        title="Show/Hide Password"
                    >👁️</button>
                </div>
            </td>
            <td>
            <form method="POST" action="displaysmtp.php" onsubmit="return confirm('Are you sure you want to delete this SMTP config?');" >
        <input type="hidden" name="delete_id" value="<?= $smtp['id'] ?>">
        <button type="submit" style="background-color: #ef4444; color: white; padding: 6px 10px; border: none; border-radius: 5px; cursor: pointer;">
            🗑️ 
        </button>
    </form>
            </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="7">No SMTP credentials found.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
        </div>
    </div>
    </div>
    </body>
    <script>
function togglePassword(inputId, btn) {
    const input = document.getElementById(inputId);
    if (input.type === "password") {
        input.type = "text";
        btn.textContent = "🙈";
    } else {
        input.type = "password";
        btn.textContent = "👁️";
    }
}
</script>
<script>
    // Add a reload after form submission
    const form = document.querySelector('form');
    form.addEventListener('submit', function() {
        setTimeout(function() {
            window.location.reload();
        }, 100); // Small delay to ensure form is submitted first
    });
</script>
    </html>

    <?php 
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['delete_id'])) {
        $deleteId = (int) $_POST['delete_id'];
        $stmt = $pdo->prepare("DELETE FROM smtp_credentials WHERE id = ?");
        if ($stmt->execute([$deleteId])) {
            $message = '✅ SMTP config deleted successfully.';
        } else {
            $message = '❌ Failed to delete SMTP config.';
        }
    }
}

    ?>

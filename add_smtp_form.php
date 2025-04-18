<?php
require 'config.php';
redirectIfNotLoggedIn();


?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Add SMTP Credentials</title>
    <style>
         body { font-family: Arial, sans-serif; margin: 50px; background: #f7f7f7; }
        form { background: #fff; padding: 20px; border-radius: 8px; max-width: 500px; margin: auto; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        input, select { width: 100%; padding: 10px; margin-bottom: 15px; }
        button { background: #007bff; color: white; padding: 10px 15px; border: none; cursor: pointer; border-radius: 5px; }
    
        .navbar {
            background-color: #1f2937; /* dark gray */
            color: white;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            font-family: sans-serif;
        }
    
        .navbar-container {
            max-width: 1200px;
            margin: 0 0 0 230px;
            padding: 0 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            height: 60px;
        }
    
        .navbar-links {
            display: flex;
            gap: 16px;
        }
    
        .navbar-link {
            color: white;
            text-decoration: none;
            padding: 8px 12px;
            border-radius: 6px;
            font-size: 14px;
            transition: background-color 0.2s ease, color 0.2s ease;
        }
    
        .navbar-link:hover {
            background-color: #374151; /* slightly lighter gray */
            color: #d1d5db; /* light gray */
        }
    
        .logout-link {
            color: #f87171; /* red */
            font-weight: bold;
        }
    
        .logout-link:hover {
            background-color: #dc2626;
            color: white;
        }
        .sidebar {
        width: 220px;
        background-color: #1f2937; /* Dark gray */
        color: white;
        height: 100vh;
        position: fixed;
        top: 0;
        left: 0;
        padding: 20px;
        font-family: sans-serif;
        box-shadow: 2px 0 6px rgba(0, 0, 0, 0.1);
    }

    .sidebar h3 {
        margin-top: 0;
        font-size: 18px;
        margin-bottom: 20px;
        text-transform: uppercase;
        letter-spacing: 1px;
        color: #f3f4f6;
    }

    .sidebar ul {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .sidebar a {
        display: block;
        color: white;
        text-decoration: none;
        padding: 10px 14px;
        margin-bottom: 6px;
        border-radius: 6px;
        font-size: 14px;
        transition: background-color 0.2s ease, color 0.2s ease;
    }

    .sidebar a:hover {
        background-color: #374151; /* Lighter gray */
        color: #e5e7eb; /* Light text */
    }

    .sidebar a.logout {
        color: #f87171; /* Red */
        font-weight: bold;
    }

    .sidebar a.logout:hover {
        background-color: #dc2626;
        color: white;
    }
    </style>
    
</head>
<body>
    <!-- navbar.php -->
<!-- navbar.php -->
<nav class="navbar">
    <div class="navbar-container">
        <div class="navbar-links">
            <a href="index.php" class="navbar-link">Home</a>
            <a href="select_config.php" class="navbar-link">SMTP Configs</a>
            <a href="edit_template.php" class="navbar-link">Edit Template</a>
        </div>
        <div>
            <a href="logout.php" class="navbar-link logout-link">Logout</a>
        </div>
    </div>
</nav>

<!-- sidebar.php -->
<div class="sidebar">
    <h3>TP Response</h3>
    <ul>
        <li><a href="index.php">Dashboard</a></li>
        <li><a href="add_smtp_form.html">Add SMTP</a></li>
        <li><a href="select_config.php">SMTP Configs</a></li>
        <li><a href="edit_template.php">Edit Template</a></li>
        <li><a href="upload_email_list.php">Upload Data</a></li>
        <li><a href="logout.php" class="logout">Logout</a></li>
    </ul>
</div>


<form method="POST" action="add_smtp.php">
    <h2>Add SMTP Credentials</h2>

    <label>Name (e.g., AOL SMTP):</label>
    <input type="text" name="name" required>

    <label>Host:</label>
    <input type="text" name="host" placeholder="smtp.example.com" required>

    <label>Port:</label>
    <input type="number" name="port" placeholder="465 or 587" required>

    <label>Encryption:</label>
    <select name="encryption" required>
        <option value="">--Select--</option>
        <option value="ssl">SSL</option>
        <option value="tls">TLS</option>
    </select>

    <label>Username:</label>
    <input type="email" name="username" required>

    <label>Password:</label>
    <input type="text" name="password" required>

    <button type="submit">Save SMTP Config</button>
</form>
<footer style="background-color: #333; color: white; padding: 20px; text-align: center;">
    <p>&copy; 2025 Mailing SMTP Application</p>
</footer>
</body>
</html>

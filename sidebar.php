<!-- Add this <style> block inside the <head> of your main layout or page -->
<style>
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

<!-- sidebar.php -->
<div class="sidebar">
    <h3>TP Response</h3>
    <ul>
        <li><a href="dashboard.php">Dashboard</a></li>
        <li><a href="add_smtp_form.php">Add SMTP</a></li>
        <li><a href="select_config.php">SMTP Configs</a></li>
        <li><a href="edit_template.php">Edit Template</a></li>
        <li><a href="upload_email_list.php">Upload Data</a></li>
        <li><a href="logout.php" class="logout">Logout</a></li>
    </ul>
</div>

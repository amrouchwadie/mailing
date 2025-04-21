<!-- Add this <style> block inside the <head> of your main layout or page -->
<style>
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
</style>

<!-- navbar.php -->
<nav class="navbar">
    <div class="navbar-container">
        <div class="navbar-links">
            <a href="dashboard.php" class="navbar-link">Home</a>
            <a href="displaysmtp.php" class="navbar-link">SMTP List</a>
            <a href="select_config.php" class="navbar-link">SMTP Configs</a>
            <a href="logs.php" class="navbar-link">View Activity Logs</a>
            <a href="edit_template.php" class="navbar-link">Edit Template</a>
            <a href="#" class="navbar-link">Tools</a>
        </div>
        <div>
            <a href="logout.php" class="navbar-link logout-link">Logout</a>
        </div>
    </div>
</nav>

<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Navbar</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="navbar">
        <ul>
            <li><a href="../index.php">Home</a></li>

            <?php if (isset($_SESSION['role'])): ?>
                <?php if ($_SESSION['role'] == 'admin'): ?>
                    <li><a href="admindashboard.php">Admin Dashboard</a></li>
                    <li><a href="user_roles.php">User Roles</a></li> <!-- New Section for Admin -->
                <?php elseif ($_SESSION['role'] == 'client'): ?>
                    <li><a href="clientdashboard.php">Client Dashboard</a></li>
                <?php elseif ($_SESSION['role'] == 'user'): ?>
                    <li><a href="userdashboard.php">User Dashboard</a></li>
                <?php endif; ?>
                
                <li><a href="logout.php">Logout</a></li>
            <?php else: ?>
                <li><a href="auth/login.php">Login</a></li>
            <?php endif; ?>
        </ul>
    </div>
</body>
</html>

<?php
session_start();
if ($_SESSION['role'] != 'admin') {
    header("Location: ../auth/login.php");
    exit;
}

include('../includes/config.php');

// Fetch posts
$postQuery = "SELECT * FROM posts";
$posts = $conn->query($postQuery);

// Fetch counts
$adminCountQuery = "SELECT COUNT(*) AS total_admins FROM users WHERE role = 'admin'";
$userCountQuery = "SELECT COUNT(*) AS total_users FROM users WHERE role = 'user'";
$clientCountQuery = "SELECT COUNT(*) AS total_clients FROM users WHERE role = 'client'";

$totalAdmins = $conn->query($adminCountQuery)->fetch_assoc()['total_admins'];
$totalUsers = $conn->query($userCountQuery)->fetch_assoc()['total_users'];
$totalClients = $conn->query($clientCountQuery)->fetch_assoc()['total_clients'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Sharp" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="container">
        <aside>
            <div class="top">
                <div class="logo">
                    <img src="../assets/images/logo.png" alt="Logo">
                    <h2>SRI<span class="primary">LLP</span></h2>
                </div>
                <div class="close" id="close-btn">
                    <span class="material-icons-sharp">close</span>
                </div>
            </div>
            <div class="sidebar">
                <a href="#" class="active">
                    <span class="material-icons-sharp">dashboard</span>
                    <h3>Dashboard</h3>
                </a>
                <a href="manage_users.php">
                    <span class="material-icons-sharp">person_outline</span>
                    <h3>Manage Users</h3>
                </a>
                <a href="./logout.php">
                    <span class="material-icons-sharp">logout</span>
                    <h3>Logout</h3>
                </a>
            </div>
        </aside>

        <main>
            <h1>Admin Dashboard</h1>
            <div class="insights">
                <!-- Total Admins -->
                <div class="sales">
                    <span class="material-icons-sharp">supervisor_account</span>
                    <div class="middle">
                        <div class="left">
                            <h3>Total Admins</h3>
                            <h1><?= $totalAdmins ?></h1>
                        </div>
                    </div>
                    <small class="text-muted">System Administrators</small>
                </div>

                <!-- Total Users -->
                <div class="sales">
                    <span class="material-icons-sharp">group</span>
                    <div class="middle">
                        <div class="left">
                            <h3>Total Users</h3>
                            <h1><?= $totalUsers ?></h1>
                        </div>
                    </div>
                    <small class="text-muted">Registered Users</small>
                </div>

                <!-- Total Clients -->
                <div class="sales">
                    <span class="material-icons-sharp">business</span>
                    <div class="middle">
                        <div class="left">
                            <h3>Total Clients</h3>
                            <h1><?= $totalClients ?></h1>
                        </div>
                    </div>
                    <small class="text-muted">Business Clients</small>
                </div>
            </div>

            <div class="recent-orders">
                <h2>Recent Posts</h2>
                <table>
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Client ID</th>
                            <th>Content</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($post = $posts->fetch_assoc()) : ?>
                            <tr>
                                <td><?= $post['title'] ?></td>
                                <td><?= $post['client_id'] ?></td>
                                <td><?= $post['content'] ?></td>
                                <td>
                                    <a href="../posts/edit_post.php?id=<?= $post['id'] ?>">Edit</a> |
                                    <a href="../posts/delete_post.php?id=<?= $post['id'] ?>">Delete</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
                <a href="../posts/create_post.php">Create Post</a>
            </div>
        </main>

        <div class="right">
            <div class="top">
                <button id="menu-btn">
                    <span class="material-icons-sharp">menu</span>
                </button>
                <div class="theme-toggler">
                    <span class="material-icons-sharp active">light_mode</span>
                    <span class="material-icons-sharp">dark_mode</span>
                </div>
                <div class="profile">
                    <div class="info">
                        <p>Hey, <b>Admin</b></p>
                        <small class="text-muted">Administrator</small>
                    </div>
                    <div class="profile-photo">
                        <img src="../assets/images/profile-1.jpg" alt="Profile Picture">
                    </div>
                </div>
            </div>
            <div class="recent-updates">
                <h2>Recent Updates</h2>
                <!-- Placeholder for recent updates -->
            </div>
        </div>
    </div>

    <script>
        const sideMenu = document.querySelector("aside");
        const menuBtn = document.querySelector("#menu-btn");
        const closeBtn = document.querySelector("#close-btn");
        const themeToggler = document.querySelector(".theme-toggler");

        // Show Sidebar
        menuBtn.addEventListener("click", () => {
            sideMenu.style.display = "block";
        });

        // Hide Sidebar
        closeBtn.addEventListener("click", () => {
            sideMenu.style.display = "none";
        });

        // Toggle Theme
        themeToggler.addEventListener("click", () => {
            document.body.classList.toggle("dark-theme-variables");

            themeToggler.querySelector("span:nth-child(1)").classList.toggle("active");
            themeToggler.querySelector("span:nth-child(2)").classList.toggle("active");
        });
    </script>
</body>
</html>

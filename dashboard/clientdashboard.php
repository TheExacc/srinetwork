<?php
session_start();
if ($_SESSION['role'] != 'client') {
    header("Location: ../auth/login.php");
    exit;
}

include('../includes/config.php'); // Include database connection

// Fetch the posts created by the logged-in client
$clientId = $_SESSION['user_id']; // Assuming the user ID is stored in session
$postQuery = "SELECT * FROM posts WHERE client_id = $clientId";
$posts = $conn->query($postQuery);

?>

<!DOCTYPE html>
<html lang="en">
<head>
<link href="https://cdn.jsdelivr.net/npm/@sweetalert2/theme-dark@4/dark.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.js"></script>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Client Dashboard</title>
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
                  <!-- Create Post link -->
    <a href="../posts/create_post.php">
        <span class="material-icons-sharp">post_add</span>
        <h3>Create Post</h3>
    </a>
                
                <a href="./logout.php">
                    <span class="material-icons-sharp">logout</span>
                    <h3>Logout</h3>
                </a>
            </div>
        </aside>

        <main>
            <h1>Client Dashboard</h1>
            <div class="recent-orders">
                <h2>Your Posts</h2>
                <table>
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Content</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($post = $posts->fetch_assoc()) : ?>
                            <tr>
                                <td><?= $post['title'] ?></td>
                                <td><?= $post['content'] ?></td>
                                <td>
                                <a href="#" class="delete-post" data-id="<?= $post['id'] ?>">Delete</a>
</td>

                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
                <a href="../posts/create_post.php">Create New Post</a>
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
                        <p>Hey, <b>Client</b></p>
                        <small class="text-muted">Business Client</small>
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
<style>
    .dark-title{
       
  color: white;
    }
    .light-title{
       
       color: black;
         }

    </style>
</html>

<script>
  // Assuming you're using the body class to manage dark/light mode


// Assuming you're using the body class to manage dark/light mode

document.querySelectorAll('.delete-post').forEach((deleteBtn) => {
    deleteBtn.addEventListener('click', function(event) {
        event.preventDefault();
        const postId = this.getAttribute('data-id'); // Get the post ID from the data-id attribute

        // Check if dark theme is applied
        const isDarkTheme = document.body.classList.contains('dark-theme-variables');
        const titleClass = isDarkTheme ? 'dark-title' : 'light-title';

        // SweetAlert confirmation dialog
        Swal.fire({
            title: 'Are you sure?',
            text: 'This action will delete the post and all related data!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'Cancel',
            reverseButtons: true,
            // Adjust SweetAlert modal style based on the theme
            background: isDarkTheme ? '#333' : '#fff', // Dark background for dark mode, light for light mode
            color: isDarkTheme ? '#fff' : '#000', // Text color (white for dark mode, black for light mode)
            titleColor: isDarkTheme ? '#fff' : '#000', // Title color (white for dark mode, black for light mode)
            confirmButtonColor: '#d33', // Red confirm button for both themes
            cancelButtonColor: '#7380ec', // Blue cancel button for both themes
            customClass: {
        title: titleClass // Apply the custom title class
    }
        }).then((result) => {
            if (result.isConfirmed) {
                // Redirect to delete_post.php with the post ID
                window.location.href = `../posts/delete_post.php?id=${postId}`;
            }
        });
    });
});


    </script>

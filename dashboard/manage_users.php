<?php
session_start();
if ($_SESSION['role'] != 'admin') {
    header("Location: ../auth/login.php");
    exit;
}

include('../includes/config.php');

// Fetch users
$query = "SELECT id, username, role FROM users";
$result = $conn->query($query);

// Handle form submission to update roles
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['roles'])) {
    $roles = $_POST['roles'];

    foreach ($roles as $userId => $role) {
        $updateQuery = "UPDATE users SET role = ? WHERE id = ?";
        $stmt = $conn->prepare($updateQuery);
        $stmt->bind_param("si", $role, $userId);
        $stmt->execute();
    }

    header("Location: manage_users.php?success=1");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users - Admin Dashboard</title>
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
                <a href="admindashboard.php">
                    <span class="material-icons-sharp">dashboard</span>
                    <h3>Dashboard</h3>
                </a>
                <a href="manage_users.php" class="active">
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
            <h1>Manage Users</h1>
            <div class="insights">
                <!-- You can add more stats here if needed, such as the number of users -->
            </div>

            <form id="role-form" method="POST" action="manage_users.php">
                <div class="recent-orders">
                    <h2>User List</h2>
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Username</th>
                                <th>Role</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $result->fetch_assoc()) : ?>
                                <tr>
                                    <td><?= htmlspecialchars($row['id']) ?></td>
                                    <td><?= htmlspecialchars($row['username']) ?></td>
                                    <td>
                                        <select name="roles[<?php echo $row['id']; ?>]">
                                            <option value="admin" <?php if ($row['role'] == 'admin') echo 'selected'; ?>>Admin</option>
                                            <option value="client" <?php if ($row['role'] == 'client') echo 'selected'; ?>>Client</option>
                                            <option value="user" <?php if ($row['role'] == 'user') echo 'selected'; ?>>User</option>
                                        </select>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                    <!-- Save Button will trigger form submission -->
                    <a href="#" class="save-btn" id="save-roles">
                        <span class="material-icons-sharp">save</span>
                        <h3>Save User Roles</h3>
                    </a>
                </div>
            </form>
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
        const saveButton = document.getElementById("save-roles");

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

        // Submit the form when "Save User Roles" is clicked
        saveButton.addEventListener("click", function(event) {
            event.preventDefault();  // Prevent default anchor behavior
            document.getElementById("role-form").submit();  // Submit the form
        });
    </script>
</body>
</html>

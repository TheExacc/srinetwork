<?php
session_start();
include('../includes/config.php'); // Include database connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Query to check user
    $query = "SELECT * FROM users WHERE username = ? AND password = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ss", $username, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = $user['role'];

        // Redirect based on role
        if ($user['role'] == 'admin') {
            header("Location: ../dashboard/admindashboard.php");
        } elseif ($user['role'] == 'client') {
            header("Location: ../dashboard/clientdashboard.php");
        } else {
            header("Location: ../dashboard/userdashboard.php");
        }
    } else {
        echo "Invalid credentials!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Sharp" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="container">
        <!-- Sidebar (minimal for login page) -->
        <aside>
            <div class="top">
                <div class="logo">
                    <img src="../assets/images/logo.png" alt="Logo">
                    <h2>SRI<span class="primary">LLP</span></h2>
                </div>
            </div>
        </aside>

        <!-- Main Content Area -->
        <main>
            <div class="login-container">
                <h2>Login</h2>
                
                <!-- Error message -->
                <?php if (isset($error_message)): ?>
                    <div class="error-message"><?= $error_message ?></div>
                <?php endif; ?>

                <form method="POST" action="">
                    <div class="input-group">
                        <label for="username">Username</label>
                        <input type="text" name="username" id="username" required>
                    </div>
                    <div class="input-group">
                        <label for="password">Password</label>
                        <input type="password" name="password" id="password" required>
                    </div>
                    <button type="submit" class="login-btn">Login</button>
                </form>
                <a href="#" class="forgot-password">Forgot Password?</a>
            </div>
        </main>
    </div>

    <script>
        const sideMenu = document.querySelector("aside");
        const themeToggler = document.querySelector(".theme-toggler");

        // Toggle Theme
        themeToggler.addEventListener("click", () => {
            document.body.classList.toggle("dark-theme-variables");

            themeToggler.querySelector("span:nth-child(1)").classList.toggle("active");
            themeToggler.querySelector("span:nth-child(2)").classList.toggle("active");
        });
    </script>
</body>
</html>
<style>
  /* General form styling */
.login-container {
    width: 100%;
    max-width: 400px;
    margin: 0 auto;
    padding: var(--padding-1);
    background-color: var(--color-white);
    border-radius: var(--border-radius-2);
    box-shadow: var(--box-shadow);
}

/* Input fields */
.input-group {
    margin-bottom: 1.4rem;
}

.input-group label {
    display: block;
    margin-bottom: 0.8rem;
    font-size: 1rem;
    color: var(--color-dark);
}

.input-group input {
    width: 100%;
    padding: 0.8rem;
    font-size: 1rem;
    border: 1px solid var(--color-light);
    border-radius: var(--border-radius-1);
    background-color: var(--color-background);
    color: var(--color-dark);
}

/* Focus effect for inputs */
.input-group input:focus {
    border-color: var(--color-primary);
    outline: none;
}

/* Login button */
.login-btn {
    width: 100%;
    padding: 0.8rem;
    background-color: var(--color-primary);
    color: var(--color-white);
    font-size: 1rem;
    border: none;
    border-radius: var(--border-radius-1);
    cursor: pointer;
    transition: background-color 300ms ease;
}

.login-btn:hover {
    background-color: var(--color-primary-variant);
}

/* Forgot password link */
.forgot-password {
    display: block;
    margin-top: 1.2rem;
    text-align: center;
    font-size: 0.8rem;
    color: var(--color-primary);
    text-decoration: none;
}

.forgot-password:hover {
    text-decoration: underline;
}

/* Error message styling */
.error-message {
    color: var(--color-danger);
    font-weight: 600;
    margin-bottom: 1.6rem;
    text-align: center;
}


    </style>
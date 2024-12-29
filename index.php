<?php
// Redirect to the login page after 3 seconds
header("refresh:2; url=auth/login.php");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to the Forum</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="login-container">
        <div class="content">
            <h1 class="heading">Welcome to Our Forum</h1>
            <p class="subheading">You are being redirected to the login page...</p>
            <div class="animated-button">
                <a href="auth/login.php" class="login-btn">Go to Login</a>
            </div>
        </div>
    </div>
</body>
</html>
<style>
    /* Adjustments to fit the login design */
.login-container {
    width: 100%;
    max-width: 400px;
    margin: 233.7px auto;
    padding: var(--padding-2);
    background-color: var(--color-white);
    border-radius: var(--border-radius-2);
    box-shadow: var(--box-shadow);
    text-align: center;
}

.heading {
    font-size: 2rem;
    font-weight: 600;
    color: var(--color-dark);
}

.subheading {
    font-size: 1rem;
    color: var(--color-dark);
    margin-top: 10px;
}

.animated-button {
    margin-top: 1.5rem;
}

.login-btn {
    margin:22px;
    padding: 1rem 2rem;
    background-color: var(--color-primary);
    color: var(--color-white);
    font-size: 1.2rem;
    text-decoration: none;
    border-radius: var(--border-radius-1);
    transition: background-color 300ms ease;
    display: inline-block;
}

.login-btn:hover {
    background-color: var(--color-primary-variant);
}

    </style>

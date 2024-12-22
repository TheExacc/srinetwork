<?php
// Redirect to the login page after 3 seconds
header("refresh:3; url=auth/login.php");
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
    <div class="container">
        <div class="content">
            <h1 class="heading">Welcome to Our Forum</h1>
            <p class="subheading">You are being redirected to the login page...</p>
            <div class="animated-button">
                <a href="auth/login.php">Go to Login</a>
            </div>
        </div>
    </div>
</body>
</html>

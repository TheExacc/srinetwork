<?php
session_start();
if ($_SESSION['role'] != 'client') {
    header("Location: ../auth/login.php");
    exit;
}

include('../includes/config.php'); // Include database connection

// Handle post creation
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $_POST['title'];
    $content = $_POST['content'];
    $emails = explode(',', $_POST['emails']); // List of emails to assign

    // Insert post
    $query = "INSERT INTO posts (title, content, client_id) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssi", $title, $content, $_SESSION['user_id']);
    $stmt->execute();

    $post_id = $stmt->insert_id; // Get the last inserted post ID

    // Assign users to post
    foreach ($emails as $email) {
        $email = trim($email);
        $user_query = "SELECT id FROM users WHERE email = ?";
        $user_stmt = $conn->prepare($user_query);
        $user_stmt->bind_param("s", $email);
        $user_stmt->execute();
        $user_result = $user_stmt->get_result();

        if ($user_result->num_rows > 0) {
            $user = $user_result->fetch_assoc();
            $mapping_query = "INSERT INTO post_user_mapping (post_id, user_id) VALUES (?, ?)";
            $mapping_stmt = $conn->prepare($mapping_query);
            $mapping_stmt->bind_param("ii", $post_id, $user['id']);
            $mapping_stmt->execute();
        }
    }
    echo "Post created and users assigned!";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Post</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <h1>Create Post</h1>
    <form method="POST" action="">
        <label for="title">Post Title</label>
        <input type="text" name="title" required>
        <br>
        <label for="content">Content</label>
        <textarea name="content" required></textarea>
        <br>
        <label for="emails">Assign to Users (comma-separated emails)</label>
        <input type="text" name="emails" required>
        <br>
        <button type="submit">Create Post</button>
    </form>
</body>
</html>

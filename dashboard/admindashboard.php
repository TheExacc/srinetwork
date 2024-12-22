<?php
session_start();
if ($_SESSION['role'] != 'admin') {
    header("Location: ../auth/login.php");
    exit;
}

include('../includes/config.php'); // Include database connection

// Fetch all posts
$query = "SELECT * FROM posts";
$result = $conn->query($query);
?>
<?php include('../navbar.php'); ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <h1>Admin Dashboard</h1>
    <a href="../posts/create_post.php">Create Post</a>
    <h2>Posts</h2>
    <table>
        <tr>
            <th>Title</th>
            <th>Content</th>
            <th>Client</th>
            <th>Actions</th>
        </tr>
        <?php while ($post = $result->fetch_assoc()) { ?>
        <tr>
            <td><?php echo $post['title']; ?></td>
            <td><?php echo $post['content']; ?></td>
            <td><?php echo $post['client_id']; ?></td>
            <td><a href="../posts/edit_post.php?id=<?php echo $post['id']; ?>">Edit</a> | <a href="../posts/delete_post.php?id=<?php echo $post['id']; ?>">Delete</a></td>
        </tr>
        <?php } ?>
    </table>
</body>
</html>



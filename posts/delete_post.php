<?php
session_start();
if ($_SESSION['role'] != 'client') {
    header("Location: ../auth/login.php");
    exit;
}

include('../includes/config.php'); // Include the database connection

// Check if the post ID is set in the URL
if (isset($_GET['id'])) {
    $postId = $_GET['id'];

    // Start a transaction to ensure that all deletions happen together
    $conn->begin_transaction();

    try {
        // Delete related question options
        $stmt1 = $conn->prepare("DELETE FROM question_options WHERE question_id IN (SELECT id FROM questions WHERE post_id = ?)");
        $stmt1->bind_param("i", $postId);
        $stmt1->execute();

        // Delete related answers
        $stmt2 = $conn->prepare("DELETE FROM answers WHERE post_id = ?");
        $stmt2->bind_param("i", $postId);
        $stmt2->execute();

        // Delete related questions
        $stmt3 = $conn->prepare("DELETE FROM questions WHERE post_id = ?");
        $stmt3->bind_param("i", $postId);
        $stmt3->execute();

        // Delete user mappings for the post
        $stmt4 = $conn->prepare("DELETE FROM post_user_mapping WHERE post_id = ?");
        $stmt4->bind_param("i", $postId);
        $stmt4->execute();

        // Finally, delete the post itself
        $stmt5 = $conn->prepare("DELETE FROM posts WHERE id = ?");
        $stmt5->bind_param("i", $postId);
        $stmt5->execute();

        // Commit the transaction
        $conn->commit();

        // Redirect to the dashboard after successful deletion
        header("Location: ../dashboard/clientdashboard.php");
        exit;
    } catch (Exception $e) {
        // If an error occurs, roll back the transaction
        $conn->rollback();
        echo "Error: " . $e->getMessage();
    }
} else {
    // If no post ID is provided, redirect to dashboard
    header("Location: ../dashboard/clientdashboard.php");
    exit;
}

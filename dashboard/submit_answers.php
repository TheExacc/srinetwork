<?php
session_start();
include('../includes/config.php');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo "You must be logged in to submit answers.";
    exit;
}

// Check if post_id is provided
if (!isset($_POST['post_id']) || empty($_POST['post_id'])) {
    echo "Post ID is required.";
    exit;
}

$post_id = $_POST['post_id'];
$user_id = $_SESSION['user_id'];

// Loop through answers and save them
foreach ($_POST as $key => $answer) {
    // Skip the post_id field
    if ($key === 'post_id') continue;

    // Extract the question_id from the form field name (e.g., answer_1 => 1)
    if (preg_match('/answer_(\d+)/', $key, $matches)) {
        $question_id = $matches[1];

        // Insert or update the answer
        $query = "INSERT INTO answers (post_id, user_id, question_id, answer) 
                  VALUES (?, ?, ?, ?) 
                  ON DUPLICATE KEY UPDATE answer = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("iiiss", $post_id, $user_id, $question_id, $answer, $answer);
        $stmt->execute();
    }
}

echo "Your answers have been submitted successfully.";
?>

<?php
include('../includes/config.php');

// Check if post_id is provided
if (!isset($_GET['post_id']) || empty($_GET['post_id'])) {
    echo json_encode(['error' => 'Post ID is required']);
    exit;
}

$post_id = $_GET['post_id'];

// Fetch questions related to the post
$query = "SELECT q.id, q.question_text, q.question_type, o.option_text 
          FROM questions q 
          LEFT JOIN options o ON q.id = o.question_id 
          WHERE q.post_id = ? 
          ORDER BY q.id";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $post_id);
$stmt->execute();
$result = $stmt->get_result();

$questions = [];
$current_question_id = null;
$question = null;

while ($row = $result->fetch_assoc()) {
    // If it's a new question, create a new question array
    if ($row['id'] != $current_question_id) {
        if ($question) {
            // Save the previous question (if any)
            $questions[] = $question;
        }
        
        $question = [
            'id' => $row['id'],
            'question_text' => $row['question_text'],
            'question_type' => $row['question_type'],
            'options' => []
        ];
        $current_question_id = $row['id'];
    }

    // Add options for the question (if any)
    if ($row['option_text']) {
        $question['options'][] = $row['option_text'];
    }
}

// Don't forget to add the last question
if ($question) {
    $questions[] = $question;
}

echo json_encode($questions);
?>

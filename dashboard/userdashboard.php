<?php
session_start();
if ($_SESSION['role'] != 'user') {
    header("Location: ../auth/login.php");
    exit;
}

include('../includes/config.php'); // Include database connection

// Fetch posts assigned to the user
$query = "SELECT p.id, p.title, p.content FROM posts p
          JOIN post_user_mapping pum ON p.id = pum.post_id
          WHERE pum.user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();

// Handle fetching questions for a specific post
if (isset($_GET['post_id'])) {
    $post_id = $_GET['post_id'];
    $questions_query = "SELECT id, question_text, question_type, options FROM questions WHERE post_id = ?";
    $stmt = $conn->prepare($questions_query);
    $stmt->bind_param("i", $post_id);
    $stmt->execute();
    $questions_result = $stmt->get_result();
    $questions = [];
    while ($row = $questions_result->fetch_assoc()) {
        $row['options'] = json_decode($row['options']); // Decode JSON options
        $questions[] = $row;
    }
    echo json_encode($questions);
    exit;
}

// Handle submitting answers
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_answers'])) {
    $post_id = $_POST['post_id'];
    foreach ($_POST as $key => $value) {
        if (strpos($key, 'answer_') === 0) {
            $question_id = str_replace('answer_', '', $key);
            $answer = $value;

            $insert_answer_query = "INSERT INTO answers (user_id, post_id, question_id, answer) VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($insert_answer_query);
            $stmt->bind_param("iiis", $_SESSION['user_id'], $post_id, $question_id, $answer);
            $stmt->execute();
        }
    }
    echo "Answers submitted successfully!";
    exit;
}
?>

<?php include('../navbar.php'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .post-bubble {
            background-color: #f0f0f0;
            padding: 15px;
            border-radius: 25px;
            margin: 15px;
            cursor: pointer;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s ease-in-out;
        }

        .post-bubble:hover {
            transform: scale(1.05);
        }

        .questions-container {
            display: none;
            margin-top: 20px;
            background-color: rgba(0, 0, 0, 0.1);
            padding: 20px;
            border-radius: 8px;
        }

        .question {
            margin-bottom: 15px;
        }

        .submit-btn {
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            border: none;
            cursor: pointer;
            border-radius: 5px;
            margin-top: 20px;
        }

        .submit-btn:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>

    <h1>User Dashboard</h1>
    <h2>Your Posts</h2>

    <div id="posts-container">
        <?php while ($post = $result->fetch_assoc()) { ?>
            <div class="post-bubble" onclick="loadQuestions(<?php echo $post['id']; ?>)">
                <h3><?php echo htmlspecialchars($post['title']); ?></h3>
                <p><?php echo htmlspecialchars($post['content']); ?></p>
            </div>
        <?php } ?>
    </div>

    <div id="questions-container" class="questions-container">
        <form id="questions-form" method="POST">
            <input type="hidden" name="post_id" id="post_id" value="">
            <!-- Questions will be dynamically loaded here -->
            <div id="questions-list"></div>
            <button type="submit" name="submit_answers" class="submit-btn">Submit Answers</button>
        </form>
    </div>

    <script>
        function loadQuestions(postId) {
            const questionsContainer = document.getElementById('questions-container');
            const questionsList = document.getElementById('questions-list');
            questionsContainer.style.display = 'none'; // Hide container initially
            questionsList.innerHTML = ''; // Clear existing content
            document.getElementById('post_id').value = postId; // Set post_id in form

            // Fetch questions using AJAX
            fetch(`userdashboard.php?post_id=${postId}`)
                .then(response => response.json())
                .then(data => {
                    questionsContainer.style.display = 'block'; // Show container

                    // Display each question
                    data.forEach(question => {
                        const questionDiv = document.createElement('div');
                        questionDiv.classList.add('question');

                        const questionText = document.createElement('p');
                        questionText.textContent = question.question_text;
                        questionDiv.appendChild(questionText);

                        if (question.question_type === 'single-select' || question.question_type === 'multi-select') {
                            const select = document.createElement('select');
                            select.name = `answer_${question.id}`;
                            question.options.forEach(option => {
                                const optionElement = document.createElement('option');
                                optionElement.value = option;
                                optionElement.textContent = option;
                                select.appendChild(optionElement);
                            });
                            questionDiv.appendChild(select);
                        } else if (question.question_type === 'open-ended') {
                            const textarea = document.createElement('textarea');
                            textarea.name = `answer_${question.id}`;
                            questionDiv.appendChild(textarea);
                        }

                        questionsList.appendChild(questionDiv);
                    });
                })
                .catch(error => console.error('Error loading questions:', error));
        }
    </script>

</body>
</html>

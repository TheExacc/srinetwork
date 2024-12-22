<?php
session_start();
if ($_SESSION['role'] != 'client') {
    header("Location: ../auth/login.php");
    exit;
}

include('../includes/config.php'); // Include database connection

// Fetch posts created by the client
$query = "SELECT id, title, content FROM posts WHERE client_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();
$posts = $result->fetch_all(MYSQLI_ASSOC);

// Handle post creation and survey creation
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['create_survey'])) {
    $title = $_POST['title'];
    $content = $_POST['content'];
    $emails = explode(',', $_POST['emails']); // List of emails to assign
    $questions = $_POST['question_text']; // Collect all questions
    $question_types = $_POST['question_type']; // Collect all question types
    $options = $_POST['options']; // Collect all options for questions

    // Insert post into the database
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

    // Add questions to the post with options
    foreach ($questions as $key => $question_text) {
        if (!empty($question_text)) {
            $question_type = $question_types[$key]; // Get question type
            $question_query = "INSERT INTO questions (post_id, question_text, question_type) VALUES (?, ?, ?)";
            $stmt_question = $conn->prepare($question_query);
            $stmt_question->bind_param("iss", $post_id, $question_text, $question_type);
            $stmt_question->execute();

            // Add options for single-select and multi-select questions
            $question_id = $stmt_question->insert_id;
            if (isset($options[$key])) {
                foreach ($options[$key] as $option_text) {
                    if (!empty($option_text)) {
                        $option_query = "INSERT INTO question_options (question_id, option_text) VALUES (?, ?)";
                        $stmt_option = $conn->prepare($option_query);
                        $stmt_option->bind_param("is", $question_id, $option_text);
                        $stmt_option->execute();
                    }
                }
            }
        }
    }

    echo "Post created and users assigned!";
}
?>

<?php include('../navbar.php'); ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Client Dashboard</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="container">
        <h1>Client Dashboard</h1>

        <!-- Display Posts -->
        <div class="post-list">
            <h2>Your Posts</h2>
            <?php if ($posts): ?>
                <div class="post-cards">
                    <?php foreach ($posts as $post): ?>
                        <div class="post-card">
                            <h3><?php echo htmlspecialchars($post['title']); ?></h3>
                            <p><?php echo htmlspecialchars($post['content']); ?></p>
                            <a href="view_post.php?id=<?php echo $post['id']; ?>">View Post</a>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p>You haven't created any posts yet.</p>
            <?php endif; ?>
        </div>

        <!-- Create Survey Form -->
        <div id="createSurveyForm" class="create-survey-form" style="display:none;">
            <form method="POST" action="">
                <h2>Create Survey</h2>
                <label for="title">Post Title</label>
                <input type="text" name="title" required>
                <br>

                <label for="content">Content</label>
                <textarea name="content" required></textarea>
                <br>

                <label for="emails">Assign to Users (comma-separated emails)</label>
                <input type="text" name="emails" required>
                <br>

                <!-- Dynamic Questions Section -->
                <div id="questionsContainer">
                    <div class="question-field">
                        <label for="question">Question Text</label>
                        <textarea name="question_text[]" required></textarea>
                        <br>

                        <label for="question_type">Question Type</label>
                        <select name="question_type[]" onchange="toggleOptionsInput(this)" required>
                            <option value="single-select">Single Select</option>
                            <option value="multi-select">Multi Select</option>
                            <option value="open-ended">Open-ended</option>
                        </select>

                        <!-- Options Section for Single and Multi-Select -->
                        <div class="options-container" style="display:none;">
                            <label>Options (comma separated)</label>
                            <input type="text" name="options[0][]" placeholder="Option 1">
                            <br>
                            <button type="button" onclick="addOption(this)">Add More Options</button>
                        </div>

                        <!-- Delete Question -->
                        <button type="button" onclick="deleteQuestion(this)">Delete Question</button>
                    </div>
                </div>

                <button type="button" onclick="addQuestion()">Add Another Question</button>
                <br>
                <button type="submit" name="create_survey">Create Survey</button>
            </form>
        </div>

        <button onclick="showCreateSurveyForm()">Create New Survey</button>
    </div>

    <script>
        // Show the Create Survey Form
        function showCreateSurveyForm() {
            document.getElementById('createSurveyForm').style.display = 'block';
        }

        // Add Another Question Field
        function addQuestion() {
            const container = document.getElementById('questionsContainer');
            const questionDiv = document.createElement('div');
            questionDiv.classList.add('question-field');

            // Create the question input elements
            const questionTextInput = document.createElement('textarea');
            questionTextInput.name = 'question_text[]';
            questionTextInput.placeholder = 'Enter your question here...';
            questionTextInput.required = true;

            const questionTypeSelect = document.createElement('select');
            questionTypeSelect.name = 'question_type[]';
            questionTypeSelect.innerHTML = `
                <option value="single-select">Single Select</option>
                <option value="multi-select">Multi Select</option>
                <option value="open-ended">Open-ended</option>
            `;
            questionTypeSelect.setAttribute('onchange', 'toggleOptionsInput(this)');

            // Create the options input section
            const optionsContainer = document.createElement('div');
            optionsContainer.classList.add('options-container');
            optionsContainer.style.display = 'none';

            const optionInput = document.createElement('input');
            optionInput.type = 'text';
            optionInput.name = 'options[0][]';
            optionInput.placeholder = 'Option 1';
            optionsContainer.appendChild(optionInput);

            const addOptionButton = document.createElement('button');
            addOptionButton.type = 'button';
            addOptionButton.textContent = 'Add More Options';
            addOptionButton.setAttribute('onclick', 'addOption(this)');
            optionsContainer.appendChild(addOptionButton);

            // Append elements to the question field
            questionDiv.appendChild(questionTextInput);
            questionDiv.appendChild(questionTypeSelect);
            questionDiv.appendChild(optionsContainer);

            // Add delete question button
            const deleteButton = document.createElement('button');
            deleteButton.type = 'button';
            deleteButton.textContent = 'Delete Question';
            deleteButton.setAttribute('onclick', 'deleteQuestion(this)');
            questionDiv.appendChild(deleteButton);

            container.appendChild(questionDiv);
        }

        // Show/Hide options input based on question type
        function toggleOptionsInput(selectElement) {
            const optionsContainer = selectElement.closest('.question-field').querySelector('.options-container');
            if (selectElement.value === 'single-select' || selectElement.value === 'multi-select') {
                optionsContainer.style.display = 'block';
            } else {
                optionsContainer.style.display = 'none';
            }
        }

        // Add More Options
        function addOption(button) {
            const optionsContainer = button.closest('.options-container');
            const newOption = document.createElement('input');
            newOption.type = 'text';
            newOption.name = 'options[0][]';
            newOption.placeholder = 'Option ' + (optionsContainer.querySelectorAll('input').length + 1);
            optionsContainer.insertBefore(newOption, button);
        }

        // Delete Question
        function deleteQuestion(button) {
            const questionDiv = button.closest('.question-field');
            questionDiv.remove();
        }
    </script>
</body>
</html>

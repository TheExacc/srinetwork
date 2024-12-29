<?php
session_start();
if ($_SESSION['role'] != 'client') {
    header("Location: ../auth/login.php");
    exit;
}

include('../includes/config.php'); // Include the database connection

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['json_file'])) {
    $jsonFile = $_FILES['json_file'];

    // Check if the file is a valid JSON file
    if ($jsonFile['type'] != 'application/json') {
        $error = "Please upload a valid JSON file.";
    } elseif ($jsonFile['error'] !== 0) {
        $error = "Error uploading the file. Please try again.";
    } else {
        // Read the uploaded JSON file
        $jsonData = file_get_contents($jsonFile['tmp_name']);
        $data = json_decode($jsonData, true);

        if ($data === null) {
            $error = "Invalid JSON format.";
        } else {
            // Extract post title and content
            $postTitle = $data['title'] ?? '';
            $postContent = $data['content'] ?? '';

            // Insert post into the database
            $clientId = $_SESSION['user_id'];
            $stmt = $conn->prepare("INSERT INTO posts (client_id, title, content) VALUES (?, ?, ?)");
            $stmt->bind_param("iss", $clientId, $postTitle, $postContent);
            $stmt->execute();
            $postId = $conn->insert_id; // Get the ID of the inserted post

            // Process the questions and options from the JSON data
            if (isset($data['questions']) && is_array($data['questions'])) {
                foreach ($data['questions'] as $question) {
                    $questionText = $question['question_text'] ?? '';
                    $questionType = $question['question_type'] ?? '';
                    $questionStmt = $conn->prepare("INSERT INTO questions (post_id, question_text, question_type) VALUES (?, ?, ?)");
                    $questionStmt->bind_param("iss", $postId, $questionText, $questionType);
                    $questionStmt->execute();
                    $questionId = $conn->insert_id;

                    // Insert options for the question
                    if (isset($question['options']) && is_array($question['options'])) {
                        foreach ($question['options'] as $option) {
                            $optionText = $option;
                            $optionStmt = $conn->prepare("INSERT INTO question_options (question_id, option_text) VALUES (?, ?)");
                            $optionStmt->bind_param("is", $questionId, $optionText);
                            $optionStmt->execute();
                        }
                    }

                    // Insert the correct answer(s) for the question
                    if (isset($question['correct_answer'])) {
                        $correctAnswer = $question['correct_answer'];
                        $answerStmt = $conn->prepare("INSERT INTO answers (post_id, user_id, question_id, answer) VALUES (?, ?, ?, ?)");
                        $answerStmt->bind_param("iiis", $postId, $clientId, $questionId, $correctAnswer);
                        $answerStmt->execute();
                    }
                }
            }

            // Process the user email mappings (users who should receive the post)
            if (isset($data['users']) && is_array($data['users'])) {
                foreach ($data['users'] as $email) {
                    // Get user ID based on email
                    $userStmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
                    $userStmt->bind_param("s", $email);
                    $userStmt->execute();
                    $result = $userStmt->get_result();
                    if ($user = $result->fetch_assoc()) {
                        $userId = $user['id'];

                        // Insert post-user mapping
                        $mappingStmt = $conn->prepare("INSERT INTO post_user_mapping (post_id, user_id) VALUES (?, ?)");
                        $mappingStmt->bind_param("ii", $postId, $userId);
                        $mappingStmt->execute();
                    }
                }
            }

            $success = "Post and questions created successfully!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Post with Questions</title>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Sharp" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="container">
        <aside>
            <div class="top">
                <div class="logo">
                    <img src="../assets/images/logo.png" alt="Logo">
                    <h2>SRI<span class="primary">LLP</span></h2>
                </div>
                <div class="close" id="close-btn">
                    <span class="material-icons-sharp">close</span>
                </div>
            </div>
            <div class="sidebar">
                <a href="../dashboard/clientdashboard.php">
                    <span class="material-icons-sharp">dashboard</span>
                    <h3>Dashboard</h3>
                </a>
                <a href="../posts/create_post.php" class="active">
                    <span class="material-icons-sharp">post_add</span>
                    <h3>Create Post</h3>
                </a>
                <a href="./logout.php">
                    <span class="material-icons-sharp">logout</span>
                    <h3>Logout</h3>
                </a>
            </div>
        </aside>

        <main>
            <h1>Create New Post with Questions</h1>
            <!-- Display success or error message -->
            <?php if (isset($success)) : ?>
                <div class="success-message"><?= $success; ?></div>
            <?php elseif (isset($error)) : ?>
                <div class="error-message"><?= $error; ?></div>
            <?php endif; ?>

            <!-- File upload form -->
            <form action="create_post.php" method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="json_file">Upload JSON File</label>
                    <input type="file" name="json_file" id="json_file" accept="application/json" required>
                </div>
                <button type="submit">Create Post</button>
            </form>
        </main>

        <div class="right">
            <div class="top">
                <button id="menu-btn">
                    <span class="material-icons-sharp">menu</span>
                </button>
                <div class="theme-toggler">
                    <span class="material-icons-sharp active">light_mode</span>
                    <span class="material-icons-sharp">dark_mode</span>
                </div>
                <div class="profile">
                    <div class="info">
                        <p>Hey, <b>Client</b></p>
                        <small class="text-muted">Business Client</small>
                    </div>
                    <div class="profile-photo">
                        <img src="../assets/images/profile-1.jpg" alt="Profile Picture">
                    </div>
                </div>
            </div>
            <div class="recent-updates">
                <h2>Recent Updates</h2>
                <!-- Placeholder for recent updates -->
            </div>
        </div>
    </div>

    <script>
        const sideMenu = document.querySelector("aside");
        const menuBtn = document.querySelector("#menu-btn");
        const closeBtn = document.querySelector("#close-btn");
        const themeToggler = document.querySelector(".theme-toggler");

        // Show Sidebar
        menuBtn.addEventListener("click", () => {
            sideMenu.style.display = "block";
        });

        // Hide Sidebar
        closeBtn.addEventListener("click", () => {
            sideMenu.style.display = "none";
        });

        // Toggle Theme
        themeToggler.addEventListener("click", () => {
            document.body.classList.toggle("dark-theme-variables");

            themeToggler.querySelector("span:nth-child(1)").classList.toggle("active");
            themeToggler.querySelector("span:nth-child(2)").classList.toggle("active");
        });
    </script>
</body>
</html>

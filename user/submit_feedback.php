<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

// Require user authentication
require_auth();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $feedback_text = trim($_POST['feedback_text'] ?? '');

    if (!empty($feedback_text)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO app_feedback (user_id, feedback_text) VALUES (?, ?)");
            $stmt->execute([$user_id, $feedback_text]);
            set_flash_message('success', 'Thank you for your feedback!');
        } catch (PDOException $e) {
            set_flash_message('error', 'Failed to submit feedback.');
        }
    } else {
        set_flash_message('error', 'Feedback cannot be empty.');
    }
}

redirect($_SERVER['HTTP_REFERER'] ?? 'index.php');
?>

<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

// Require admin authentication
require_admin();

try {
    $stmt = $pdo->prepare("
        SELECT af.*, u.name as user_name, u.email
        FROM app_feedback af
        LEFT JOIN users u ON af.user_id = u.id
        ORDER BY af.created_at DESC
    ");
    $stmt->execute();
    $feedbacks = $stmt->fetchAll();
} catch (PDOException $e) {
    $errors['general'] = 'Failed to fetch feedback';
    $feedbacks = [];
}

require_once '../includes/admin_header.php';
?>

<div class="bg-background min-h-screen py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h1 class="text-3xl font-bold text-white mb-8">App Feedback</h1>

        <?php if (isset($errors['general'])): ?>
            <div class="bg-error bg-opacity-10 text-error px-4 py-3 rounded-md mb-6">
                <?php echo $errors['general']; ?>
            </div>
        <?php endif; ?>

        <?php if (empty($feedbacks)): ?>
            <div class="bg-surface rounded-lg p-8 text-center">
                <h3 class="text-xl font-bold text-white mb-2">No Feedback Found</h3>
                <p class="text-gray-400">No feedback has been submitted yet.</p>
            </div>
        <?php else: ?>
            <div class="space-y-6">
                <?php foreach ($feedbacks as $feedback): ?>
                    <div class="bg-surface rounded-lg shadow-lg p-6">
                        <div class="mb-2">
                            <span class="font-semibold text-white"><?php echo htmlspecialchars($feedback['user_name'] ?? 'Unknown User'); ?></span>
                            <span class="text-gray-400 text-sm ml-2"><?php echo htmlspecialchars($feedback['email'] ?? ''); ?></span>
                            <span class="text-gray-400 text-sm float-right"><?php echo date('Y-m-d H:i', strtotime($feedback['created_at'])); ?></span>
                        </div>
                        <p class="text-gray-300 whitespace-pre-wrap"><?php echo htmlspecialchars($feedback['feedback_text']); ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>

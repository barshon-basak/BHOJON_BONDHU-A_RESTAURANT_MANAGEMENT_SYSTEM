<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

require_admin();

// Handle restaurant status updates
if (is_post_request() && isset($_POST['action'])) {
    if ($_POST['action'] === 'update_status') {
        $restaurant_id = $_POST['restaurant_id'] ?? null;
        $new_status = $_POST['status'] ?? null;
        
        if ($restaurant_id && $new_status) {
            try {
                // Update status in restaurant_status table instead of restaurants table
                $stmt = $pdo->prepare("SELECT COUNT(*) FROM restaurant_status WHERE restaurant_id = ?");
                $stmt->execute([$restaurant_id]);
                $exists = $stmt->fetchColumn();

                if ($exists) {
                    $stmt = $pdo->prepare("UPDATE restaurant_status SET status = ? WHERE restaurant_id = ?");
                    $stmt->execute([$new_status, $restaurant_id]);
                } else {
                    $stmt = $pdo->prepare("INSERT INTO restaurant_status (restaurant_id, status) VALUES (?, ?)");
                    $stmt->execute([$restaurant_id, $new_status]);
                }

                set_flash_message('success', 'Restaurant status updated successfully');
                redirect('restaurants.php');
            } catch (PDOException $e) {
                $errors['general'] = 'Failed to update restaurant status';
            }
        }
    } elseif ($_POST['action'] === 'delete_restaurant') {
        $restaurant_id = $_POST['restaurant_id'] ?? null;
        if ($restaurant_id) {
            try {
                $stmt = $pdo->prepare("DELETE FROM restaurants WHERE id = ?");
                $stmt->execute([$restaurant_id]);
                set_flash_message('success', 'Restaurant deleted successfully');
                redirect('restaurants.php');
            } catch (PDOException $e) {
                $errors['general'] = 'Failed to delete restaurant';
            }
        }
    }
}

// Fetch all restaurants with their ratings and status from restaurant_status table
try {
    $stmt = $pdo->prepare("
        SELECT r.*,
               COALESCE(AVG(rv.rating), 0) as avg_rating,
               COUNT(rv.id) as review_count,
               rs.status as status
        FROM restaurants r
        LEFT JOIN reviews rv ON r.id = rv.restaurant_id
        LEFT JOIN restaurant_status rs ON r.id = rs.restaurant_id
        GROUP BY r.id
        ORDER BY r.name ASC
    ");
    $stmt->execute();
    $restaurants = $stmt->fetchAll();
} catch (PDOException $e) {
    $errors['general'] = 'Failed to fetch restaurants';
    $restaurants = [];
}

require_once '../includes/admin_header.php';
?>

<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-8">
        <h1 class="text-3xl font-bold text-white">Manage Restaurants</h1>
        <a href="restaurant_form.php" 
            class="bg-primary text-white px-4 py-2 rounded-md hover:bg-opacity-90">
            Add New Restaurant
        </a>
    </div>

    <?php if (isset($errors['general'])): ?>
        <div class="bg-error bg-opacity-10 text-error px-4 py-3 rounded-md mb-6">
            <?php echo $errors['general']; ?>
        </div>
    <?php endif; ?>

    <?php if (empty($restaurants)): ?>
        <div class="bg-surface rounded-lg p-8 text-center">
            <i class="fas fa-utensils text-4xl text-gray-600 mb-4"></i>
            <h3 class="text-xl font-bold text-white mb-2">No Restaurants Found</h3>
            <p class="text-gray-400">Add your first restaurant to get started.</p>
        </div>
    <?php else: ?>
        <div class="space-y-6">
            <?php foreach ($restaurants as $restaurant): ?>
                <div class="bg-surface rounded-lg shadow-lg overflow-hidden">
                    <div class="p-6">
                        <div class="flex justify-between items-start mb-4">
                            <div>
                                <h2 class="text-xl font-bold text-white">
                                    <?php echo htmlspecialchars($restaurant['name']); ?>
                                </h2>
                                <p class="text-gray-400">
                                    <?php echo htmlspecialchars($restaurant['description']); ?>
                                </p>
                            </div>
                            <div class="text-right">
                                <div class="text-yellow-400">
                                    <?php 
                                    $avg_rating = round($restaurant['avg_rating']);
                                    echo str_repeat('★', $avg_rating) . str_repeat('☆', 5 - $avg_rating);
                                    ?>
                                </div>
                                <div class="text-sm text-gray-400">
                                    <?php echo number_format($restaurant['avg_rating'], 1); ?> / 5.0
                                    (<?php echo $restaurant['review_count']; ?> reviews)
                                </div>
                            </div>
                        </div>

                        <?php if ($restaurant['review_count'] > 0): ?>
                            <?php
                                // Fetch reviews for this restaurant
                                $stmt = $pdo->prepare("
                                    SELECT r.*, u.name as user_name
                                    FROM reviews r
                                    JOIN users u ON r.user_id = u.id
                                    WHERE r.restaurant_id = ?
                                    ORDER BY r.created_at DESC
                                ");
                                $stmt->execute([$restaurant['id']]);
                                $reviews = $stmt->fetchAll();
                            ?>
                            <div class="mt-4 border-t border-gray-700 pt-4">
                                <h3 class="text-lg font-semibold text-white mb-3">Recent Reviews</h3>
                                <div class="space-y-4">
                                    <?php foreach ($reviews as $review): ?>
                                        <div class="bg-gray-800 rounded p-4">
                                            <div class="flex justify-between items-start mb-2">
                                                <div>
                                                    <div class="font-semibold text-white">
                                                        <?php echo htmlspecialchars($review['user_name']); ?>
                                                    </div>
                                                    <div class="text-sm text-gray-400">
                                                        <?php echo format_date($review['created_at']); ?>
                                                    </div>
                                                </div>
                                                <div class="text-yellow-400">
                                                    <?php echo str_repeat('★', $review['rating']); ?>
                                                </div>
                                            </div>
                                            <p class="text-gray-300">
                                                <?php echo nl2br(htmlspecialchars($review['comment'])); ?>
                                            </p>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endif; ?>

                        <div class="mt-4 flex justify-between items-center">
                            <div class="flex space-x-4">
                                <a href="restaurant_form.php?id=<?php echo $restaurant['id']; ?>" 
                                    class="text-primary hover:text-primary-dark">
                                    <i class="fas fa-edit mr-1"></i> Edit
                                </a>
                                <form method="POST" class="inline">
                                    <input type="hidden" name="action" value="update_status">
                                    <input type="hidden" name="restaurant_id" value="<?php echo $restaurant['id']; ?>">
                                    <select name="status" 
                                        class="bg-gray-800 border-gray-700 text-white rounded-md"
                                        onchange="this.form.submit()">
                                        <option value="active" <?php echo $restaurant['status'] === 'active' ? 'selected' : ''; ?>>
                                            Active
                                        </option>
                                        <option value="inactive" <?php echo $restaurant['status'] === 'inactive' ? 'selected' : ''; ?>>
                                            Inactive
                                        </option>
                                    </select>
                                </form>
                                <form method="POST" class="inline ml-4" onsubmit="return confirm('Are you sure you want to delete this restaurant?');">
                                    <input type="hidden" name="action" value="delete_restaurant">
                                    <input type="hidden" name="restaurant_id" value="<?php echo $restaurant['id']; ?>">
                                    <button type="submit" class="text-red-600 hover:text-red-800">
                                        <i class="fas fa-trash-alt mr-1"></i> Delete
                                    </button>
                                </form>
                            </div>
                            <a href="dishes.php?restaurant_id=<?php echo $restaurant['id']; ?>" 
                                class="text-primary hover:text-primary-dark">
                                <i class="fas fa-utensils mr-1"></i> Manage Dishes
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php require_once '../includes/footer.php'; ?>

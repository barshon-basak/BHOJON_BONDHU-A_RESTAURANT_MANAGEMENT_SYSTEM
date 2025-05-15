<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

// Require user authentication
require_auth();

$restaurant_id = $_GET['id'] ?? null;

// Handle review submission
if (is_post_request() && isset($_POST['submit_review'])) {
    $rating = (int)$_POST['rating'];
    $comment = trim($_POST['comment']);
    
    try {
        $stmt = $pdo->prepare("INSERT INTO reviews (restaurant_id, user_id, rating, comment) VALUES (?, ?, ?, ?)");
        $stmt->execute([$restaurant_id, $_SESSION['user_id'], $rating, $comment]);
        set_flash_message('success', 'Review submitted successfully!');
        redirect("restaurant.php?id=$restaurant_id"); // Fixed redirect URL
    } catch (PDOException $e) {
        set_flash_message('error', 'Failed to submit review');
    }
}

// Fetch restaurant details and reviews
try {
    // Get restaurant details
    $stmt = $pdo->prepare("SELECT * FROM restaurants WHERE id = ?");
    $stmt->execute([$restaurant_id]);
    $restaurant = $stmt->fetch();

    if (!$restaurant) {
        set_flash_message('error', 'Restaurant not found');
        redirect('restaurants.php');
    }

    // Get restaurant's dishes
    $stmt = $pdo->prepare("SELECT * FROM dishes WHERE restaurant_id = ? AND status = 'available'");
    $stmt->execute([$restaurant_id]);
    $dishes = $stmt->fetchAll();

    // Get reviews with user names
    $stmt = $pdo->prepare("
        SELECT r.*, u.name as user_name 
        FROM reviews r 
        JOIN users u ON r.user_id = u.id 
        WHERE r.restaurant_id = ? 
        ORDER BY r.created_at DESC
    ");
    $stmt->execute([$restaurant_id]);
    $reviews = $stmt->fetchAll();

    // Calculate average rating
    $stmt = $pdo->prepare("
        SELECT AVG(rating) as avg_rating, COUNT(*) as review_count 
        FROM reviews 
        WHERE restaurant_id = ?
    ");
    $stmt->execute([$restaurant_id]);
    $rating_info = $stmt->fetch();

} catch (PDOException $e) {
    set_flash_message('error', 'Failed to fetch restaurant details');
    redirect('restaurants.php');
}

require_once '../includes/user_header.php';
?>

<div class="container mx-auto px-4 py-8">
    <!-- Restaurant Details -->
    <div class="bg-surface rounded-lg shadow-lg p-6 mb-8">
        <h1 class="text-3xl font-bold text-white mb-4"><?php echo htmlspecialchars($restaurant['name']); ?></h1>
        <p class="text-gray-400 mb-4"><?php echo htmlspecialchars($restaurant['description']); ?></p>
        
        <?php if ($rating_info['review_count'] > 0): ?>
        <div class="text-yellow-400 mb-2">
            <?php 
            $avg_rating = round($rating_info['avg_rating']);
            echo str_repeat('★', $avg_rating) . str_repeat('☆', 5 - $avg_rating);
            ?>
            <span class="text-gray-400 text-sm">
                (<?php echo number_format($rating_info['avg_rating'], 1); ?>/5 from <?php echo $rating_info['review_count']; ?> reviews)
            </span>
        </div>
        <?php endif; ?>
    </div>

    <!-- Menu Section -->
    <div class="bg-surface rounded-lg shadow-lg p-6 mb-8">
        <h2 class="text-2xl font-bold text-white mb-4">Menu</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php foreach ($dishes as $dish): ?>
                <div class="bg-gray-800 rounded-lg p-4">
                    <?php if ($dish['image_url']): ?>
                        <img src="<?php echo htmlspecialchars($dish['image_url']); ?>" alt="<?php echo htmlspecialchars($dish['name']); ?>" class="w-full h-48 object-cover rounded-md mb-4">
                    <?php else: ?>
                        <div class="w-full h-48 bg-gray-700 rounded-md mb-4 flex items-center justify-center text-gray-400">
                            No Image
                        </div>
                    <?php endif; ?>
                    <h3 class="text-xl font-semibold text-white mb-2"><?php echo htmlspecialchars($dish['name']); ?></h3>
                    <p class="text-gray-400 mb-4"><?php echo htmlspecialchars($dish['description']); ?></p>
                    <div class="flex justify-between items-center">
                        <span class="text-white font-bold">$<?php echo number_format($dish['price'], 2); ?></span>
                        <form method="POST" action="cart.php">
                            <input type="hidden" name="action" value="add_to_cart" />
                            <input type="hidden" name="dish_id" value="<?php echo $dish['id']; ?>">
                            <button type="submit" class="bg-primary text-white px-4 py-2 rounded">
                                Add to Cart
                            </button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Write Review Section -->
    <div class="bg-surface rounded-lg shadow-lg p-6 mb-8">
        <h2 class="text-2xl font-bold text-white mb-4">Write a Review</h2>
        <form method="POST" class="space-y-4">
            <div>
                <label class="block text-gray-300 mb-2">Rating</label>
                <div class="flex space-x-4">
                    <?php for ($i = 1; $i <= 5; $i++): ?>
                    <label class="inline-flex items-center">
                        <input type="radio" name="rating" value="<?php echo $i; ?>" required class="form-radio text-primary">
                        <span class="ml-2 text-white"><?php echo $i; ?> ★</span>
                    </label>
                    <?php endfor; ?>
                </div>
            </div>
            <div>
                <label class="block text-gray-300 mb-2">Your Review</label>
                <textarea name="comment" required rows="4" class="w-full bg-gray-800 text-white rounded p-2" 
                    placeholder="Share your experience..."></textarea>
            </div>
            <button type="submit" name="submit_review" class="bg-primary text-white px-6 py-2 rounded">
                Submit Review
            </button>
        </form>
    </div>

    <!-- Reviews Section -->
    <div class="bg-surface rounded-lg shadow-lg p-6">
        <h2 class="text-2xl font-bold text-white mb-4">Customer Reviews</h2>
        <?php if (empty($reviews)): ?>
            <p class="text-gray-400">No reviews yet. Be the first to review!</p>
        <?php else: ?>
            <div class="space-y-6">
                <?php foreach ($reviews as $review): ?>
                    <div class="border-b border-gray-700 pb-6 last:border-0 last:pb-0">
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
        <?php endif; ?>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>

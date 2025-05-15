<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

// Fetch featured restaurants
try {
    $stmt = $pdo->query("
        SELECT * FROM restaurants 
        WHERE status = 'active' 
        ORDER BY RAND() 
        LIMIT 6
    ");
    $featured_restaurants = $stmt->fetchAll();
} catch (PDOException $e) {
    $featured_restaurants = [];
}

require_once 'includes/header.php';
?>

<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold text-white mb-8">Featured Restaurants</h1>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
        <?php foreach ($featured_restaurants as $restaurant): ?>
            <div class="bg-surface rounded-lg shadow-lg p-6">
                <div class="mb-4">
                    <?php if ($restaurant['image_url']): ?>
                        <img class="h-48 w-full object-cover rounded" 
                            src="<?php echo htmlspecialchars($restaurant['image_url']); ?>" 
                            alt="<?php echo htmlspecialchars($restaurant['name']); ?>">
                    <?php else: ?>
                        <div class="h-48 w-full bg-gray-800 flex items-center justify-center rounded">
                            <i class="fas fa-utensils text-4xl text-gray-600"></i>
                        </div>
                    <?php endif; ?>
                </div>
                <h3 class="text-xl font-bold text-white mb-2">
                    <?php echo htmlspecialchars($restaurant['name']); ?>
                </h3>
                <p class="text-gray-400 mb-4">
                    <?php echo htmlspecialchars($restaurant['description']); ?>
                </p>
                <a href="user/access_prompt.php" 
                    class="inline-flex items-center text-primary hover:text-primary-dark">
                    View Menu
                    <i class="fas fa-arrow-right ml-2"></i>
                </a>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>

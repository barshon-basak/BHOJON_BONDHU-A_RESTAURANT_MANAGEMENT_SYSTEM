<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

require_auth();

$user_id = $_SESSION['user_id'] ?? null;
$user_name = '';
if ($user_id) {
    $stmt = $pdo->prepare("SELECT name FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $user_name = $stmt->fetchColumn();
}

$featured_restaurants = [];
try {
$stmt = $pdo->query("SELECT id, name, image_url FROM restaurants");
$featured_restaurants = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {}

require_once '../includes/user_header.php';
?>

<div class="bg-background min-h-screen py-12">
    <!-- Tagline Section with Image Side -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-16 bg-primary rounded-lg p-12 text-white shadow-lg flex flex-col md:flex-row items-center">
        <div class="md:w-1/2 mb-8 md:mb-0 px-8">
            <h1 class="text-5xl font-extrabold mb-4 leading-tight">Delicious Food Delivered to You</h1>
            <p class="text-xl max-w-lg mb-6">Experience the best meals from your favorite restaurants, delivered fast and fresh to your doorstep.</p>
            <a href="restaurants.php" class="inline-block bg-white text-primary font-semibold py-3 px-8 rounded-lg shadow hover:bg-gray-100 transition">Browse Restaurants</a>
        </div>
        <div class="md:w-1/2 flex justify-center px-8">
            <!-- Placeholder image, replace with actual image path -->
            <img src="https://www.shutterstock.com/image-photo/buffet-table-scene-take-out-600nw-1745914352.jpg" alt="Delicious Food" class="max-w-full h-auto rounded-lg shadow-lg" />
        </div>
    </section>

    <!-- Explore Restaurants Section -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-16">
        <h2 class="text-3xl font-bold text-white mb-8 text-center">Explore Restaurants</h2>
        <?php if (!empty($featured_restaurants)): ?>
            <div class="relative">
                <button id="scroll-left" aria-label="Scroll Left" class="absolute left-0 top-1/2 transform -translate-y-1/2 bg-primary text-white rounded-full p-2 shadow-lg hover:bg-primary-dark z-10">
                    <i class="fas fa-chevron-left"></i>
                </button>
                <div id="restaurant-carousel" class="flex space-x-6 overflow-x-auto scrollbar-hide scroll-smooth px-12">
                    <?php foreach ($featured_restaurants as $restaurant): ?>
                        <a href="restaurant.php?id=<?php echo $restaurant['id']; ?>" class="min-w-[300px] rounded-lg overflow-hidden shadow-lg hover:shadow-2xl transition bg-surface flex-shrink-0">
                            <img src="<?php echo htmlspecialchars($restaurant['image_url'] ?: '/assets/restaurant-placeholder.png'); ?>" alt="<?php echo htmlspecialchars($restaurant['name']); ?>" class="w-full h-48 object-cover" />
                            <div class="p-4">
                                <h3 class="text-xl font-semibold text-white"><?php echo htmlspecialchars($restaurant['name']); ?></h3>
                            </div>
                        </a>
                    <?php endforeach; ?>
                </div>
                <button id="scroll-right" aria-label="Scroll Right" class="absolute right-0 top-1/2 transform -translate-y-1/2 bg-primary text-white rounded-full p-2 shadow-lg hover:bg-primary-dark z-10">
                    <i class="fas fa-chevron-right"></i>
                </button>
            </div>
        <?php else: ?>
            <p class="text-gray-400 text-center">No restaurants available at the moment.</p>
        <?php endif; ?>
    </section>

    <!-- Why Choose Us Section -->
    <section class="bg-surface py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="text-3xl font-bold text-white mb-10">Why Choose Us</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-8">
                <div class="flex flex-col items-center">
                    <i class="fas fa-shipping-fast text-primary text-5xl mb-4"></i>
                    <h3 class="text-xl font-semibold text-white mb-2">Fast Delivery</h3>
                    <p class="text-gray-300 max-w-xs">Get your food delivered quickly and fresh, right to your doorstep.</p>
                </div>
                <div class="flex flex-col items-center">
                    <i class="fas fa-utensils text-primary text-5xl mb-4"></i>
                    <h3 class="text-xl font-semibold text-white mb-2">Wide Variety</h3>
                    <p class="text-gray-300 max-w-xs">Choose from a large selection of restaurants and cuisines.</p>
                </div>
                <div class="flex flex-col items-center">
                    <i class="fas fa-star text-primary text-5xl mb-4"></i>
                    <h3 class="text-xl font-semibold text-white mb-2">Top Quality</h3>
                    <p class="text-gray-300 max-w-xs">We partner with the best restaurants to ensure quality meals.</p>
                </div>
                <div class="flex flex-col items-center">
                    <i class="fas fa-headset text-primary text-5xl mb-4"></i>
                    <h3 class="text-xl font-semibold text-white mb-2">24/7 Support</h3>
                    <p class="text-gray-300 max-w-xs">Our support team is always ready to help you with your orders.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- App Feedback Section -->
    <section class="bg-surface py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="text-3xl font-bold text-white mb-6">App Feedback</h2>
            <?php if (isset($_SESSION['user_id'])): ?>
            <form method="POST" action="submit_feedback.php" class="max-w-xl mx-auto space-y-4">
                <textarea name="feedback_text" rows="4" required placeholder="Your feedback about the website..." class="w-full rounded-md p-4 text-black"></textarea>
                <button type="submit" class="bg-primary text-white px-6 py-3 rounded-md hover:bg-opacity-90">Submit Feedback</button>
            </form>
            <?php else: ?>
            <p class="text-gray-400">Please <a href="login.php" class="underline">login</a> to submit feedback.</p>
            <?php endif; ?>
        </div>
    </section>
</div>

<?php require_once '../includes/footer.php'; ?>

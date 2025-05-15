<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

// Require user authentication
require_auth();

$search = $_GET['search'] ?? '';
$sort = $_GET['sort'] ?? 'asc';
$min_rating = isset($_GET['min_rating']) ? (int)$_GET['min_rating'] : 0;
$status_filter = $_GET['status'] ?? 'all';

// Validate sort parameter
$sort = strtolower($sort) === 'desc' ? 'DESC' : 'ASC';

// Fetch restaurants with filters including status from new restaurant_status table
try {
    $sql = "
        SELECT r.*,
               COALESCE(AVG(rv.rating), 0) as avg_rating,
               COUNT(rv.id) as review_count,
               rs.status as restaurant_status
        FROM restaurants r
        LEFT JOIN reviews rv ON r.id = rv.restaurant_id
        LEFT JOIN restaurant_status rs ON r.id = rs.restaurant_id
        WHERE 1=1
    ";

    $params = [];

    if (!empty($search)) {
        $sql .= " AND r.name LIKE :search ";
        $params[':search'] = '%' . $search . '%';
    }

    if ($status_filter === 'active') {
        $sql .= " AND rs.status = 'active' ";
    } elseif ($status_filter === 'inactive') {
        $sql .= " AND rs.status = 'inactive' ";
    }

    $sql .= " GROUP BY r.id ";

    if ($min_rating > 0) {
        $sql .= " HAVING avg_rating >= :min_rating ";
        $params[':min_rating'] = $min_rating;
    }

    $sql .= " ORDER BY r.name $sort ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $restaurants = $stmt->fetchAll();
} catch (PDOException $e) {
    $errors['general'] = 'Failed to fetch restaurants';
    $restaurants = [];
}

require_once '../includes/user_header.php';
?>

<form method="GET" class="mb-10 flex flex-col sm:flex-row sm:items-center sm:space-x-4 justify-center">
    <div class="relative flex-grow mb-2 sm:mb-0 max-w-md">
        <input
            type="text"
            name="search"
            value="<?php echo htmlspecialchars($search); ?>"
            placeholder="Search by restaurant name"
            class="w-full px-4 py-2 rounded-md text-black border border-gray-300 shadow-sm focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary"
        />
        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
            <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35m0 0A7.5 7.5 0 1110.5 3a7.5 7.5 0 016.15 13.65z" />
            </svg>
        </div>
    </div>
    <select name="sort" class="px-4 py-2 rounded-md text-black border border-gray-300 shadow-sm focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary">
        <option value="asc" <?php echo $sort === 'ASC' ? 'selected' : ''; ?>>Sort A-Z</option>
        <option value="desc" <?php echo $sort === 'DESC' ? 'selected' : ''; ?>>Sort Z-A</option>
    </select>
    <select name="min_rating" class="px-4 py-2 rounded-md text-black border border-gray-300 shadow-sm focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary">
        <option value="0" <?php echo $min_rating === 0 ? 'selected' : ''; ?>>All Ratings</option>
        <option value="1" <?php echo $min_rating === 1 ? 'selected' : ''; ?>>1 star & up</option>
        <option value="2" <?php echo $min_rating === 2 ? 'selected' : ''; ?>>2 stars & up</option>
        <option value="3" <?php echo $min_rating === 3 ? 'selected' : ''; ?>>3 stars & up</option>
        <option value="4" <?php echo $min_rating === 4 ? 'selected' : ''; ?>>4 stars & up</option>
        <option value="5" <?php echo $min_rating === 5 ? 'selected' : ''; ?>>5 stars only</option>
    </select>
    <select name="status" class="px-4 py-2 rounded-md text-black border border-gray-300 shadow-sm focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary">
        <option value="all" <?php echo $status_filter === 'all' ? 'selected' : ''; ?>>All Status</option>
        <option value="active" <?php echo $status_filter === 'active' ? 'selected' : ''; ?>>ON</option>
        <option value="inactive" <?php echo $status_filter === 'inactive' ? 'selected' : ''; ?>>OFF</option>
    </select>
    <button type="submit" class="ml-0 sm:ml-2 bg-primary text-white px-4 py-2 rounded-md hover:bg-opacity-90">
        Search
    </button>
</form>

<div class="bg-background min-h-screen py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h1 class="text-3xl font-bold text-white mb-8">Restaurants</h1>

        <?php if (isset($errors['general'])): ?>
            <div class="bg-error bg-opacity-10 text-error px-4 py-3 rounded-md mb-6">
                <?php echo $errors['general']; ?>
            </div>
        <?php endif; ?>

        <?php if (empty($restaurants)): ?>
            <div class="bg-surface rounded-lg p-8 text-center">
                <i class="fas fa-utensils text-4xl text-gray-600 mb-4"></i>
                <h3 class="text-xl font-bold text-white mb-2">No Restaurants Found</h3>
                <p class="text-gray-400">Check back later for new restaurants.</p>
            </div>
        <?php else: ?>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php foreach ($restaurants as $restaurant): ?>
                    <div class="bg-surface rounded-lg shadow-lg overflow-hidden">
                        <?php if ($restaurant['image_url']): ?>
                            <img src="<?php echo htmlspecialchars($restaurant['image_url']); ?>" 
                                alt="<?php echo htmlspecialchars($restaurant['name']); ?>"
                                class="w-full h-48 object-cover">
                        <?php endif; ?>
                        <div class="p-6">
                            <div class="flex justify-between items-start mb-4">
                                <h2 class="text-xl font-bold text-white">
                                    <?php echo htmlspecialchars($restaurant['name']); ?>
                                </h2>
                                <div class="text-right">
                                    <div class="text-yellow-400">
                                        <?php echo str_repeat('★', round($restaurant['avg_rating'])) . str_repeat('☆', 5 - round($restaurant['avg_rating'])); ?>
                                    </div>
                                    <div class="text-sm text-gray-400">
                                        <?php echo number_format($restaurant['avg_rating'], 1); ?> / 5.0
                                        (<?php echo $restaurant['review_count']; ?> reviews)
                                    </div>
                                </div>
                            </div>
                            <p class="text-gray-400 mb-4">
                                <?php echo htmlspecialchars($restaurant['description']); ?>
                            </p>
                            <a href="restaurant.php?id=<?php echo $restaurant['id']; ?>" 
                                class="inline-block bg-primary text-white px-4 py-2 rounded-md hover:bg-opacity-90">
                                View Menu
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>

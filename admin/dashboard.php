<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

// Require admin authentication
require_admin();

// Fetch statistics
$total_revenue = 0;
$today_revenue = 0;
try {
    // Total restaurants
    $stmt = $pdo->query("SELECT COUNT(*) FROM restaurants");
    $total_restaurants = $stmt->fetchColumn();

    // Active restaurants
    $stmt = $pdo->query("SELECT COUNT(*) FROM restaurants WHERE status = 'active'");
    $active_restaurants = $stmt->fetchColumn();

    // Total orders
    $stmt = $pdo->query("SELECT COUNT(*) FROM orders");
    $total_orders = $stmt->fetchColumn();

    // Today's orders
    $stmt = $pdo->query("SELECT COUNT(*) FROM orders WHERE DATE(created_at) = CURDATE()");
    $today_orders = $stmt->fetchColumn();

    // Total revenue
    $stmt = $pdo->query("SELECT COALESCE(SUM(total_amount), 0) FROM orders WHERE status != 'cancelled'");
    $total_revenue = $stmt->fetchColumn();

    // Today's revenue
    $stmt = $pdo->query("SELECT COALESCE(SUM(total_amount), 0) FROM orders WHERE DATE(created_at) = CURDATE() AND status != 'cancelled'");
    $today_revenue = $stmt->fetchColumn();

    // Recent orders
    $stmt = $pdo->prepare("
        SELECT o.id, o.user_id, o.total_amount, o.status, o.created_at, r.name as restaurant_name, u.name as user_name
        FROM orders o
        JOIN restaurants r ON o.restaurant_id = r.id
        JOIN users u ON o.user_id = u.id
        ORDER BY o.created_at DESC
        LIMIT 5
    ");
    $stmt->execute();
    $recent_orders = $stmt->fetchAll();

} catch (PDOException $e) {
    $errors['general'] = 'Failed to fetch statistics';
}

require_once '../includes/admin_header.php';
?>

<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold text-white mb-8">Dashboard</h1>

    <!-- Statistics Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Restaurants Stats -->
        <div class="bg-surface rounded-lg shadow-lg p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-white">Restaurants</h2>
                <i class="fas fa-store text-2xl text-primary"></i>
            </div>
            <div class="space-y-2">
                <div class="flex justify-between">
                    <span class="text-gray-400">Total</span>
                    <span class="text-white font-bold"><?php echo $total_restaurants; ?></span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-400">Active</span>
                    <span class="text-white font-bold"><?php echo $active_restaurants; ?></span>
                </div>
            </div>
        </div>

        <!-- Orders Stats -->
        <div class="bg-surface rounded-lg shadow-lg p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-white">Orders</h2>
                <i class="fas fa-shopping-bag text-2xl text-primary"></i>
            </div>
            <div class="space-y-2">
                <div class="flex justify-between">
                    <span class="text-gray-400">Total</span>
                    <span class="text-white font-bold"><?php echo $total_orders; ?></span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-400">Today</span>
                    <span class="text-white font-bold"><?php echo $today_orders; ?></span>
                </div>
            </div>
        </div>

        <!-- Revenue Stats -->
        <div class="bg-surface rounded-lg shadow-lg p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-white">Revenue</h2>
                <i class="fas fa-dollar-sign text-2xl text-primary"></i>
            </div>
            <div class="space-y-2">
                <div class="flex justify-between">
                    <span class="text-gray-400">Total</span>
                    <span class="text-white font-bold">$<?php echo number_format($total_revenue, 2); ?></span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-400">Today</span>
                    <span class="text-white font-bold">$<?php echo number_format($today_revenue, 2); ?></span>
                </div>
            </div>
        </div>

    <!-- Quick Actions -->
    <div class="bg-surface rounded-lg shadow-lg p-6">
        <h2 class="text-lg font-semibold text-white mb-4">Quick Actions</h2>
        <div class="space-y-2">
            <a href="restaurants.php" class="block text-primary hover:text-primary-dark">
                <i class="fas fa-plus mr-2"></i> Add Restaurant
            </a>
            <a href="orders.php" class="block text-primary hover:text-primary-dark">
                <i class="fas fa-list mr-2"></i> View Orders
            </a>
            <a href="feedback.php" class="block text-primary hover:text-primary-dark">
                <i class="fas fa-comments mr-2"></i> View Feedback
            </a>
        </div>
    </div>
    </div>

    <!-- Recent Orders -->
    <div class="bg-surface rounded-lg shadow-lg p-6">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-xl font-semibold text-white">Recent Orders</h2>
            <a href="orders.php" class="text-primary hover:text-primary-dark">
                View All
            </a>
        </div>

        <?php if (empty($recent_orders)): ?>
            <p class="text-gray-400">No recent orders</p>
        <?php else: ?>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="text-left">
                            <th class="pb-3 text-gray-400">Order ID</th>
                            <th class="pb-3 text-gray-400">Customer</th>
                            <th class="pb-3 text-gray-400">Restaurant</th>
                            <th class="pb-3 text-gray-400">Amount</th>
                            <th class="pb-3 text-gray-400">Status</th>
                            <th class="pb-3 text-gray-400">Date</th>
                        </tr>
                    </thead>
                    <tbody class="text-white">
                        <?php foreach ($recent_orders as $order): ?>
                            <tr>
                                <td class="py-2">#<?php echo $order['id']; ?></td>
                                <td class="py-2"><?php echo htmlspecialchars($order['user_name']); ?></td>
                                <td class="py-2"><?php echo htmlspecialchars($order['restaurant_name']); ?></td>
                                <td class="py-2">$<?php echo number_format($order['final_amount'], 2); ?></td>
                                <td class="py-2">
                                    <span class="px-2 py-1 rounded text-sm
                                        <?php echo match($order['status']) {
                                            'pending' => 'bg-yellow-200 text-yellow-800',
                                            'confirmed' => 'bg-blue-200 text-blue-800',
                                            'completed' => 'bg-green-200 text-green-800',
                                            'cancelled' => 'bg-red-200 text-red-800',
                                            default => 'bg-gray-200 text-gray-800'
                                        }; ?>">
                                        <?php echo ucfirst($order['status']); ?>
                                    </span>
                                </td>
                                <td class="py-2"><?php echo format_date($order['created_at']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>

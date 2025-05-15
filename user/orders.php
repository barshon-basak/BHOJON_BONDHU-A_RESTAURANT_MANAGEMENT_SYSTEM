<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

// Require user authentication
require_auth();

// Fetch user orders with details
try {
    $stmt = $pdo->prepare("
        SELECT o.*, 
               (SELECT GROUP_CONCAT(CONCAT(oi.quantity, 'x ', d.name) SEPARATOR ', ')
                FROM order_items oi 
                JOIN dishes d ON oi.dish_id = d.id 
                WHERE oi.order_id = o.id) as items_summary
        FROM orders o 
        WHERE o.user_id = ? 
        ORDER BY o.created_at DESC
    ");
    $stmt->execute([$_SESSION['user_id']]);
    $orders = $stmt->fetchAll();
} catch (PDOException $e) {
    $errors['general'] = 'Failed to fetch orders';
    $orders = [];
}

// Handle order cancellation
if (is_post_request() && isset($_POST['action']) && $_POST['action'] === 'cancel') {
    $order_id = $_POST['order_id'] ?? null;
    if ($order_id) {
        try {
            $stmt = $pdo->prepare("
                UPDATE orders 
                SET status = 'cancelled' 
                WHERE id = ? AND user_id = ? AND status = 'pending'
            ");
            $stmt->execute([$order_id, $_SESSION['user_id']]);
            set_flash_message('success', 'Order cancelled successfully');
            redirect('orders.php');
        } catch (PDOException $e) {
            $errors['general'] = 'Failed to cancel order';
        }
    }
}

require_once '../includes/user_header.php';
?>

<div class="bg-background min-h-screen py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h1 class="text-3xl font-bold text-white mb-8">Your Orders</h1>

        <?php if (isset($errors['general'])): ?>
            <div class="bg-error bg-opacity-10 text-error px-4 py-3 rounded-md mb-6">
                <?php echo $errors['general']; ?>
            </div>
        <?php endif; ?>

        <?php if (empty($orders)): ?>
            <div class="bg-surface rounded-lg p-8 text-center">
                <i class="fas fa-box-open text-4xl text-gray-600 mb-4"></i>
                <h3 class="text-xl font-bold text-white mb-2">No Orders Found</h3>
                <p class="text-gray-400 mb-4">You haven't placed any orders yet.</p>
                <a href="restaurants.php" class="text-primary hover:text-primary-dark">
                    Browse Restaurants
                </a>
            </div>
        <?php else: ?>
            <div class="space-y-6">
                <?php foreach ($orders as $order): ?>
                    <div class="bg-surface rounded-lg shadow-lg overflow-hidden">
                        <div class="p-6">
                            <div class="flex justify-between items-start mb-4">
                                <div>
                                    <h3 class="text-lg font-semibold text-white">
                                        Order #<?php echo $order['id']; ?>
                                    </h3>
                                    <p class="text-sm text-gray-400">
                                        Placed on <?php echo format_date($order['created_at']); ?>
                                    </p>
                                </div>
                                <span class="px-3 py-1 rounded-full text-sm font-semibold 
                                    <?php echo match($order['status']) {
                                        'pending' => 'bg-yellow-500 bg-opacity-20 text-yellow-500',
                                        'processing' => 'bg-blue-500 bg-opacity-20 text-blue-500',
                                        'completed' => 'bg-green-500 bg-opacity-20 text-green-500',
                                        'cancelled' => 'bg-red-500 bg-opacity-20 text-red-500',
                                        default => 'bg-gray-500 bg-opacity-20 text-gray-500'
                                    }; ?>">
                                    <?php echo ucfirst($order['status']); ?>
                                </span>
                            </div>

                            <div class="border-t border-gray-700 pt-4">
                                <div class="space-y-2">
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-400">Items:</span>
                                        <span class="text-white"><?php echo htmlspecialchars($order['items_summary']); ?></span>
                                    </div>
                                    <?php if (isset($order['delivery_address'])): ?>
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-400">Delivery Address:</span>
                                        <span class="text-white"><?php echo nl2br(htmlspecialchars($order['delivery_address'])); ?></span>
                                    </div>
                                    <?php endif; ?>
                                    <?php if (!empty($order['special_instructions'])): ?>
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-400">Special Instructions:</span>
                                        <span class="text-white"><?php echo nl2br(htmlspecialchars($order['special_instructions'])); ?></span>
                                    </div>
                                    <?php endif; ?>
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-400">Total Amount:</span>
                                        <span class="text-white font-semibold">$<?php echo number_format($order['total_amount'], 2); ?></span>
                                    </div>
                                </div>
                            </div>

                            <?php if ($order['status'] === 'pending'): ?>
                                <div class="mt-4 flex justify-end">
                                    <form method="POST" class="inline">
                                        <input type="hidden" name="action" value="cancel">
                                        <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                        <button type="submit" 
                                            onclick="return confirm('Are you sure you want to cancel this order?')"
                                            class="text-error hover:text-error-dark">
                                            <i class="fas fa-times mr-1"></i> Cancel Order
                                        </button>
                                    </form>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>

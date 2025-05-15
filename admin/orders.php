<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

// Require admin authentication
require_admin();

// Handle order status updates
if (is_post_request() && isset($_POST['action']) && $_POST['action'] === 'update_status') {
    $order_id = $_POST['order_id'] ?? null;
    $new_status = $_POST['status'] ?? null;
    
    if ($order_id && $new_status) {
        try {
            $pdo->beginTransaction();

            // Update order status
            $stmt = $pdo->prepare("UPDATE orders SET status = ? WHERE id = ?");
            $stmt->execute([$new_status, $order_id]);

            // If order is completed, add to income
            if ($new_status === 'completed') {
                // Check if this order is already in income table
                $stmt = $pdo->prepare("SELECT COUNT(*) FROM income WHERE order_id = ?");
                $stmt->execute([$order_id]);
                $exists = $stmt->fetchColumn();

                if (!$exists) {
                    // Get order amount
                    $stmt = $pdo->prepare("SELECT total_amount FROM orders WHERE id = ?");
                    $stmt->execute([$order_id]);
                    $amount = $stmt->fetchColumn();

                    // Add to income
                    $stmt = $pdo->prepare("
                        INSERT INTO income (order_id, amount, created_at)
                        VALUES (?, ?, NOW())
                    ");
                    $stmt->execute([$order_id, $amount]);
                }
            }

            $pdo->commit();
            set_flash_message('success', 'Order status updated successfully');
            redirect('orders.php');
        } catch (PDOException $e) {
            $pdo->rollBack();
            $errors['general'] = 'Failed to update order status';
        }
    }
}

try {
    $stmt = $pdo->prepare("
        SELECT o.*, u.name as user_name, u.phone,
               (SELECT GROUP_CONCAT(CONCAT(oi.quantity, 'x ', d.name) SEPARATOR ', ')
                FROM order_items oi 
                JOIN dishes d ON oi.dish_id = d.id 
                WHERE oi.order_id = o.id) as items_summary,
               (SELECT extra_info FROM order_extra_info eoi WHERE eoi.order_id = o.id LIMIT 1) as extra_info,
               (SELECT address FROM delivery_address da WHERE da.order_id = o.id LIMIT 1) as delivery_address
        FROM orders o 
        LEFT JOIN users u ON o.user_id = u.id
        ORDER BY o.created_at DESC
    ");
    $stmt->execute();
    $orders = $stmt->fetchAll();
} catch (PDOException $e) {
    $errors['general'] = 'Failed to fetch orders';
    $orders = [];
}

require_once '../includes/admin_header.php';
?>

<div class="bg-background min-h-screen py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h1 class="text-3xl font-bold text-white mb-8">Manage Orders</h1>

        <?php if (isset($errors['general'])): ?>
            <div class="bg-error bg-opacity-10 text-error px-4 py-3 rounded-md mb-6">
                <?php echo $errors['general']; ?>
            </div>
        <?php endif; ?>

        <?php if (empty($orders)): ?>
            <div class="bg-surface rounded-lg p-8 text-center">
                <i class="fas fa-box-open text-4xl text-gray-600 mb-4"></i>
                <h3 class="text-xl font-bold text-white mb-2">No Orders Found</h3>
                <p class="text-gray-400">There are no orders in the system yet.</p>
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
                                <form method="POST" class="flex items-center space-x-2">
                                    <input type="hidden" name="action" value="update_status">
                                    <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                    <select name="status" 
                                        class="rounded-md bg-gray-800 border-gray-700 text-white text-sm"
                                        onchange="this.form.submit()">
                                        <?php 
                                        $statuses = ['pending', 'processing', 'completed', 'cancelled'];
                                        foreach ($statuses as $status): 
                                        ?>
                                            <option value="<?php echo $status; ?>" 
                                                <?php echo $order['status'] === $status ? 'selected' : ''; ?>>
                                                <?php echo ucfirst($status); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </form>
                            </div>

                            <div class="border-t border-gray-700 pt-4">
                                <div class="space-y-2">
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-400">Customer:</span>
                                        <span class="text-white">
                                            <?php echo htmlspecialchars($order['user_name']); ?> 
                                            (<?php echo htmlspecialchars($order['phone']); ?>)
                                        </span>
                                    </div>
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-400">Items:</span>
                                        <span class="text-white"><?php echo htmlspecialchars($order['items_summary']); ?></span>
                                    </div>
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-400">Delivery Address:</span>
                                        <span class="text-white"><?php echo nl2br(htmlspecialchars($order['delivery_address'] ?? 'N/A')); ?></span>
                                    </div>
                                    <?php if (!empty($order['extra_info'])): ?>
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-400">Additional Info:</span>
                                        <span class="text-white"><?php echo nl2br(htmlspecialchars($order['extra_info'])); ?></span>
                                    </div>
                                    <?php endif; ?>
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-400">Total Amount:</span>
                                        <span class="text-white font-semibold">$<?php echo number_format($order['total_amount'], 2); ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>

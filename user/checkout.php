<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

// Require user authentication
require_auth();

// Fetch cart items from database for logged-in user
$user_id = $_SESSION['user_id'];
try {
    $stmt = $pdo->prepare("
        SELECT c.id as cart_id, d.id, d.name, d.price, c.quantity
        FROM cart c
        JOIN dishes d ON c.dish_id = d.id
        WHERE c.user_id = ?
    ");
    $stmt->execute([$user_id]);
    $cart_items = $stmt->fetchAll();

    // Calculate order total
    $cart_total = 0;
    foreach ($cart_items as $item) {
        $cart_total += $item['price'] * $item['quantity'];
    }
} catch (PDOException $e) {
    set_flash_message('error', 'Failed to fetch cart items');
    redirect('cart.php');
}

// Handle order submission
if (is_post_request() && isset($_POST['place_order'])) {
    $payment_method = $_POST['payment_method'] ?? 'COD';
    try {
        $pdo->beginTransaction();

        // Create order
        $stmt = $pdo->prepare("
            INSERT INTO orders (user_id, total_amount, status)
            VALUES (?, ?, 'pending')
        ");
        $stmt->execute([
            $user_id,
            $cart_total
        ]);
        $order_id = $pdo->lastInsertId();

        // Add payment method in separate table
        $stmt = $pdo->prepare("
            INSERT INTO payment_methods (order_id, method)
            VALUES (?, ?)
        ");
        $stmt->execute([$order_id, $payment_method]);

        // Add order items
        foreach ($cart_items as $item) {
            $stmt = $pdo->prepare("
                INSERT INTO order_items (order_id, dish_id, quantity, price)
                VALUES (?, ?, ?, ?)
            ");
            $stmt->execute([$order_id, $item['id'], $item['quantity'], $item['price']]);
        }

        // Save extra info if provided
        $extra_info = trim($_POST['extra_info'] ?? '');
        $delivery_address = trim($_POST['delivery_address'] ?? '');

        if (!empty($extra_info)) {
            $stmt = $pdo->prepare("INSERT INTO order_extra_info (order_id, user_id, extra_info) VALUES (?, ?, ?)");
            $stmt->execute([$order_id, $user_id, $extra_info]);
        }

        if (!empty($delivery_address)) {
            $stmt = $pdo->prepare("INSERT INTO delivery_address (order_id, address) VALUES (?, ?)");
            $stmt->execute([$order_id, $delivery_address]);
        }

        $pdo->commit();

        // Clear cart items from database
        $stmt = $pdo->prepare("DELETE FROM cart WHERE user_id = ?");
        $stmt->execute([$user_id]);

        set_flash_message('success', 'Order placed successfully!');
        redirect('orders.php');

    } catch (PDOException $e) {
        $pdo->rollBack();
        // For debugging: display error message directly
        die('Order placement error: ' . htmlspecialchars($e->getMessage()));
        // error_log('Order placement error: ' . $e->getMessage());
        // set_flash_message('error', 'Failed to place order: ' . $e->getMessage());
    }
}

require_once '../includes/user_header.php';
?>

<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold text-white mb-8">Checkout</h1>

    <!-- Order Summary -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        <div>
            <div class="bg-surface rounded-lg shadow-lg p-6 mb-6">
                <h2 class="text-xl font-bold text-white mb-4">Order Summary</h2>
                <div class="space-y-4">
                <?php foreach ($cart_items as $item): ?>
                    <div class="flex justify-between items-center">
                        <div>
                            <h3 class="text-white"><?php echo htmlspecialchars($item['name']); ?></h3>
                            <p class="text-sm text-gray-400">Quantity: <?php echo $item['quantity']; ?></p>
                        </div>
                        <span class="text-white">$<?php echo number_format($item['price'] * $item['quantity'], 2); ?></span>
                    </div>
                <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- Order Total -->
        <div class="bg-surface rounded-lg shadow-lg p-6 h-fit">
            <h2 class="text-xl font-bold text-white mb-4">Order Total</h2>
            <div class="space-y-4">
                <div class="flex justify-between text-lg font-bold">
                    <span class="text-white">Total</span>
                    <span class="text-white">$<?php echo number_format($cart_total, 2); ?></span>
                </div>
<form method="POST" class="mt-6">
    <label for="payment_method" class="block text-white mb-2 font-semibold">Payment Method</label>
    <select id="payment_method" name="payment_method" required
        class="w-full mb-4 px-4 py-2 rounded-md text-black border border-gray-300 shadow-sm focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary">
        <option value="COD" <?php echo (isset($_POST['payment_method']) && $_POST['payment_method'] === 'COD') ? 'selected' : ''; ?>>Cash on Delivery (COD)</option>
        <option value="BKASH" <?php echo (isset($_POST['payment_method']) && $_POST['payment_method'] === 'BKASH') ? 'selected' : ''; ?>>BKASH</option>
        <option value="CARD" <?php echo (isset($_POST['payment_method']) && $_POST['payment_method'] === 'CARD') ? 'selected' : ''; ?>>Card</option>
    </select>
    <div class="mb-4">
        <label for="delivery_address" class="block text-white mb-2 font-semibold">Delivery Address</label>
        <textarea id="delivery_address" name="delivery_address" rows="3" required class="w-full px-4 py-2 rounded-md text-black border border-gray-300 shadow-sm focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary" placeholder="Enter your delivery address"></textarea>
    </div>
    <div class="mb-4">
        <label for="extra_info" class="block text-white mb-2 font-semibold">Additional Information</label>
        <textarea id="extra_info" name="extra_info" rows="3" class="w-full px-4 py-2 rounded-md text-black border border-gray-300 shadow-sm focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary" placeholder="Enter any extra information or instructions"></textarea>
    </div>
    <button type="submit" name="place_order"
        class="w-full bg-primary text-white px-6 py-3 rounded-md hover:bg-opacity-90">
        Place Order
    </button>
</form>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>

<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

// Require user authentication
require_auth();

    // Fetch cart items for the logged-in user
    try {
        $stmt = $pdo->prepare("
            SELECT c.*, d.name as dish_name, d.price as dish_price,
                   r.name as restaurant_name
            FROM cart c 
            LEFT JOIN dishes d ON c.dish_id = d.id 
            LEFT JOIN restaurants r ON d.restaurant_id = r.id
            WHERE c.user_id = ? AND c.dish_id IS NOT NULL
        ");
        $stmt->execute([$_SESSION['user_id']]);
        $cart_items = $stmt->fetchAll();

    // Calculate total amount
    $total_amount = 0;
    foreach ($cart_items as $item) {
        $total_amount += $item['dish_price'] * $item['quantity'];
    }
} catch (PDOException $e) {
    $errors['general'] = 'Failed to fetch cart items';
    $cart_items = [];
}

// Handle cart updates and adding items
if (is_post_request()) {
    if (isset($_POST['action'])) {
        $action = $_POST['action'];
        $cart_id = $_POST['cart_id'] ?? null;

        switch ($action) {
            case 'add_to_cart':
                $dish_id = $_POST['dish_id'] ?? null;
                if ($dish_id) {
                    try {
                        // Check if dish already in cart
                        $stmt = $pdo->prepare("SELECT id, quantity FROM cart WHERE user_id = ? AND dish_id = ?");
                        $stmt->execute([$_SESSION['user_id'], $dish_id]);
                        $existing = $stmt->fetch();

                        if ($existing) {
                            // Update quantity
                            $new_quantity = $existing['quantity'] + 1;
                            $stmt = $pdo->prepare("UPDATE cart SET quantity = ? WHERE id = ? AND user_id = ?");
                            $stmt->execute([$new_quantity, $existing['id'], $_SESSION['user_id']]);
                        } else {
                            // Insert new cart item
                            $stmt = $pdo->prepare("INSERT INTO cart (user_id, dish_id, quantity) VALUES (?, ?, ?)");
                            $stmt->execute([$_SESSION['user_id'], $dish_id, 1]);
                        }
                        set_flash_message('success', 'Dish added to cart');
                        // Redirect back to referring page or cart page
                        $redirect_url = $_SERVER['HTTP_REFERER'] ?? '/user/cart.php';
                        redirect($redirect_url);
                    } catch (PDOException $e) {
                        $errors['general'] = 'Failed to add dish to cart';
                    }
                }
                break;

            case 'update':
                $quantity = (int)($_POST['quantity'] ?? 1);
                if ($cart_id && $quantity > 0) {
                    try {
                        $stmt = $pdo->prepare("UPDATE cart SET quantity = ? WHERE id = ? AND user_id = ?");
                        $stmt->execute([$quantity, $cart_id, $_SESSION['user_id']]);
                        set_flash_message('success', 'Cart updated successfully');
                        redirect('cart.php');
                    } catch (PDOException $e) {
                        $errors['general'] = 'Failed to update cart';
                    }
                }
                break;

            case 'remove':
                if ($cart_id) {
                    try {
                        $stmt = $pdo->prepare("DELETE FROM cart WHERE id = ? AND user_id = ?");
                        $stmt->execute([$cart_id, $_SESSION['user_id']]);
                        set_flash_message('success', 'Item removed from cart');
                        redirect('cart.php');
                    } catch (PDOException $e) {
                        $errors['general'] = 'Failed to remove item from cart';
                    }
                }
                break;
        }
    }
}

require_once '../includes/user_header.php';
?>

<div class="bg-background min-h-screen py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h1 class="text-3xl font-bold text-white mb-8">Your Cart</h1>

        <?php if (isset($errors['general'])): ?>
            <div class="bg-error bg-opacity-10 text-error px-4 py-3 rounded-md mb-6">
                <?php echo $errors['general']; ?>
            </div>
        <?php endif; ?>

        <?php if (empty($cart_items)): ?>
            <div class="bg-surface rounded-lg p-8 text-center">
                <i class="fas fa-shopping-cart text-4xl text-gray-600 mb-4"></i>
                <h3 class="text-xl font-bold text-white mb-2">Your Cart is Empty</h3>
                <p class="text-gray-400 mb-4">Browse restaurants to add dishes to your cart.</p>
                <a href="restaurants.php" class="text-primary hover:text-primary-dark">
                    Browse Restaurants
                </a>
            </div>
        <?php else: ?>
            <div class="bg-surface rounded-lg shadow-lg overflow-hidden mb-6">
                <table class="w-full">
                    <thead>
                        <tr class="bg-surface border-b border-gray-700">
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Dish</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Restaurant</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Price</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Quantity</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Total</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-700">
                        <?php foreach ($cart_items as $item): ?>
                            <tr>
                                <td class="px-6 py-4">
                                    <div class="text-sm font-medium text-white">
                                        <?php echo htmlspecialchars($item['dish_name']); ?>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-300">
                                    <?php echo htmlspecialchars($item['restaurant_name']); ?>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-300">
                                    $<?php echo number_format($item['dish_price'], 2); ?>
                                </td>
                                <td class="px-6 py-4">
                                    <form method="POST" class="inline-flex items-center">
                                        <input type="hidden" name="action" value="update">
                                        <input type="hidden" name="cart_id" value="<?php echo $item['id']; ?>">
                                        <select name="quantity" onchange="this.form.submit()"
                                            class="rounded-md bg-gray-800 border-gray-700 text-white">
                                            <?php for ($i = 1; $i <= 10; $i++): ?>
                                                <option value="<?php echo $i; ?>" <?php echo $item['quantity'] == $i ? 'selected' : ''; ?>>
                                                    <?php echo $i; ?>
                                                </option>
                                            <?php endfor; ?>
                                        </select>
                                    </form>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-300">
                                    $<?php echo number_format($item['dish_price'] * $item['quantity'], 2); ?>
                                </td>
                                <td class="px-6 py-4">
                                    <form method="POST" class="inline">
                                        <input type="hidden" name="action" value="remove">
                                        <input type="hidden" name="cart_id" value="<?php echo $item['id']; ?>">
                                        <button type="submit" class="text-error hover:text-error-dark">
                                            <i class="fas fa-trash"></i> Remove
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div class="bg-surface rounded-lg shadow-lg p-4 mb-6">
                <div class="flex justify-between items-center">
                    <div class="text-white">
                        <span class="text-lg font-semibold">Total Amount:</span>
                        <span class="text-2xl font-bold ml-2">$<?php echo number_format($total_amount, 2); ?></span>
                    </div>
                    <div class="flex items-center space-x-4">
                        <a href="restaurants.php" class="text-primary hover:text-primary-dark">
                            <i class="fas fa-arrow-left mr-2"></i> Continue Shopping
                        </a>
                        <a href="checkout.php" 
                            class="bg-primary text-white px-6 py-3 rounded-md hover:bg-opacity-90 inline-flex items-center">
                            <i class="fas fa-shopping-cart mr-2"></i> Proceed to Checkout
                        </a>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>

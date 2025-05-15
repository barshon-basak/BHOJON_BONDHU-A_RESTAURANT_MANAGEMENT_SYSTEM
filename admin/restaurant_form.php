<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

// Require admin authentication
require_admin();

$restaurant_id = $_GET['id'] ?? null;
$is_edit = isset($restaurant_id);

// Initialize variables
$restaurant = [
    'name' => '',
    'description' => '',
    'status' => 'active',
    'image_url' => ''
];

// If editing, fetch restaurant details
if ($is_edit) {
    try {
        $stmt = $pdo->prepare("SELECT r.*, rs.status FROM restaurants r LEFT JOIN restaurant_status rs ON r.id = rs.restaurant_id WHERE r.id = ?");
        $stmt->execute([$restaurant_id]);
        $restaurant = $stmt->fetch();

        if (!$restaurant) {
            set_flash_message('error', 'Restaurant not found');
            redirect('restaurants.php');
        }
    } catch (PDOException $e) {
        set_flash_message('error', 'Failed to fetch restaurant details');
        redirect('restaurants.php');
    }
}

// Handle form submission
if (is_post_request()) {
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $status = $_POST['status'];
    $image_url = trim($_POST['image_url']);
    $errors = [];

    // Validate input
    if (empty($name)) {
        $errors['name'] = 'Restaurant name is required';
    }
    if (empty($description)) {
        $errors['description'] = 'Description is required';
    }

    if (empty($errors)) {
        try {
            if ($is_edit) {
                // Update existing restaurant (without status column)
                $stmt = $pdo->prepare("
                    UPDATE restaurants 
                    SET name = ?, description = ?, image_url = ?
                    WHERE id = ?
                ");
                $stmt->execute([$name, $description, $image_url, $restaurant_id]);

                // Update or insert status in restaurant_status table
                $stmt = $pdo->prepare("SELECT COUNT(*) FROM restaurant_status WHERE restaurant_id = ?");
                $stmt->execute([$restaurant_id]);
                $exists = $stmt->fetchColumn();

                if ($exists) {
                    $stmt = $pdo->prepare("UPDATE restaurant_status SET status = ? WHERE restaurant_id = ?");
                    $stmt->execute([$status, $restaurant_id]);
                } else {
                    $stmt = $pdo->prepare("INSERT INTO restaurant_status (restaurant_id, status) VALUES (?, ?)");
                    $stmt->execute([$restaurant_id, $status]);
                }

                set_flash_message('success', 'Restaurant updated successfully');
            } else {
                // Add new restaurant (without status column)
                $stmt = $pdo->prepare("
                    INSERT INTO restaurants (name, description, image_url)
                    VALUES (?, ?, ?)
                ");
                $stmt->execute([$name, $description, $image_url]);
                $new_restaurant_id = $pdo->lastInsertId();

                // Insert status in restaurant_status table
                $stmt = $pdo->prepare("INSERT INTO restaurant_status (restaurant_id, status) VALUES (?, ?)");
                $stmt->execute([$new_restaurant_id, $status]);

                set_flash_message('success', 'Restaurant added successfully');
            }
            redirect('restaurants.php');
        } catch (PDOException $e) {
            $errors['general'] = $is_edit ? 'Failed to update restaurant' : 'Failed to add restaurant';
        }
    }

            // If there were errors, update the restaurant array with the submitted values
            if (!empty($errors)) {
                $restaurant = [
                    'name' => $name,
                    'description' => $description,
                    'status' => $status,
                    'image_url' => $image_url
                ];
            }
}

require_once '../includes/admin_header.php';
?>

<div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto">
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-3xl font-bold text-white">
                <?php echo $is_edit ? 'Edit Restaurant' : 'Add New Restaurant'; ?>
            </h1>
            <a href="restaurants.php" class="text-gray-400 hover:text-white">
                <i class="fas fa-arrow-left mr-2"></i> Back to Restaurants
            </a>
        </div>

        <?php if (isset($errors['general'])): ?>
            <div class="bg-error bg-opacity-10 text-error px-4 py-3 rounded-md mb-6">
                <?php echo $errors['general']; ?>
            </div>
        <?php endif; ?>

        <form method="POST" class="bg-surface rounded-lg shadow-lg p-6">
            <div class="space-y-6">
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">Restaurant Name</label>
                    <input type="text" name="name" value="<?php echo htmlspecialchars($restaurant['name']); ?>"
                        class="w-full bg-gray-800 border-gray-700 rounded-md text-white"
                        required>
                    <?php if (isset($errors['name'])): ?>
                        <p class="mt-1 text-sm text-error"><?php echo $errors['name']; ?></p>
                    <?php endif; ?>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">Description</label>
                    <textarea name="description" rows="4" 
                        class="w-full bg-gray-800 border-gray-700 rounded-md text-white"
                        required><?php echo htmlspecialchars($restaurant['description']); ?></textarea>
                    <?php if (isset($errors['description'])): ?>
                        <p class="mt-1 text-sm text-error"><?php echo $errors['description']; ?></p>
                    <?php endif; ?>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">Image URL</label>
                    <input type="url" name="image_url" value="<?php echo htmlspecialchars($restaurant['image_url']); ?>"
                        class="w-full bg-gray-800 border-gray-700 rounded-md text-white"
                        placeholder="https://example.com/image.jpg">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">Status</label>
                    <select name="status" class="w-full bg-gray-800 border-gray-700 rounded-md text-white">
                        <option value="active" <?php echo $restaurant['status'] === 'active' ? 'selected' : ''; ?>>
                            Active
                        </option>
                        <option value="inactive" <?php echo $restaurant['status'] === 'inactive' ? 'selected' : ''; ?>>
                            Inactive
                        </option>
                    </select>
                </div>

                <div class="flex justify-end">
                    <button type="submit" class="bg-primary text-white px-6 py-2 rounded-md hover:bg-opacity-90">
                        <?php echo $is_edit ? 'Update Restaurant' : 'Add Restaurant'; ?>
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>

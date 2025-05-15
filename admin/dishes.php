<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

// Require admin authentication
require_admin();

$restaurant_id = $_GET['restaurant_id'] ?? null;

// Fetch restaurant details
try {
    $stmt = $pdo->prepare("SELECT * FROM restaurants WHERE id = ?");
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

// Handle form submission
if (is_post_request()) {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'add':
                $name = trim($_POST['name']);
                $description = trim($_POST['description']);
                $price = (float)$_POST['price'];
                $status = $_POST['status'];

                // Handle image URL only, remove upload image option
                $image_url = null;
                if (!empty(trim($_POST['image_url']))) {
                    $image_url = trim($_POST['image_url']);
                }

                try {
                    $stmt = $pdo->prepare("
                        INSERT INTO dishes (restaurant_id, name, description, price, image_url, status)
                        VALUES (?, ?, ?, ?, ?, ?)
                    ");
                    $stmt->execute([$restaurant_id, $name, $description, $price, $image_url, $status]);
                    set_flash_message('success', 'Dish added successfully');
                } catch (PDOException $e) {
                    set_flash_message('error', 'Failed to add dish');
                }
                break;

            case 'update':
                $dish_id = $_POST['dish_id'];
                $name = trim($_POST['name']);
                $description = trim($_POST['description']);
                $price = (float)$_POST['price'];
                $status = $_POST['status'];

                // Handle image URL or upload
                $image_url = null;
                if (!empty(trim($_POST['image_url']))) {
                    $image_url = trim($_POST['image_url']);
                } elseif (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                    $upload_dir = __DIR__ . '/../uploads/dishes/';
                    if (!is_dir($upload_dir)) {
                        mkdir($upload_dir, 0755, true);
                    }
                    $tmp_name = $_FILES['image']['tmp_name'];
                    $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
                    $filename = uniqid('dish_', true) . '.' . $ext;
                    $destination = $upload_dir . $filename;
                    if (move_uploaded_file($tmp_name, $destination)) {
                        $image_url = '/uploads/dishes/' . $filename;
                    }
                }

                try {
                    if ($image_url) {
                        $stmt = $pdo->prepare("
                            UPDATE dishes 
                            SET name = ?, description = ?, price = ?, status = ?, image_url = ?
                            WHERE id = ? AND restaurant_id = ?
                        ");
                        $stmt->execute([$name, $description, $price, $status, $image_url, $dish_id, $restaurant_id]);
                    } else {
                        $stmt = $pdo->prepare("
                            UPDATE dishes 
                            SET name = ?, description = ?, price = ?, status = ?
                            WHERE id = ? AND restaurant_id = ?
                        ");
                        $stmt->execute([$name, $description, $price, $status, $dish_id, $restaurant_id]);
                    }
                    set_flash_message('success', 'Dish updated successfully');
                } catch (PDOException $e) {
                    set_flash_message('error', 'Failed to update dish');
                }
                break;

            case 'delete':
                $dish_id = $_POST['dish_id'];
                try {
                    $stmt = $pdo->prepare("DELETE FROM dishes WHERE id = ? AND restaurant_id = ?");
                    $stmt->execute([$dish_id, $restaurant_id]);
                    set_flash_message('success', 'Dish deleted successfully');
                } catch (PDOException $e) {
                    set_flash_message('error', 'Failed to delete dish');
                }
                break;
        }
        redirect("dishes.php?restaurant_id=$restaurant_id");
    }
}

// Fetch dishes
try {
    $stmt = $pdo->prepare("SELECT * FROM dishes WHERE restaurant_id = ? ORDER BY name");
    $stmt->execute([$restaurant_id]);
    $dishes = $stmt->fetchAll();
} catch (PDOException $e) {
    $errors['general'] = 'Failed to fetch dishes';
    $dishes = [];
}

require_once '../includes/admin_header.php';
?>

<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-white">Manage Dishes</h1>
            <p class="text-gray-400">Restaurant: <?php echo htmlspecialchars($restaurant['name']); ?></p>
        </div>
        <button onclick="document.getElementById('addDishModal').classList.remove('hidden')"
            class="bg-primary text-white px-4 py-2 rounded-md hover:bg-opacity-90">
            Add New Dish
        </button>
    </div>

    <?php if (isset($errors['general'])): ?>
        <div class="bg-error bg-opacity-10 text-error px-4 py-3 rounded-md mb-6">
            <?php echo $errors['general']; ?>
        </div>
    <?php endif; ?>

    <?php if (empty($dishes)): ?>
        <div class="bg-surface rounded-lg p-8 text-center">
            <i class="fas fa-utensils text-4xl text-gray-600 mb-4"></i>
            <h3 class="text-xl font-bold text-white mb-2">No Dishes Found</h3>
            <p class="text-gray-400">Add your first dish to get started.</p>
        </div>
    <?php else: ?>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php foreach ($dishes as $dish): ?>
                <div class="bg-surface rounded-lg shadow-lg p-6">
                    <div class="mb-4">
                        <?php if ($dish['image_url']): ?>
                            <img src="<?php echo htmlspecialchars($dish['image_url']); ?>" alt="<?php echo htmlspecialchars($dish['name']); ?>" class="w-full h-48 object-cover rounded-md mb-4">
                        <?php else: ?>
                            <div class="w-full h-48 bg-gray-700 rounded-md mb-4 flex items-center justify-center text-gray-400">
                                No Image
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <h3 class="text-xl font-bold text-white">
                                <?php echo htmlspecialchars($dish['name']); ?>
                            </h3>
                            <p class="text-gray-400">
                                <?php echo htmlspecialchars($dish['description']); ?>
                            </p>
                        </div>
                        <span class="text-white font-bold">
                            $<?php echo number_format($dish['price'], 2); ?>
                        </span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="px-2 py-1 rounded text-sm <?php 
                            echo $dish['status'] === 'available' 
                                ? 'bg-green-900 bg-opacity-20 text-green-400'
                                : 'bg-red-900 bg-opacity-20 text-red-400'
                            ?>">
                            <?php echo ucfirst($dish['status']); ?>
                        </span>
                        <div class="flex space-x-2">
                            <button onclick="editDish(<?php echo htmlspecialchars(json_encode($dish)); ?>)"
                                class="text-primary hover:text-primary-dark">
                                <i class="fas fa-edit"></i>
                            </button>
                            <form method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this dish?')">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="dish_id" value="<?php echo $dish['id']; ?>">
                                <button type="submit" class="text-error hover:text-opacity-80">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<!-- Add Dish Modal -->
<div id="addDishModal" class="fixed inset-0 bg-black bg-opacity-50 hidden">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-surface rounded-lg shadow-lg max-w-md w-full p-6">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold text-white">Add New Dish</h2>
                <button onclick="document.getElementById('addDishModal').classList.add('hidden')"
                    class="text-gray-400 hover:text-white">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="action" value="add">
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Name</label>
                        <input type="text" name="name" required
                            class="w-full bg-gray-800 border-gray-700 rounded-md text-white">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Description</label>
                        <textarea name="description" required rows="3"
                            class="w-full bg-gray-800 border-gray-700 rounded-md text-white"></textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Price</label>
                        <input type="number" name="price" required min="0" step="0.01"
                            class="w-full bg-gray-800 border-gray-700 rounded-md text-white">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Status</label>
                        <select name="status" required class="w-full bg-gray-800 border-gray-700 rounded-md text-white">
                            <option value="available">Available</option>
                            <option value="unavailable">Unavailable</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Image URL</label>
                        <input type="url" name="image_url" placeholder="https://example.com/image.jpg" class="w-full bg-gray-800 border-gray-700 rounded-md text-white">
                    </div>
                    
                    <div class="flex justify-end mt-6">
                        <button type="button" 
                            onclick="document.getElementById('addDishModal').classList.add('hidden')"
                            class="bg-gray-600 text-white px-4 py-2 rounded-md mr-2">
                            Cancel
                        </button>
                        <button type="submit" class="bg-primary text-white px-4 py-2 rounded-md">
                            Add Dish
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Dish Modal -->
<div id="editDishModal" class="fixed inset-0 bg-black bg-opacity-50 hidden">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-surface rounded-lg shadow-lg max-w-md w-full p-6">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold text-white">Edit Dish</h2>
                <button onclick="document.getElementById('editDishModal').classList.add('hidden')"
                    class="text-gray-400 hover:text-white">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form method="POST" id="editDishForm" enctype="multipart/form-data">
                <input type="hidden" name="action" value="update">
                <input type="hidden" name="dish_id" id="editDishId">
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Name</label>
                        <input type="text" name="name" id="editDishName" required
                            class="w-full bg-gray-800 border-gray-700 rounded-md text-white">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Description</label>
                        <textarea name="description" id="editDishDescription" required rows="3"
                            class="w-full bg-gray-800 border-gray-700 rounded-md text-white"></textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Price</label>
                        <input type="number" name="price" id="editDishPrice" required min="0" step="0.01"
                            class="w-full bg-gray-800 border-gray-700 rounded-md text-white">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Status</label>
                        <select name="status" id="editDishStatus" required 
                            class="w-full bg-gray-800 border-gray-700 rounded-md text-white">
                            <option value="available">Available</option>
                            <option value="unavailable">Unavailable</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Image URL</label>
                        <input type="url" name="image_url" id="editDishImageUrl" placeholder="https://example.com/image.jpg" class="w-full bg-gray-800 border-gray-700 rounded-md text-white">
                    </div>
                    
                    <div class="flex justify-end mt-6">
                        <button type="button" 
                            onclick="document.getElementById('editDishModal').classList.add('hidden')"
                            class="bg-gray-600 text-white px-4 py-2 rounded-md mr-2">
                            Cancel
                        </button>
                        <button type="submit" class="bg-primary text-white px-4 py-2 rounded-md">
                            Update Dish
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function editDish(dish) {
    document.getElementById('editDishId').value = dish.id;
    document.getElementById('editDishName').value = dish.name;
    document.getElementById('editDishDescription').value = dish.description;
    document.getElementById('editDishPrice').value = dish.price;
    document.getElementById('editDishStatus').value = dish.status;
    document.getElementById('editDishImageUrl').value = dish.image_url || '';
    document.getElementById('editDishModal').classList.remove('hidden');
}
</script>

<?php require_once '../includes/footer.php'; ?>

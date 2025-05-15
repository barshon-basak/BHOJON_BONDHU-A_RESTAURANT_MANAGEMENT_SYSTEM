<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

// Require user authentication
require_auth();

// Fetch user details
try {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch();
} catch (PDOException $e) {
    set_flash_message('error', 'Failed to fetch user details');
    redirect('logout.php');
}

$errors = [];

// Handle profile update
if (is_post_request()) {
    $name = sanitize_input($_POST['name'] ?? '');
    $email = sanitize_input($_POST['email'] ?? '');
    $phone = sanitize_input($_POST['phone'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

// Handle photo URL input
$photo_url = sanitize_input($_POST['photo_url'] ?? '');
if (!empty($photo_url)) {
    try {
        $stmt = $pdo->prepare("INSERT INTO user_photos (user_id, photo_path) VALUES (?, ?)");
        $stmt->execute([$_SESSION['user_id'], $photo_url]);
    } catch (PDOException $e) {
        $errors['photo'] = 'Failed to save photo URL';
    }
}

    // Validate inputs
    if (empty($name)) {
        $errors['name'] = 'Name is required';
    }

    if (empty($email)) {
        $errors['email'] = 'Email is required';
    } elseif (!is_valid_email($email)) {
        $errors['email'] = 'Invalid email format';
    }

    if (empty($phone)) {
        $errors['phone'] = 'Phone number is required';
    } elseif (!is_valid_phone($phone)) {
        $errors['phone'] = 'Invalid phone number format';
    }

    if (!empty($password) && $password !== $confirm_password) {
        $errors['confirm_password'] = 'Passwords do not match';
    }

    // If no errors, update user details
    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("UPDATE users SET name = ?, email = ?, phone = ? WHERE id = ?");
            $stmt->execute([$name, $email, $phone, $_SESSION['user_id']]);

            // Update password if provided
            if (!empty($password)) {
                $hashed_password = hash_password($password);
                $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
                $stmt->execute([$hashed_password, $_SESSION['user_id']]);
            }

            set_flash_message('success', 'Profile updated successfully');
            redirect('profile.php');
        } catch (PDOException $e) {
            $errors['general'] = 'Failed to update profile';
        }
    }
}

require_once '../includes/user_header.php';
?>

<div class="bg-background min-h-screen py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <?php
        // Display current photo if exists at the top
        try {
            $stmt = $pdo->prepare("SELECT photo_path FROM user_photos WHERE user_id = ? ORDER BY uploaded_at DESC LIMIT 1");
            $stmt->execute([$_SESSION['user_id']]);
            $photo = $stmt->fetchColumn();
            if ($photo):
        ?>
            <div class="mb-8 flex justify-center">
-                <img src="<?php echo htmlspecialchars(str_replace('../', '', $photo)); ?>" alt="Profile Photo" class="w-32 h-32 rounded-full object-cover mx-auto">
+                 </div>
        <?php
            endif;
        } catch (PDOException $e) {
            // Ignore photo display errors
        }
        ?>
        <h1 class="text-3xl font-bold text-white mb-8">Your Profile</h1>

        <?php if (isset($errors['general'])): ?>
            <div class="bg-error bg-opacity-10 text-error px-4 py-3 rounded-md mb-6">
                <?php echo $errors['general']; ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="profile.php" enctype="multipart/form-data" class="bg-surface rounded-lg shadow-lg p-6 space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-300">Name</label>
                <input type="text" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" required
                    class="mt-1 block w-full rounded-md bg-gray-800 border-gray-700 text-white">
                <?php if (isset($errors['name'])): ?>
                    <p class="mt-1 text-sm text-error"><?php echo $errors['name']; ?></p>
                <?php endif; ?>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-300">Email</label>
                <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required
                    class="mt-1 block w-full rounded-md bg-gray-800 border-gray-700 text-white">
                <?php if (isset($errors['email'])): ?>
                    <p class="mt-1 text-sm text-error"><?php echo $errors['email']; ?></p>
                <?php endif; ?>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-300">Phone Number</label>
                <input type="tel" name="phone" value="<?php echo htmlspecialchars($user['phone']); ?>" required
                    class="mt-1 block w-full rounded-md bg-gray-800 border-gray-700 text-white">
                <?php if (isset($errors['phone'])): ?>
                    <p class="mt-1 text-sm text-error"><?php echo $errors['phone']; ?></p>
                <?php endif; ?>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-300">New Password (leave blank to keep current)</label>
                <input type="password" name="password"
                    class="mt-1 block w-full rounded-md bg-gray-800 border-gray-700 text-white">
                <?php if (isset($errors['password'])): ?>
                    <p class="mt-1 text-sm text-error"><?php echo $errors['password']; ?></p>
                <?php endif; ?>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-300">Confirm New Password</label>
                <input type="password" name="confirm_password"
                    class="mt-1 block w-full rounded-md bg-gray-800 border-gray-700 text-white">
                <?php if (isset($errors['confirm_password'])): ?>
                    <p class="mt-1 text-sm text-error"><?php echo $errors['confirm_password']; ?></p>
                <?php endif; ?>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-300">Profile Photo URL</label>
                <input type="url" name="photo_url" placeholder="Enter image URL"
                    class="mt-1 block w-full rounded-md bg-gray-800 border-gray-700 text-white" />
                <?php if (isset($errors['photo'])): ?>
                    <p class="mt-1 text-sm text-error"><?php echo $errors['photo']; ?></p>
                <?php endif; ?>
            <div>
                <button type="submit" 
                    class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary hover:bg-opacity-90">
                    Update Profile
                </button>
            </div>
        </form>
        <?php
        // Display current photo if exists
        // Removed duplicate display at bottom since photo is now shown at top
        ?>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>

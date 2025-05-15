<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Admin Login - BHOJON Bondhu</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        background: '#1a1a1a',
                        surface: '#2d2d2d',
                        primary: '#ff4b4b',
                        'primary-dark': '#cc3c3c',
                        error: '#ff4b4b'
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-background min-h-screen">
<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

// Redirect if already logged in as admin
if (is_admin_logged_in()) {
    redirect('dashboard.php');
}

$errors = [];

if (is_post_request()) {
    $username = sanitize_input($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    // Validate inputs
    if (empty($username)) {
        $errors['username'] = 'Username is required';
    }

    if (empty($password)) {
        $errors['password'] = 'Password is required';
    }

    // If no errors, attempt to log in
    if (empty($errors)) {
        $stmt = $pdo->prepare("SELECT * FROM admin WHERE username = ?");
        $stmt->execute([$username]);
        $admin = $stmt->fetch();

        if ($admin && verify_password($password, $admin['password'])) {
            // Set session variables
            $_SESSION['admin_id'] = $admin['id'];
            set_flash_message('success', 'Login successful!');
            redirect('dashboard.php');
        } else {
            $errors['general'] = 'Invalid username or password';
        }
    }
}
?>

<div class="flex items-center justify-center min-h-screen py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full bg-surface rounded-lg shadow-lg p-8">
        <div class="text-center mb-8">
            <h2 class="text-3xl font-bold text-primary">
                Admin Login
            </h2>
            <p class="mt-2 text-sm text-gray-400">
                Access the admin dashboard
            </p>
        </div>

        <?php if (isset($errors['general'])): ?>
            <div class="mb-4 p-4 rounded-md bg-error bg-opacity-10 text-error">
                <?php echo $errors['general']; ?>
            </div>
        <?php endif; ?>

        <form class="space-y-6" method="POST" novalidate>
            <!-- Username -->
            <div>
                <label for="username" class="block text-sm font-medium text-gray-300">
                    Username
                </label>
                <div class="mt-1">
                    <input id="username" name="username" type="text" required 
                        class="appearance-none block w-full px-3 py-2 border border-gray-700 rounded-md shadow-sm bg-gray-800 text-white placeholder-gray-400 focus:outline-none focus:ring-primary focus:border-primary"
                        value="<?php echo isset($username) ? htmlspecialchars($username) : ''; ?>">
                    <?php if (isset($errors['username'])): ?>
                        <p class="mt-1 text-sm text-error"><?php echo $errors['username']; ?></p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Password -->
            <div>
                <label for="password" class="block text-sm font-medium text-gray-300">
                    Password
                </label>
                <div class="mt-1">
                    <input id="password" name="password" type="password" required 
                        class="appearance-none block w-full px-3 py-2 border border-gray-700 rounded-md shadow-sm bg-gray-800 text-white placeholder-gray-400 focus:outline-none focus:ring-primary focus:border-primary">
                    <?php if (isset($errors['password'])): ?>
                        <p class="mt-1 text-sm text-error"><?php echo $errors['password']; ?></p>
                    <?php endif; ?>
                </div>
            </div>

            <div>
                <button type="submit" 
                    class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary hover:bg-primary focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">
                    Login to Admin Panel
                </button>
            </div>
        </form>

        <div class="mt-6 text-center">
            <a href="/" class="text-sm text-primary hover:text-primary-dark">
                <i class="fas fa-arrow-left mr-2"></i> Back to Main Site
            </a>
        </div>
    </div>
</div>

<?php
require_once '../includes/footer.php';
?>
</body>
</html>

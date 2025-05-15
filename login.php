<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

// Redirect if already logged in
if (is_user_logged_in()) {
    redirect('/user/dashboard.php');
}

$errors = [];

if (is_post_request()) {
    $email = sanitize_input($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    // Validate inputs
    if (empty($email)) {
        $errors['email'] = 'Email is required';
    }

    if (empty($password)) {
        $errors['password'] = 'Password is required';
    }

    // If no errors, attempt to log in
    if (empty($errors)) {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && verify_password($password, $user['password'])) {
            // Set user session as logged in
            $_SESSION['user_logged_in'] = true;
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['name'] = $user['name'] ?? '';

            // Redirect to dashboard page
            header('Location: user/dashboard.php');
            exit;
        } else {
            $errors['general'] = 'Invalid email or password';
        }
    }
}

require_once 'includes/header.php';
?>

<div class="flex items-center justify-center min-h-screen bg-background py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full bg-surface rounded-lg shadow-lg p-8">
        <div class="text-center mb-8">
            <h2 class="text-3xl font-bold text-primary">
                Login to Your Account
            </h2>
            <p class="mt-2 text-sm text-gray-400">
                Don't have an account? 
                <a href="register.php" class="text-primary hover:text-primary-dark">
                    Register here
                </a>
            </p>
        </div>

        <?php if (isset($errors['general'])): ?>
            <div class="mb-4 p-4 rounded-md bg-error bg-opacity-10 text-error">
                <?php echo $errors['general']; ?>
            </div>
        <?php endif; ?>

        <form class="space-y-6" method="POST" novalidate>
            <!-- Email -->
            <div>
                <label for="email" class="block text-sm font-medium text-gray-300">
                    Email address
                </label>
                <div class="mt-1">
                    <input id="email" name="email" type="email" required 
                        class="appearance-none block w-full px-3 py-2 border border-gray-700 rounded-md shadow-sm bg-gray-800 text-white placeholder-gray-400 focus:outline-none focus:ring-primary focus:border-primary">
                    <?php if (isset($errors['email'])): ?>
                        <p class="mt-1 text-sm text-error"><?php echo $errors['email']; ?></p>
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
                    Login
                </button>
            </div>
        </form>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>

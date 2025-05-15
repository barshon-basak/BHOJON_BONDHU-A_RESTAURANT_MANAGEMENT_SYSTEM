<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

// Redirect if already logged in
if (is_user_logged_in()) {
    redirect('restaurants.php');
}

$errors = [];

if (is_post_request()) {
    $name = sanitize_input($_POST['name'] ?? '');
    $email = sanitize_input($_POST['email'] ?? '');
    $phone = sanitize_input($_POST['phone'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    // Validate inputs
    if (empty($name)) {
        $errors['name'] = 'Name is required';
    }

    if (empty($email)) {
        $errors['email'] = 'Email is required';
    } elseif (!is_valid_email($email)) {
        $errors['email'] = 'Invalid email format';
    } else {
        // Check if email already exists
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $errors['email'] = 'Email already registered';
        }
    }

    if (empty($phone)) {
        $errors['phone'] = 'Phone number is required';
    } elseif (!is_valid_phone($phone)) {
        $errors['phone'] = 'Invalid phone number format';
    }

    if (empty($password)) {
        $errors['password'] = 'Password is required';
    } elseif (strlen($password) < 6) {
        $errors['password'] = 'Password must be at least 6 characters';
    }

    if ($password !== $confirm_password) {
        $errors['confirm_password'] = 'Passwords do not match';
    }

    // If no errors, create user
    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO users (name, email, phone, password) VALUES (?, ?, ?, ?)");
            $stmt->execute([$name, $email, $phone, hash_password($password)]);
            
            // Set success message and redirect
            set_flash_message('success', 'Registration successful! Please login.');
            redirect('login.php');
        } catch (PDOException $e) {
            $errors['general'] = 'Registration failed. Please try again.';
        }
    }
}
require_once 'includes/main_header.php';
?>

<div class="flex items-center justify-center min-h-screen bg-background py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full bg-surface rounded-lg shadow-lg p-8">
        <div class="text-center mb-8">
            <h2 class="text-3xl font-bold text-primary">
                Create Account
            </h2>
            <p class="mt-2 text-sm text-gray-400">
                Already have an account? 
                <a href="/login.php" class="text-primary hover:text-primary-dark">
                    Login here
                </a>
            </p>
        </div>

        <?php if (isset($errors['general'])): ?>
            <div class="mb-4 p-4 rounded-md bg-error bg-opacity-10 text-error">
                <?php echo $errors['general']; ?>
            </div>
        <?php endif; ?>

        <form class="space-y-6" method="POST" novalidate>
            <!-- Name -->
            <div>
                <label for="name" class="block text-sm font-medium text-gray-300">
                    Full Name
                </label>
                <div class="mt-1">
                    <input id="name" name="name" type="text" required 
                        class="appearance-none block w-full px-3 py-2 border border-gray-700 rounded-md shadow-sm bg-gray-800 text-white placeholder-gray-400 focus:outline-none focus:ring-primary focus:border-primary"
                        value="<?php echo isset($name) ? htmlspecialchars($name) : ''; ?>">
                    <?php if (isset($errors['name'])): ?>
                        <p class="mt-1 text-sm text-error"><?php echo $errors['name']; ?></p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Email -->
            <div>
                <label for="email" class="block text-sm font-medium text-gray-300">
                    Email address
                </label>
                <div class="mt-1">
                    <input id="email" name="email" type="email" required 
                        class="appearance-none block w-full px-3 py-2 border border-gray-700 rounded-md shadow-sm bg-gray-800 text-white placeholder-gray-400 focus:outline-none focus:ring-primary focus:border-primary"
                        value="<?php echo isset($email) ? htmlspecialchars($email) : ''; ?>">
                    <?php if (isset($errors['email'])): ?>
                        <p class="mt-1 text-sm text-error"><?php echo $errors['email']; ?></p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Phone -->
            <div>
                <label for="phone" class="block text-sm font-medium text-gray-300">
                    Phone Number
                </label>
                <div class="mt-1">
                    <input id="phone" name="phone" type="tel" required 
                        class="appearance-none block w-full px-3 py-2 border border-gray-700 rounded-md shadow-sm bg-gray-800 text-white placeholder-gray-400 focus:outline-none focus:ring-primary focus:border-primary"
                        value="<?php echo isset($phone) ? htmlspecialchars($phone) : ''; ?>"
                        placeholder="11-digit number">
                    <?php if (isset($errors['phone'])): ?>
                        <p class="mt-1 text-sm text-error"><?php echo $errors['phone']; ?></p>
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
                        class="appearance-none block w-full px-3 py-2 border border-gray-700 rounded-md shadow-sm bg-gray-800 text-white placeholder-gray-400 focus:outline-none focus:ring-primary focus:border-primary"
                        placeholder="Minimum 6 characters">
                    <?php if (isset($errors['password'])): ?>
                        <p class="mt-1 text-sm text-error"><?php echo $errors['password']; ?></p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Confirm Password -->
            <div>
                <label for="confirm_password" class="block text-sm font-medium text-gray-300">
                    Confirm Password
                </label>
                <div class="mt-1">
                    <input id="confirm_password" name="confirm_password" type="password" required 
                        class="appearance-none block w-full px-3 py-2 border border-gray-700 rounded-md shadow-sm bg-gray-800 text-white placeholder-gray-400 focus:outline-none focus:ring-primary focus:border-primary">
                    <?php if (isset($errors['confirm_password'])): ?>
                        <p class="mt-1 text-sm text-error"><?php echo $errors['confirm_password']; ?></p>
                    <?php endif; ?>
                </div>
            </div>

            <div>
                <button type="submit" 
                    class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary hover:bg-primary focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">
                    Register
                </button>
            </div>
        </form>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>

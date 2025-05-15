<?php
require_once '../includes/header.php';
?>

<div class="flex items-center justify-center min-h-screen bg-background py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full bg-surface rounded-lg shadow-lg p-8 text-center">
        <h2 class="text-3xl font-bold text-primary mb-6">Login or Register to Explore</h2>
        <p class="text-gray-400 mb-8">You need to be logged in to view the menu and explore our restaurants.</p>
        <div class="space-x-4">
            <a href="../login.php" 
                class="inline-block px-6 py-3 bg-primary text-white rounded-md hover:bg-primary-dark font-medium">
                Login
            </a>
            <a href="../register.php" 
                class="inline-block px-6 py-3 bg-secondary text-white rounded-md hover:bg-secondary-dark font-medium">
                Register
            </a>
        </div>
    </div>
</div>

<?php
require_once '../includes/footer.php';
?>

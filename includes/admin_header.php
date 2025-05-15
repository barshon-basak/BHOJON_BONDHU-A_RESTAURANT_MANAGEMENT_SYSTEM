<?php
// Get current page name
$current_page = basename($_SERVER['PHP_SELF'], '.php');

require_once 'functions.php';

// Check if admin is logged in
if (!is_admin_logged_in()) {
    redirect('login.php');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BHOJON Bondhu - Admin Panel</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
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
    <nav class="bg-surface shadow-lg">
        <div class="container mx-auto px-4">
            <div class="flex justify-between items-center h-16">
                <a href="dashboard.php" class="text-2xl font-bold text-white">BHOJON Bondhu Admin</a>
                
                <div class="flex items-center space-x-4">
                    <a href="dashboard.php" 
                        class="text-white hover:text-primary <?php echo $current_page === 'dashboard' ? 'text-primary' : ''; ?>">
                        Dashboard
                    </a>
                    <a href="restaurants.php" 
                        class="text-white hover:text-primary <?php echo $current_page === 'restaurants' ? 'text-primary' : ''; ?>">
                        Restaurants
                    </a>
<a href="orders.php" 
    class="text-white hover:text-primary <?php echo $current_page === 'orders' ? 'text-primary' : ''; ?>">
    Orders
</a>
                    <a href="income.php" 
                        class="text-white hover:text-primary <?php echo $current_page === 'income' ? 'text-primary' : ''; ?>">
                        Income
                    </a>
                    <a href="feedback.php" 
                        class="text-white hover:text-primary <?php echo $current_page === 'feedback' ? 'text-primary' : ''; ?>">
                        Feedback
                    </a>
                    <a href="logout.php" class="text-white hover:text-primary">
                        Logout
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <?php if (isset($_SESSION['flash_message'])): ?>
        <div class="container mx-auto px-4 mt-4">
            <div class="rounded-md p-4 <?php 
                echo isset($_SESSION['flash_type']) && $_SESSION['flash_type'] === 'error' 
                    ? 'bg-error bg-opacity-10 text-error'
                    : 'bg-green-900 bg-opacity-10 text-green-400'
                ?>">
                <?php echo $_SESSION['flash_message']; ?>
            </div>
        </div>
        <?php 
        unset($_SESSION['flash_message']);
        unset($_SESSION['flash_type']);
        ?>
    <?php endif; ?>

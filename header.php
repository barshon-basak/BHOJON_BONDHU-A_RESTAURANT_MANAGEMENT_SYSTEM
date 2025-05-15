<?php
$current_page = basename($_SERVER['PHP_SELF'], '.php');
require_once 'functions.php';

$is_user_logged_in = isset($_SESSION['user_id']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>BHOJON Bondhu - Restaurant Management System</title>
<script src="https://cdn.tailwindcss.com"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
<style>
@keyframes glow {
  0%, 100% {
    text-shadow: 0 0 5px #ff4b4b, 0 0 10px #ff4b4b, 0 0 20px #ff4b4b;
  }
  50% {
    text-shadow: 0 0 10px #ff0000, 0 0 20px #ff0000, 0 0 30px #ff0000;
  }
}
.animate-glow {
  animation: glow 2s infinite alternate;
}
</style>
<script>
tailwind.config = {
  theme: {
    extend: {
      colors: {
        background: '#1a1a1a',
        surface: '#2d2d2d',
        primary: '#ff4b4b',
        'primary-dark': '#cc3c3c',
        error: '#ff4b4b',
      },
      fontFamily: {
        logo: ['"Pacifico"', 'cursive'],
      },
    },
  },
};
</script>
</head>
<body class="bg-background min-h-screen flex flex-col">
<?php
$flash = get_flash_message();
if ($flash):
?>
<div class="container mx-auto mt-4 px-4">
    <div class="rounded-md p-4 <?php echo $flash['type'] === 'success' ? 'bg-green-600 text-white' : 'bg-red-600 text-white'; ?>">
        <?php echo htmlspecialchars($flash['message']); ?>
    </div>
</div>
<?php
endif;
?>
<header class="bg-primary shadow-lg">
  <div class="container mx-auto flex items-center justify-between px-6 py-4">
    <a href="/user/dashboard.php" class="flex items-center space-x-3">
      <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQ8cKQRJkP5SoEjKLSCOG46FjLKcN8xquXisg&s" alt="BHOJON Bondhu Logo" class="h-12 w-12 rounded-full" />
      <span class="text-white font-logo text-4xl select-none animate-glow">Bhojon Bondhu</span>
    </a>
    <nav class="hidden md:flex space-x-8 text-white font-semibold">
      <?php if ($is_user_logged_in): ?>
          <a href="/user/dashboard.php" class="hover:text-yellow-300 <?php echo $current_page === 'dashboard' ? 'text-yellow-300' : ''; ?>">Dashboard</a>
          <a href="/user/restaurants.php" class="hover:text-yellow-300 <?php echo $current_page === 'restaurants' ? 'text-yellow-300' : ''; ?>">Restaurants</a>
          <a href="/user/orders.php" class="hover:text-yellow-300 <?php echo $current_page === 'orders' ? 'text-yellow-300' : ''; ?>">My Orders</a>
          <a href="/user/cart.php" class="relative hover:text-yellow-300 <?php echo $current_page === 'cart' ? 'text-yellow-300' : ''; ?>">
            <i class="fas fa-shopping-cart fa-lg"></i>
            <?php if (isset($_SESSION['cart']) && !empty($_SESSION['cart']['items'])): ?>
              <span class="absolute -top-2 -right-3 bg-yellow-400 text-black rounded-full text-xs px-2 py-0.5 font-bold"><?php echo count($_SESSION['cart']['items']); ?></span>
            <?php endif; ?>
          </a>
          <a href="user/profile.php" class="hover:text-yellow-300 <?php echo $current_page === 'profile' ? 'text-yellow-300' : ''; ?>">Profile</a>
          <a href="user/logout.php" class="hover:text-yellow-300">Logout</a>
      <?php else: ?>
        <a href="login.php" class="hover:text-yellow-300">Login</a>
        <a href="register.php" class="hover:text-yellow-300">Register</a>
      <?php endif; ?>
    </nav>
    <button id="mobile-menu-button" class="md:hidden text-white focus:outline-none" aria-label="Toggle menu">
      <i class="fas fa-bars fa-lg"></i>
    </button>
  </div>
  <div id="mobile-menu" class="hidden md:hidden bg-primary-dark text-white px-6 py-4 flex flex-col space-y-4 shadow-lg">
    <?php if ($is_user_logged_in): ?>
        <a href="user/dashboard.php" class="block hover:text-yellow-300">Dashboard</a>
        <a href="user/restaurants.php" class="block hover:text-yellow-300">Restaurants</a>
        <a href="user/orders.php" class="block hover:text-yellow-300">My Orders</a>
        <a href="user/cart.php" class="block hover:text-yellow-300">Cart</a>
        <a href="user/profile.php" class="block hover:text-yellow-300">Profile</a>
        <a href="user/logout.php" class="block hover:text-yellow-300">Logout</a>
    <?php else: ?>
      <a href="login.php" class="block hover:text-yellow-300">Login</a>
      <a href="register.php" class="block hover:text-yellow-300">Register</a>
    <?php endif; ?>
  </div>
</header>

<script>
  const mobileMenuButton = document.getElementById('mobile-menu-button');
  const mobileMenu = document.getElementById('mobile-menu');

  if (mobileMenuButton && mobileMenu) {
    mobileMenuButton.addEventListener('click', () => {
      mobileMenu.classList.toggle('hidden');
    });
  }
</script>

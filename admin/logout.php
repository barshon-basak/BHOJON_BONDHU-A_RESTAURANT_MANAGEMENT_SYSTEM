<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

// Clear admin session
unset($_SESSION['admin_id']);
session_destroy();

// Start a new session to set flash message
session_start();
set_flash_message('success', 'You have been logged out successfully.');

// Redirect to admin login
redirect('login.php');

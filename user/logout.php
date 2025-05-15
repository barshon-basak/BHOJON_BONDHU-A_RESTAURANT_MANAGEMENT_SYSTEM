<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

// Clear user session
unset($_SESSION['user_id']);
session_destroy();

// Redirect to login
redirect('../login.php');

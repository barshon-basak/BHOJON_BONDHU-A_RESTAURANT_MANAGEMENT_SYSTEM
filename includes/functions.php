<?php
// Input sanitization
function sanitize_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Password hashing
function hash_password($password) {
    return password_hash($password, PASSWORD_DEFAULT);
}

// Verify password
function verify_password($password, $hash) {
    return password_verify($password, $hash);
}

// Check if user is logged in
function is_user_logged_in() {
    return isset($_SESSION['user_id']);
}

// Check if admin is logged in
function is_admin_logged_in() {
    return isset($_SESSION['admin_id']);
}

// Redirect function
function redirect($location) {
    header("Location: $location");
    exit();
}

// Generate random string for tokens
function generate_token($length = 32) {
    return bin2hex(random_bytes($length));
}

// Flash messages
function set_flash_message($type, $message) {
    $_SESSION['flash'] = [
        'type' => $type,
        'message' => $message
    ];
}

function get_flash_message() {
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

// Format price
function format_price($price) {
    return number_format($price, 2);
}

// Validate email
function is_valid_email($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

// Validate phone number
function is_valid_phone($phone) {
    return preg_match('/^[0-9]{11}$/', $phone);
}

// Check if request is POST
function is_post_request() {
    return $_SERVER['REQUEST_METHOD'] === 'POST';
}

// Check if request is GET
function is_get_request() {
    return $_SERVER['REQUEST_METHOD'] === 'GET';
}

// Require authentication
function require_auth() {
    if (!is_user_logged_in()) {
        set_flash_message('error', 'Please login to continue.');
        redirect('/login.php');
    }
}

// Require admin authentication
function require_admin() {
    if (!is_admin_logged_in()) {
        set_flash_message('error', 'Unauthorized access.');
        redirect('login.php');
    }
}

// Get current user data
function get_logged_in_user() {
    global $pdo;
    if (is_user_logged_in()) {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        return $stmt->fetch();
    }
    return null;
}

// Get current admin data
function get_logged_in_admin() {
    global $pdo;
    if (is_admin_logged_in()) {
        $stmt = $pdo->prepare("SELECT * FROM admin WHERE id = ?");
        $stmt->execute([$_SESSION['admin_id']]);
        return $stmt->fetch();
    }
    return null;
}

// Upload image
function upload_image($file, $target_dir = "uploads/") {
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }
    
    $target_file = $target_dir . basename($file["name"]);
    $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
    
    // Check if image file is a actual image or fake image
    $check = getimagesize($file["tmp_name"]);
    if($check === false) {
        return ["error" => "File is not an image."];
    }
    
    // Check file size
    if ($file["size"] > 5000000) {
        return ["error" => "Sorry, your file is too large."];
    }
    
    // Allow certain file formats
    if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif" ) {
        return ["error" => "Sorry, only JPG, JPEG, PNG & GIF files are allowed."];
    }
    
    // Generate unique filename
    $new_filename = uniqid() . '.' . $imageFileType;
    $target_file = $target_dir . $new_filename;
    
    if (move_uploaded_file($file["tmp_name"], $target_file)) {
        return ["success" => true, "filename" => $target_file];
    } else {
        return ["error" => "Sorry, there was an error uploading your file."];
    }
}

// Calculate order total
function calculate_order_total($items) {
    $total = 0;
    foreach ($items as $item) {
        $total += $item['price'] * $item['quantity'];
    }
    return $total;
}

// Get order status label
function get_order_status_label($status) {
    $labels = [
        'pending' => 'Pending',
        'processing' => 'Processing',
        'completed' => 'Completed',
        'cancelled' => 'Cancelled'
    ];
    return $labels[$status] ?? $status;
}

// Format date
function format_date($date) {
    return date('F j, Y g:i A', strtotime($date));
}
?>

<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

// Admin credentials
$admins = [
    ['username' => 'barshon123', 'password' => 'nsuonlychill'],
    ['username' => 'zunayed123', 'password' => 'nsuonlychill'],
];

foreach ($admins as $admin) {
    $username = $admin['username'];
    $password = hash_password($admin['password']);

    try {
        $stmt = $pdo->prepare("INSERT INTO admin (username, password) VALUES (?, ?)");
        $stmt->execute([$username, $password]);
    } catch (PDOException $e) {
        echo "Error inserting admin $username: " . $e->getMessage() . "\n";
    }
}

echo "Admin accounts created successfully.";
?>

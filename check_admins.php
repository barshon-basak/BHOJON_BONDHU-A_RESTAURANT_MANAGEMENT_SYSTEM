<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

try {
    $stmt = $pdo->query("SELECT * FROM admin");
    $admins = $stmt->fetchAll();

    if ($admins) {
        foreach ($admins as $admin) {
            echo "Username: " . htmlspecialchars($admin['username']) . "\n";
        }
    } else {
        echo "No admin accounts found.";
    }
} catch (PDOException $e) {
    echo "Error fetching admin accounts: " . $e->getMessage();
}
?>

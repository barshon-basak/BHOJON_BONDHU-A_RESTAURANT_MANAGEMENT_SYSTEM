<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

// Require user authentication
require_auth();

require_once '../vendor/autoload.php'; // Assuming TCPDF is installed via Composer

use TCPDF;

// Get order ID from query parameter
$order_id = $_GET['order_id'] ?? null;
if (!$order_id) {
    die('Order ID is required');
}

// Fetch order details
try {
    $stmt = $pdo->prepare("
        SELECT o.id, o.user_id, o.total_amount, o.status, o.created_at,
               pm.method as payment_method,
               oi.dish_id, oi.quantity, oi.price, d.name as dish_name
        FROM orders o
        LEFT JOIN payment_methods pm ON o.id = pm.order_id
        LEFT JOIN order_items oi ON o.id = oi.order_id
        LEFT JOIN dishes d ON oi.dish_id = d.id
        WHERE o.id = ?
    ");
    $stmt->execute([$order_id]);
    $order_data = $stmt->fetchAll();

    if (!$order_data) {
        die('Order not found');
    }
} catch (PDOException $e) {
    die('Failed to fetch order details: ' . $e->getMessage());
}

// Prepare PDF
$pdf = new TCPDF();
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Restaurant Management');
$pdf->SetTitle('Order Bill #' . $order_id);
$pdf->SetMargins(15, 15, 15);
$pdf->AddPage();

$html = '<h1>Order Bill #' . htmlspecialchars($order_id) . '</h1>';
$html .= '<p><strong>Order Date:</strong> ' . htmlspecialchars($order_data[0]['created_at']) . '</p>';
$html .= '<p><strong>Payment Method:</strong> ' . htmlspecialchars($order_data[0]['payment_method']) . '</p>';
$html .= '<table border="1" cellpadding="5">
            <thead>
                <tr>
                    <th>Dish</th>
                    <th>Quantity</th>
                    <th>Price</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>';

$total_amount = 0;
foreach ($order_data as $item) {
    $line_total = $item['quantity'] * $item['price'];
    $total_amount += $line_total;
    $html .= '<tr>
                <td>' . htmlspecialchars($item['dish_name']) . '</td>
                <td>' . htmlspecialchars($item['quantity']) . '</td>
                <td>$' . number_format($item['price'], 2) . '</td>
                <td>$' . number_format($line_total, 2) . '</td>
              </tr>';
}

$html .= '</tbody></table>';
$html .= '<h3>Total Amount: $' . number_format($total_amount, 2) . '</h3>';

$pdf->writeHTML($html, true, false, true, false, '');

$pdf->Output('order_bill_' . $order_id . '.pdf', 'D');
exit;
?>

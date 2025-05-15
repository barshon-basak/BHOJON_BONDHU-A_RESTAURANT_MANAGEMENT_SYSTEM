<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

// Require admin authentication
require_admin();

// Get filter parameters
$period = $_GET['period'] ?? 'all';
$start_date = $_GET['start_date'] ?? '';
$end_date = $_GET['end_date'] ?? '';

// First, ensure we have an income table
try {
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS income (
            id INT PRIMARY KEY AUTO_INCREMENT,
            order_id INT UNIQUE,
            amount DECIMAL(10,2) NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (order_id) REFERENCES orders(id)
        )
    ");
} catch (PDOException $e) {
    // Table might already exist, continue
}

// Add any completed orders that aren't in the income table yet
try {
    $pdo->exec("
        INSERT IGNORE INTO income (order_id, amount, created_at)
        SELECT id, total_amount, created_at
        FROM orders
        WHERE status = 'completed'
        AND id NOT IN (SELECT order_id FROM income)
    ");
} catch (PDOException $e) {
    // Handle error silently
}

// Build the SQL query based on filters
$sql = "SELECT 
            DATE(i.created_at) as date,
            COUNT(i.id) as total_orders,
            SUM(i.amount) as daily_revenue
        FROM income i
        WHERE 1=1";

switch ($period) {
    case 'today':
        $sql .= " AND DATE(i.created_at) = CURDATE()";
        break;
    case 'week':
        $sql .= " AND i.created_at >= DATE_SUB(NOW(), INTERVAL 1 WEEK)";
        break;
    case 'month':
        $sql .= " AND i.created_at >= DATE_SUB(NOW(), INTERVAL 1 MONTH)";
        break;
    case 'custom':
        if ($start_date && $end_date) {
            $sql .= " AND DATE(i.created_at) BETWEEN ? AND ?";
        }
        break;
}

$sql .= " GROUP BY DATE(i.created_at) ORDER BY date DESC";

try {
    if ($period === 'custom' && $start_date && $end_date) {
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$start_date, $end_date]);
    } else {
        $stmt = $pdo->query($sql);
    }
    $income_data = $stmt->fetchAll();

    // Calculate totals
    $total_revenue = array_sum(array_column($income_data, 'daily_revenue'));
    $total_orders = array_sum(array_column($income_data, 'total_orders'));
    $avg_order_value = $total_orders > 0 ? $total_revenue / $total_orders : 0;

} catch (PDOException $e) {
    $errors['general'] = 'Failed to fetch income data';
    $income_data = [];
}

?>
<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

// Require admin authentication
require_admin();

// Get filter parameters
$period = $_GET['period'] ?? 'all';
$start_date = $_GET['start_date'] ?? '';
$end_date = $_GET['end_date'] ?? '';

// First, ensure we have an income table
try {
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS income (
            id INT PRIMARY KEY AUTO_INCREMENT,
            order_id INT UNIQUE,
            amount DECIMAL(10,2) NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (order_id) REFERENCES orders(id)
        )
    ");
} catch (PDOException $e) {
    // Table might already exist, continue
}

// Add any completed orders that aren't in the income table yet
try {
    $pdo->exec("
        INSERT IGNORE INTO income (order_id, amount, created_at)
        SELECT id, total_amount, created_at
        FROM orders
        WHERE status = 'completed'
        AND id NOT IN (SELECT order_id FROM income)
    ");
} catch (PDOException $e) {
    // Handle error silently
}

// Build the SQL query based on filters
$sql = "SELECT 
            DATE(i.created_at) as date,
            COUNT(i.id) as total_orders,
            SUM(i.amount) as daily_revenue
        FROM income i
        WHERE 1=1";

switch ($period) {
    case 'today':
        $sql .= " AND DATE(i.created_at) = CURDATE()";
        break;
    case 'week':
        $sql .= " AND i.created_at >= DATE_SUB(NOW(), INTERVAL 1 WEEK)";
        break;
    case 'month':
        $sql .= " AND i.created_at >= DATE_SUB(NOW(), INTERVAL 1 MONTH)";
        break;
    case 'custom':
        if ($start_date && $end_date) {
            $sql .= " AND DATE(i.created_at) BETWEEN ? AND ?";
        }
        break;
}

$sql .= " GROUP BY DATE(i.created_at) ORDER BY date DESC";

try {
    if ($period === 'custom' && $start_date && $end_date) {
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$start_date, $end_date]);
    } else {
        $stmt = $pdo->query($sql);
    }
    $income_data = $stmt->fetchAll();

    // Calculate totals
    $total_revenue = array_sum(array_column($income_data, 'daily_revenue'));
    $total_orders = array_sum(array_column($income_data, 'total_orders'));
    $avg_order_value = $total_orders > 0 ? $total_revenue / $total_orders : 0;

} catch (PDOException $e) {
    $errors['general'] = 'Failed to fetch income data';
    $income_data = [];
}

require_once '../includes/admin_header.php';
?>

<main class="max-w-7xl mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold text-white mb-8">Income Management</h1>

    <!-- Filter Form -->
    <div class="bg-surface rounded-lg shadow-lg p-6 mb-8">
        <form method="GET" class="flex flex-wrap gap-4 items-end">
            <div>
                <label class="block text-sm font-medium text-gray-300 mb-2">Period</label>
                <select name="period" onchange="toggleCustomDates(this.value)"
                    class="rounded-md bg-gray-800 border-gray-700 text-white">
                    <option value="all" <?php echo $period === 'all' ? 'selected' : ''; ?>>All Time</option>
                    <option value="today" <?php echo $period === 'today' ? 'selected' : ''; ?>>Today</option>
                    <option value="week" <?php echo $period === 'week' ? 'selected' : ''; ?>>Last 7 Days</option>
                    <option value="month" <?php echo $period === 'month' ? 'selected' : ''; ?>>Last 30 Days</option>
                    <option value="custom" <?php echo $period === 'custom' ? 'selected' : ''; ?>>Custom Range</option>
                </select>
            </div>

            <div id="customDates" class="<?php echo $period === 'custom' ? 'flex' : 'hidden'; ?> gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">Start Date</label>
                    <input type="date" name="start_date" value="<?php echo $start_date; ?>"
                        class="rounded-md bg-gray-800 border-gray-700 text-white">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">End Date</label>
                    <input type="date" name="end_date" value="<?php echo $end_date; ?>"
                        class="rounded-md bg-gray-800 border-gray-700 text-white">
                </div>
            </div>

            <div>
                <button type="submit" 
                    class="px-4 py-2 bg-primary text-white rounded-md hover:bg-opacity-90">
                    Apply Filter
                </button>
            </div>
        </form>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <!-- Total Revenue -->
        <div class="bg-surface rounded-lg shadow-lg p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-primary bg-opacity-20">
                    <i class="fas fa-dollar-sign text-primary text-2xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-400">Total Revenue</p>
                    <p class="text-2xl font-bold text-white">$<?php echo number_format($total_revenue, 2); ?></p>
                </div>
            </div>
        </div>

        <!-- Total Orders -->
        <div class="bg-surface rounded-lg shadow-lg p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-secondary bg-opacity-20">
                    <i class="fas fa-shopping-cart text-secondary text-2xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-400">Total Orders</p>
                    <p class="text-2xl font-bold text-white"><?php echo $total_orders; ?></p>
                </div>
            </div>
        </div>

        <!-- Average Order Value -->
        <div class="bg-surface rounded-lg shadow-lg p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-primary bg-opacity-20">
                    <i class="fas fa-chart-line text-primary text-2xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-400">Average Order Value</p>
                    <p class="text-2xl font-bold text-white">$<?php echo number_format($avg_order_value, 2); ?></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Daily Revenue Table -->
    <div class="bg-surface rounded-lg shadow-lg overflow-hidden">
        <table class="w-full">
            <thead>
                <tr class="bg-surface border-b border-gray-700">
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Date</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Orders</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Revenue</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-700">
                <?php foreach ($income_data as $data): ?>
                    <tr>
                        <td class="px-6 py-4"><?php echo date('F j, Y', strtotime($data['date'])); ?></td>
                        <td class="px-6 py-4"><?php echo $data['total_orders']; ?></td>
                        <td class="px-6 py-4">$<?php echo number_format($data['daily_revenue'], 2); ?></td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($income_data)): ?>
                    <tr>
                        <td colspan="3" class="px-6 py-4 text-center text-gray-400">
                            No income data found for the selected period
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</main>

<script>
    function toggleCustomDates(value) {
        const customDates = document.getElementById('customDates');
        customDates.style.display = value === 'custom' ? 'flex' : 'none';
    }
</script>

<?php
require_once '../includes/footer.php';

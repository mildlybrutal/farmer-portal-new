<?php
require_once '../includes/auth_middleware.php';
require_once '../config/database.php';
check_retailer_auth();

$retailer_id = $_SESSION['user_id'];

// Get all orders for this retailer
$orders_sql = "SELECT o.*, 
               u.username as farmer_name,
               GROUP_CONCAT(p.name) as products,
               GROUP_CONCAT(oi.quantity) as quantities,
               GROUP_CONCAT(oi.price_per_unit) as prices
               FROM orders o
               JOIN users u ON o.seller_id = u.id
               JOIN order_items oi ON o.id = oi.order_id
               JOIN products p ON oi.product_id = p.id
               WHERE o.buyer_id = ?
               GROUP BY o.id
               ORDER BY o.created_at DESC";

$orders_stmt = mysqli_prepare($conn, $orders_sql);
mysqli_stmt_bind_param($orders_stmt, "i", $retailer_id);
mysqli_stmt_execute($orders_stmt);
$orders_result = mysqli_stmt_get_result($orders_stmt);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Orders - Farmer's Portal</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">
    <nav class="bg-green-600 text-white shadow-lg">
        <div class="container mx-auto px-4 py-3">
            <div class="flex justify-between items-center">
                <a href="/" class="text-xl font-bold">Farmer's Portal</a>
                <div class="space-x-4">
                    <a href="dashboard.php" class="hover:text-gray-200">Dashboard</a>
                    <a href="../auth/logout.php" class="hover:text-gray-200">Logout</a>
                </div>
            </div>
        </div>
    </nav>

    <main class="container mx-auto px-4 py-8">
        <h1 class="text-3xl font-bold text-gray-800 mb-8">My Orders</h1>

        <div class="bg-white shadow-md rounded-lg overflow-hidden">
            <?php if (mysqli_num_rows($orders_result) > 0): ?>
                <table class="min-w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Order ID</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Farmer</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Products</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total Amount</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <?php while ($order = mysqli_fetch_assoc($orders_result)): ?>
                            <?php
                            $products = explode(',', $order['products']);
                            $quantities = explode(',', $order['quantities']);
                            $prices = explode(',', $order['prices']);
                            $product_details = array();
                            
                            for ($i = 0; $i < count($products); $i++) {
                                $product_details[] = $quantities[$i] . ' × ' . $products[$i] . ' (₹' . $prices[$i] . '/unit)';
                            }
                            ?>
                            <tr>
                                <td class="px-6 py-4">#<?php echo $order['id']; ?></td>
                                <td class="px-6 py-4"><?php echo htmlspecialchars($order['farmer_name']); ?></td>
                                <td class="px-6 py-4">
                                    <ul class="list-disc list-inside">
                                        <?php foreach ($product_details as $detail): ?>
                                            <li><?php echo htmlspecialchars($detail); ?></li>
                                        <?php endforeach; ?>
                                    </ul>
                                </td>
                                <td class="px-6 py-4">₹<?php echo number_format($order['total_amount'], 2); ?></td>
                                <td class="px-6 py-4">
                                    <?php
                                    $status_colors = [
                                        'pending' => 'yellow',
                                        'confirmed' => 'blue',
                                        'completed' => 'green',
                                        'cancelled' => 'red'
                                    ];
                                    $color = $status_colors[$order['status']];
                                    ?>
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-<?php echo $color; ?>-100 text-<?php echo $color; ?>-800">
                                        <?php echo ucfirst($order['status']); ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4"><?php echo date('M j, Y', strtotime($order['created_at'])); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p class="p-4 text-gray-600">No orders found.</p>
            <?php endif; ?>
        </div>
    </main>
</body>
</html>

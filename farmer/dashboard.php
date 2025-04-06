<?php
require_once '../includes/auth_middleware.php';
require_once '../config/database.php';
check_farmer_auth();

// Get farmer's products
$farmer_id = $_SESSION['user_id'];
$products_sql = "SELECT * FROM products WHERE farmer_id = ? ORDER BY created_at DESC";
$products_stmt = mysqli_prepare($conn, $products_sql);
mysqli_stmt_bind_param($products_stmt, "i", $farmer_id);
mysqli_stmt_execute($products_stmt);
$products_result = mysqli_stmt_get_result($products_stmt);

// Get pending bids
$bids_sql = "SELECT b.*, u.username as retailer_name, p.name as product_name 
             FROM bids b 
             JOIN users u ON b.retailer_id = u.id 
             JOIN products p ON b.product_id = p.id 
             WHERE b.farmer_id = ? AND b.status = 'pending' 
             ORDER BY b.created_at DESC";
$bids_stmt = mysqli_prepare($conn, $bids_sql);
mysqli_stmt_bind_param($bids_stmt, "i", $farmer_id);
mysqli_stmt_execute($bids_stmt);
$bids_result = mysqli_stmt_get_result($bids_stmt);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Farmer Dashboard - Farmer's Portal</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">
    <nav class="bg-green-600 text-white shadow-lg">
        <div class="container mx-auto px-4 py-3">
            <div class="flex justify-between items-center">
                <a href="/" class="text-xl font-bold">Farmer's Portal</a>
                <div class="space-x-4">
                    <a href="add_product.php" class="hover:text-gray-200">Add Product</a>
                    <a href="/auth/logout.php" class="hover:text-gray-200">Logout</a>
                </div>
            </div>
        </div>
    </nav>

    <main class="container mx-auto px-4 py-8">
        <h1 class="text-3xl font-bold text-gray-800 mb-8">Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?></h1>

        <!-- Pending Bids Section -->
        <section class="mb-8">
            <h2 class="text-2xl font-semibold text-gray-700 mb-4">Pending Bids</h2>
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <?php if (mysqli_num_rows($bids_result) > 0): ?>
                    <table class="min-w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Product</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Retailer</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Quantity</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Bid Amount</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <?php while ($bid = mysqli_fetch_assoc($bids_result)): ?>
                                <tr>
                                    <td class="px-6 py-4"><?php echo htmlspecialchars($bid['product_name']); ?></td>
                                    <td class="px-6 py-4"><?php echo htmlspecialchars($bid['retailer_name']); ?></td>
                                    <td class="px-6 py-4"><?php echo htmlspecialchars($bid['quantity']); ?></td>
                                    <td class="px-6 py-4">₹<?php echo htmlspecialchars($bid['bid_amount']); ?></td>
                                    <td class="px-6 py-4">
                                        <form method="POST" action="handle_bid.php" class="inline">
                                            <input type="hidden" name="bid_id" value="<?php echo $bid['id']; ?>">
                                            <button type="submit" name="action" value="accept" 
                                                class="bg-green-500 text-white px-3 py-1 rounded mr-2 hover:bg-green-600">
                                                Accept
                                            </button>
                                            <button type="submit" name="action" value="reject"
                                                class="bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600">
                                                Reject
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p class="p-4 text-gray-600">No pending bids at the moment.</p>
                <?php endif; ?>
            </div>
        </section>

        <!-- Products Section -->
        <section>
            <h2 class="text-2xl font-semibold text-gray-700 mb-4">Your Products</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php while ($product = mysqli_fetch_assoc($products_result)): ?>
                    <div class="bg-white rounded-lg shadow overflow-hidden">
                        <?php if ($product['image_url']): ?>
                            <img src="<?php echo htmlspecialchars($product['image_url']); ?>" 
                                alt="<?php echo htmlspecialchars($product['name']); ?>"
                                class="w-full h-48 object-cover">
                        <?php endif; ?>
                        <div class="p-4">
                            <h3 class="text-xl font-semibold text-gray-800"><?php echo htmlspecialchars($product['name']); ?></h3>
                            <p class="text-gray-600 mt-2"><?php echo htmlspecialchars($product['description']); ?></p>
                            <div class="mt-4 flex justify-between items-center">
                                <span class="text-green-600 font-semibold">₹<?php echo htmlspecialchars($product['price']); ?>/<?php echo htmlspecialchars($product['unit']); ?></span>
                                <span class="text-gray-500">Stock: <?php echo htmlspecialchars($product['quantity']); ?> <?php echo htmlspecialchars($product['unit']); ?></span>
                            </div>
                            <div class="mt-4">
                                <a href="edit_product.php?id=<?php echo $product['id']; ?>" 
                                    class="inline-block bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
                                    Edit Product
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        </section>
    </main>
</body>
</html>

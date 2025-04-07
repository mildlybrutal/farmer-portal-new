<?php
require_once '../includes/auth_middleware.php';
require_once '../config/database.php';
check_retailer_auth();

// Get all available products
$products_sql = "SELECT p.*, u.username as farmer_name, u.id as farmer_id 
                 FROM products p 
                 JOIN users u ON p.farmer_id = u.id 
                 WHERE p.quantity > 0 AND p.status = 'available' 
                 ORDER BY p.created_at DESC";
$products_result = mysqli_query($conn, $products_sql);

// Get retailer's active bids
$retailer_id = $_SESSION['user_id'];
$bids_sql = "SELECT b.*, p.name as product_name, u.username as farmer_name 
             FROM bids b 
             JOIN products p ON b.product_id = p.id 
             JOIN users u ON b.farmer_id = u.id 
             WHERE b.retailer_id = ? AND b.status = 'pending' 
             ORDER BY b.created_at DESC";
$bids_stmt = mysqli_prepare($conn, $bids_sql);
mysqli_stmt_bind_param($bids_stmt, "i", $retailer_id);
mysqli_stmt_execute($bids_stmt);
$bids_result = mysqli_stmt_get_result($bids_stmt);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Retailer Dashboard - Farmer's Portal</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">
    <nav class="bg-green-600 text-white shadow-lg">
        <div class="container mx-auto px-4 py-3">
            <div class="flex justify-between items-center">
                <a href="/" class="text-xl font-bold">Farmer's Portal</a>
                <div class="space-x-4">
                    <a href="orders.php" class="hover:text-gray-200">My Orders</a>
                    <a href="../auth/logout.php" class="hover:text-gray-200">Logout</a>
                </div>
            </div>
        </div>
    </nav>

    <main class="container mx-auto px-4 py-8">
        <h1 class="text-3xl font-bold text-gray-800 mb-8">Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?></h1>

        <!-- Active Bids Section -->
        <section class="mb-8">
            <h2 class="text-2xl font-semibold text-gray-700 mb-4">Your Active Bids</h2>
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <?php if (mysqli_num_rows($bids_result) > 0): ?>
                    <table class="min-w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Product</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Farmer</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Quantity</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Your Bid</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <?php while ($bid = mysqli_fetch_assoc($bids_result)): ?>
                                <tr>
                                    <td class="px-6 py-4"><?php echo htmlspecialchars($bid['product_name']); ?></td>
                                    <td class="px-6 py-4"><?php echo htmlspecialchars($bid['farmer_name']); ?></td>
                                    <td class="px-6 py-4"><?php echo htmlspecialchars($bid['quantity']); ?></td>
                                    <td class="px-6 py-4">₹<?php echo htmlspecialchars($bid['bid_amount']); ?></td>
                                    <td class="px-6 py-4">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                            Pending
                                        </span>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p class="p-4 text-gray-600">No active bids at the moment.</p>
                <?php endif; ?>
            </div>
        </section>

        <!-- Available Products Section -->
        <section>
            <h2 class="text-2xl font-semibold text-gray-700 mb-4">Available Products</h2>
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
                            <p class="text-sm text-gray-500 mt-2">Seller: <?php echo htmlspecialchars($product['farmer_name']); ?></p>
                            <div class="mt-4 flex justify-between items-center">
                                <span class="text-green-600 font-semibold">₹<?php echo htmlspecialchars($product['price']); ?>/<?php echo htmlspecialchars($product['unit']); ?></span>
                                <span class="text-gray-500">Available: <?php echo htmlspecialchars($product['quantity']); ?> <?php echo htmlspecialchars($product['unit']); ?></span>
                            </div>
                            <div class="mt-4">
                                <button onclick="openBidModal(<?php 
                                    echo htmlspecialchars(json_encode([
                                        'id' => $product['id'],
                                        'name' => $product['name'],
                                        'farmer_id' => $product['farmer_id'],
                                        'price' => $product['price'],
                                        'unit' => $product['unit'],
                                        'quantity' => $product['quantity']
                                    ])); 
                                ?>)" class="w-full bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
                                    Place Bid
                                </button>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        </section>
    </main>

    <!-- Bid Modal -->
    <div id="bidModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Place a Bid</h3>
                <form id="bidForm" action="place_bid.php" method="POST" class="space-y-4">
                    <input type="hidden" name="product_id" id="bidProductId">
                    <input type="hidden" name="farmer_id" id="bidFarmerId">
                    
                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-2">Product</label>
                        <p id="bidProductName" class="text-gray-600"></p>
                    </div>

                    <div>
                        <label for="bidQuantity" class="block text-gray-700 text-sm font-bold mb-2">Quantity</label>
                        <input type="number" id="bidQuantity" name="quantity" step="0.01" required
                            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                        <p id="maxQuantity" class="text-sm text-gray-500 mt-1"></p>
                    </div>

                    <div>
                        <label for="bidAmount" class="block text-gray-700 text-sm font-bold mb-2">Bid Amount (per unit)</label>
                        <input type="number" id="bidAmount" name="bid_amount" step="0.01" required
                            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                        <p id="marketPrice" class="text-sm text-gray-500 mt-1"></p>
                    </div>

                    <div>
                        <label for="bidMessage" class="block text-gray-700 text-sm font-bold mb-2">Message to Farmer</label>
                        <textarea id="bidMessage" name="message" rows="3"
                            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"></textarea>
                    </div>

                    <div class="flex justify-end space-x-4">
                        <button type="button" onclick="closeBidModal()"
                            class="px-4 py-2 bg-gray-200 text-gray-800 rounded hover:bg-gray-300">Cancel</button>
                        <button type="submit"
                            class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">Submit Bid</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function openBidModal(product) {
            document.getElementById('bidModal').classList.remove('hidden');
            document.getElementById('bidProductId').value = product.id;
            document.getElementById('bidFarmerId').value = product.farmer_id;
            document.getElementById('bidProductName').textContent = product.name;
            document.getElementById('maxQuantity').textContent = `Maximum available: ${product.quantity} ${product.unit}`;
            document.getElementById('marketPrice').textContent = `Market price: ₹${product.price}/${product.unit}`;
            
            // Set max quantity in input
            document.getElementById('bidQuantity').max = product.quantity;
        }

        function closeBidModal() {
            document.getElementById('bidModal').classList.add('hidden');
            document.getElementById('bidForm').reset();
        }

        // Close modal when clicking outside
        document.getElementById('bidModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeBidModal();
            }
        });
    </script>
</body>
</html>

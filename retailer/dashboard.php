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
$bids_sql = "SELECT b.*, b.bid_amount as amount, p.name as product_name, u.username as farmer_name 
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
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#f0fdfa',
                            100: '#ccfbf1',
                            200: '#99f6e4',
                            300: '#5eead4',
                            400: '#2dd4bf',
                            500: '#14b8a6',
                            600: '#0d9488',
                            700: '#0f766e',
                            800: '#115e59',
                            900: '#134e4a',
                        }
                    },
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                        display: ['Poppins', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    <style>
        .glass-effect {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }
        
        .card-shadow {
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            transition: all 0.4s ease;
        }
        
        .card-shadow:hover {
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.1);
            transform: translateY(-5px);
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        @keyframes slideUp {
            from { opacity: 0; transform: translateY(100px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .animate-fadeIn {
            animation: fadeIn 0.8s cubic-bezier(0.4, 0, 0.2, 1) forwards;
            opacity: 0;
        }
        
        .animate-slideUp {
            animation: slideUp 0.5s cubic-bezier(0.4, 0, 0.2, 1) forwards;
            opacity: 0;
        }
        
        .bg-gradient {
            background: linear-gradient(135deg, #f0fdfa 0%, #ccfbf1 50%, #99f6e4 100%);
        }
        
        .modal-overlay {
            backdrop-filter: blur(8px);
        }
    </style>
<script>
    // Show popup if insufficient quantity error is present in the URL
    document.addEventListener('DOMContentLoaded', function() {
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.get('error') === 'insufficient_quantity') {
            alert('Cannot place bid: Requested quantity is not available.');
        }
    });
</script>
</head>
<body class="bg-gradient min-h-screen font-sans">
    <nav class="glass-effect text-slate-800 shadow-sm fixed w-full top-0 z-50">
        <div class="container mx-auto px-4 py-3">
            <div class="flex justify-between items-center">
                <a href="../index.php" class="text-xl font-display font-bold text-teal-600 hover:text-teal-700 transition-all">Farmer's Portal</a>
                <div class="space-x-4">
                    <a href="orders.php" class="font-medium text-slate-600 hover:text-teal-600 transition-all">My Orders</a>
                    <span class="font-medium text-slate-600">Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?></span>
                    <a href="../auth/logout.php" class="font-medium text-slate-600 hover:text-teal-600 transition-all">Logout</a>
                </div>
            </div>
        </div>
    </nav>

    <main class="container mx-auto px-4 py-8 mt-16">
        <h1 class="text-4xl font-display font-bold text-slate-800 mb-8 animate-fadeIn">Welcome to Your Dashboard</h1>

        <!-- Active Bids Section -->
        <section class="mb-12 animate-fadeIn" style="animation-delay: 0.2s;">
            <h2 class="text-2xl font-display font-semibold text-slate-700 mb-6 flex items-center gap-2">
                <svg class="w-6 h-6 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
                Your Active Bids
            </h2>
            <div class="grid gap-6">
                <?php while ($bid = mysqli_fetch_assoc($bids_result)): ?>
                    <div class="glass-effect rounded-xl p-6 card-shadow">
                        <div class="flex flex-wrap items-center justify-between gap-4">
                            <div>
                                <h3 class="text-lg font-semibold text-slate-800 mb-2"><?php echo htmlspecialchars($bid['product_name']); ?></h3>
                                <p class="text-slate-600">Farmer: <?php echo htmlspecialchars($bid['farmer_name']); ?></p>
                                <p class="text-slate-600">Amount: ₹<?php echo number_format($bid['amount'], 2); ?></p>
                                <p class="text-sm text-slate-500 mt-2">Placed on: <?php echo date('M j, Y g:i A', strtotime($bid['created_at'])); ?></p>
                            </div>
                            <span class="px-4 py-2 bg-yellow-100 text-yellow-800 rounded-full text-sm font-medium">
                                Pending Response
                            </span>
                        </div>
                    </div>
                <?php endwhile; ?>
                <?php if (mysqli_num_rows($bids_result) == 0): ?>
                    <div class="glass-effect rounded-xl p-6 text-center text-slate-600">
                        No active bids at the moment.
                    </div>
                <?php endif; ?>
            </div>
        </section>

        <!-- Available Products Section -->
        <section class="animate-fadeIn" style="animation-delay: 0.4s;">
            <h2 class="text-2xl font-display font-semibold text-slate-700 mb-6 flex items-center gap-2">
                <svg class="w-6 h-6 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                </svg>
                Available Products
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php while ($product = mysqli_fetch_assoc($products_result)): ?>
                    <div class="glass-effect rounded-xl overflow-hidden card-shadow">
                        <?php if (!empty($product['image_url'])): ?>
                            <img src="../<?php echo htmlspecialchars($product['image_url']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" class="w-full h-48 object-cover">
                        <?php else: ?>
                            <div class="w-full h-48 bg-slate-100 flex items-center justify-center">
                                <svg class="w-12 h-12 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                            </div>
                        <?php endif; ?>
                        <div class="p-6">
                            <div class="flex justify-between items-start mb-4">
                                <div>
                                    <h3 class="text-lg font-semibold text-slate-800 mb-1"><?php echo htmlspecialchars($product['name']); ?></h3>
                                    <p class="text-sm text-slate-500">by <?php echo htmlspecialchars($product['farmer_name']); ?></p>
                                </div>
                                <span class="text-lg font-semibold text-teal-600">₹<?php echo number_format($product['price'], 2); ?>/kg</span>
                            </div>
                            <p class="text-slate-600 mb-4"><?php echo htmlspecialchars($product['description']); ?></p>
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-slate-500">Available: <?php echo $product['quantity']; ?> kg</span>
                                <button onclick="openBidModal('<?php echo $product['id']; ?>', '<?php echo htmlspecialchars($product['name']); ?>', '<?php echo $product['farmer_id']; ?>')" 
                                    class="px-6 py-2 bg-teal-600 text-white rounded-full hover:bg-teal-700 transition-all font-medium text-sm">
                                    Place Bid
                                </button>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
                <?php if (mysqli_num_rows($products_result) == 0): ?>
                    <div class="col-span-full glass-effect rounded-xl p-6 text-center text-slate-600">
                        No products available at the moment.
                    </div>
                <?php endif; ?>
            </div>
        </section>
    </main>

    <!-- Bid Modal -->
    <div id="bidModal" class="hidden fixed inset-0 bg-slate-900/50 modal-overlay overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-8 w-full max-w-md">
            <div class="glass-effect rounded-2xl shadow-xl animate-slideUp">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-xl font-display font-bold text-slate-800" id="modalTitle">Place a Bid</h3>
                        <button onclick="closeBidModal()" class="text-slate-400 hover:text-slate-500 transition-all">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                    <form action="place_bid.php" method="POST" class="space-y-4">
                        <input type="hidden" name="product_id" id="productId">
                        <input type="hidden" name="farmer_id" id="farmerId">
                        
                        <div>
                            <label for="quantity" class="block text-sm font-medium text-slate-700 mb-2">Quantity (kg)</label>
                            <input type="number" name="quantity" id="quantity" required min="1" step="1"
                                class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-teal-500 focus:border-transparent transition-all">
                        </div>
                        
                        <div>
                            <label for="bid_amount" class="block text-sm font-medium text-slate-700 mb-2">Bid Amount (₹ per kg)</label>
                            <input type="number" name="bid_amount" id="bid_amount" required min="0" step="0.01"
                                class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-teal-500 focus:border-transparent transition-all">
                        </div>

                        <button type="submit" class="w-full px-6 py-3 bg-teal-600 text-white rounded-lg hover:bg-teal-700 transition-all font-medium">
                            Submit Bid
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function openBidModal(productId, productName, farmerId) {
            document.getElementById('bidModal').classList.remove('hidden');
            document.getElementById('modalTitle').textContent = 'Place a Bid for ' + productName;
            document.getElementById('productId').value = productId;
            document.getElementById('farmerId').value = farmerId;
            document.body.style.overflow = 'hidden';
        }

        function closeBidModal() {
            document.getElementById('bidModal').classList.add('hidden');
            document.body.style.overflow = 'auto';
        }
    </script>
</body>
</html>

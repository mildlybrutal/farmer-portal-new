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
        
        .animate-fadeIn {
            animation: fadeIn 0.8s cubic-bezier(0.4, 0, 0.2, 1) forwards;
            opacity: 0;
        }
        
        .bg-gradient {
            background: linear-gradient(135deg, #f0fdfa 0%, #ccfbf1 50%, #99f6e4 100%);
        }
    </style>
</head>
<body class="bg-gradient min-h-screen font-sans">
    <nav class="glass-effect text-slate-800 shadow-sm fixed w-full top-0 z-50">
        <div class="container mx-auto px-4 py-3">
            <div class="flex justify-between items-center">
                <a href="/" class="text-xl font-display font-bold text-teal-600 hover:text-teal-700 transition-all">Farmer's Portal</a>
                <div class="space-x-4">
                    <span class="font-medium text-slate-600">Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?></span>
                    <a href="../auth/logout.php" class="font-medium text-slate-600 hover:text-teal-600 transition-all">Logout</a>
                </div>
            </div>
        </div>
    </nav>

    <main class="container mx-auto px-4 py-8 mt-16">
        <h1 class="text-4xl font-display font-bold text-slate-800 mb-8 animate-fadeIn">Welcome to Your Dashboard</h1>

        <!-- Pending Bids Section -->
        <section class="mb-12 animate-fadeIn" style="animation-delay: 0.2s;">
            <h2 class="text-2xl font-display font-semibold text-slate-700 mb-6 flex items-center gap-2">
                <svg class="w-6 h-6 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
                Pending Bids
            </h2>
            <div class="grid gap-6">
                <?php while ($bid = mysqli_fetch_assoc($bids_result)): ?>
                    <div class="glass-effect rounded-xl p-6 card-shadow">
                        <div class="flex flex-wrap items-center justify-between gap-4">
                            <div>
                                <h3 class="text-lg font-semibold text-slate-800 mb-2"><?php echo htmlspecialchars($bid['product_name']); ?></h3>
                                <p class="text-slate-600">Bid by: <?php echo htmlspecialchars($bid['retailer_name']); ?></p>
                                <p class="text-slate-600">Amount: ₹<?php echo number_format($bid['bid_amount'], 2); ?></p>
                                <p class="text-sm text-slate-500 mt-2">Placed on: <?php echo date('M j, Y g:i A', strtotime($bid['created_at'])); ?></p>
                            </div>
                            <div class="flex gap-3">
                                <form action="handle_bid.php" method="POST" class="inline">
                                    <input type="hidden" name="bid_id" value="<?php echo $bid['id']; ?>">
                                    <input type="hidden" name="action" value="accept">
                                    <button type="submit" class="px-6 py-2 bg-teal-600 text-white rounded-full hover:bg-teal-700 transition-all font-medium">
                                        Accept
                                    </button>
                                </form>
                                <form action="handle_bid.php" method="POST" class="inline">
                                    <input type="hidden" name="bid_id" value="<?php echo $bid['id']; ?>">
                                    <input type="hidden" name="action" value="reject">
                                    <button type="submit" class="px-6 py-2 bg-white text-slate-600 rounded-full hover:bg-slate-50 transition-all font-medium border border-slate-200">
                                        Reject
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
                <?php if (mysqli_num_rows($bids_result) == 0): ?>
                    <div class="glass-effect rounded-xl p-6 text-center text-slate-600">
                        No pending bids at the moment.
                    </div>
                <?php endif; ?>
            </div>
        </section>

        <!-- Products Section -->
        <section class="animate-fadeIn" style="animation-delay: 0.4s;">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-display font-semibold text-slate-700 flex items-center gap-2">
                    <svg class="w-6 h-6 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                    Your Products
                </h2>
                <a href="add_product.php" class="px-6 py-2 bg-teal-600 text-white rounded-full hover:bg-teal-700 transition-all font-medium flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    Add Product
                </a>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php while ($product = mysqli_fetch_assoc($products_result)): ?>
                    <div class="glass-effect rounded-xl overflow-hidden card-shadow">
                        <?php if (!empty($product['image_url'])): ?>
                            <img src="<?php echo htmlspecialchars($product['image_url']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" class="w-full h-48 object-cover">
                        <?php else: ?>
                            <div class="w-full h-48 bg-slate-100 flex items-center justify-center">
                                <svg class="w-12 h-12 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                            </div>
                        <?php endif; ?>
                        <div class="p-6">
                            <h3 class="text-lg font-semibold text-slate-800 mb-2"><?php echo htmlspecialchars($product['name']); ?></h3>
                            <p class="text-slate-600 mb-4"><?php echo htmlspecialchars($product['description']); ?></p>
                            <div class="flex justify-between items-center">
                                <span class="text-lg font-semibold text-teal-600">₹<?php echo number_format($product['price'], 2); ?>/kg</span>
                                <span class="text-sm text-slate-500">Quantity: <?php echo $product['quantity']; ?> kg</span>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
                <?php if (mysqli_num_rows($products_result) == 0): ?>
                    <div class="col-span-full glass-effect rounded-xl p-6 text-center text-slate-600">
                        No products listed yet. Click "Add Product" to get started.
                    </div>
                <?php endif; ?>
            </div>
        </section>
    </main>
</body>
</html>

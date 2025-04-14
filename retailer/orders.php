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
        
        .card-shadow {
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
        }
        
        .card-shadow:hover {
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.1);
            transform: translateY(-2px);
        }
    </style>
</head>
<body class="bg-gradient min-h-screen font-sans">
    <div class="fixed inset-0 bg-gradient-conic from-teal-50 via-white to-emerald-50 opacity-60 pointer-events-none"></div>

    <nav class="glass-effect text-slate-800 shadow-sm fixed w-full top-0 z-50">
        <div class="container mx-auto px-4 py-3">
            <div class="flex justify-between items-center">
                <a href="../index.php" class="text-xl font-display font-bold text-teal-600 hover:text-teal-700 transition-all flex items-center gap-2">
                    <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M10.394 2.08a1 1 0 00-.788 0l-7 3a1 1 0 000 1.84L5.25 8.051a.999.999 0 01.356-.257l4-1.714a1 1 0 11.788 1.838l-2.727 1.17 3.721 1.596a1 1 0 00.788 0l7-3a1 1 0 000-1.84l-7-3zM3.31 9.397L5 10.12v4.102a8.969 8.969 0 00-1.05-.174 1 1 0 01-.89-.89 11.115 11.115 0 01.25-3.762zM9.3 16.573A9.026 9.026 0 007 14.935v-3.957l1.818.78a3 3 0 002.364 0l5.508-2.361a11.026 11.026 0 01.25 3.762 1 1 0 01-.89.89 8.968 8.968 0 00-5.35 2.524 1 1 0 01-1.4 0zM6 18a1 1 0 001-1v-2.065a8.935 8.935 0 00-2-.712V17a1 1 0 001 1z"/>
                    </svg>
                    Farmer's Portal
                </a>
                <div class="space-x-4">
                    <a href="dashboard.php" class="font-medium text-slate-600 hover:text-teal-600 transition-all">Dashboard</a>
                    <span class="font-medium text-slate-600">Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?></span>
                    <a href="../auth/logout.php" class="font-medium text-slate-600 hover:text-teal-600 transition-all">Logout</a>
                </div>
            </div>
        </div>
    </nav>

    <main class="container mx-auto px-4 py-8 mt-16">
        <div class="flex items-center justify-between mb-8 animate-fadeIn">
            <h1 class="text-3xl font-display font-bold text-slate-800">My Orders</h1>
            <a href="dashboard.php" class="px-6 py-2.5 bg-teal-600 text-white rounded-full hover:bg-teal-700 transition-all shadow-md hover:shadow-lg font-medium text-sm">
                Browse Products
            </a>
        </div>

        <div class="space-y-6">
            <?php if (mysqli_num_rows($orders_result) > 0): ?>
                <?php while ($order = mysqli_fetch_assoc($orders_result)): 
                    $products = explode(',', $order['products']);
                    $quantities = explode(',', $order['quantities']);
                    $prices = explode(',', $order['prices']);
                ?>
                    <div class="glass-effect rounded-2xl p-6 card-shadow animate-fadeIn">
                        <div class="flex justify-between items-start mb-4">
                            <div>
                                <h3 class="text-lg font-semibold text-slate-800 mb-1">Order #<?php echo htmlspecialchars($order['id']); ?></h3>
                                <p class="text-sm text-slate-600">
                                    Placed on <?php echo date('F j, Y', strtotime($order['created_at'])); ?>
                                </p>
                            </div>
                            <span class="px-4 py-1 rounded-full text-sm font-medium 
                                <?php echo $order['status'] === 'completed' ? 'bg-green-100 text-green-800' : 
                                        ($order['status'] === 'pending' ? 'bg-yellow-100 text-yellow-800' : 
                                        'bg-red-100 text-red-800'); ?>">
                                <?php echo ucfirst(htmlspecialchars($order['status'])); ?>
                            </span>
                        </div>
                        
                        <div class="border-t border-slate-200 my-4"></div>
                        
                        <div class="space-y-3">
                            <?php for ($i = 0; $i < count($products); $i++): ?>
                                <div class="flex justify-between items-center text-sm">
                                    <div class="flex items-center gap-2">
                                        <span class="text-slate-800"><?php echo htmlspecialchars($products[$i]); ?></span>
                                        <span class="text-slate-500">×</span>
                                        <span class="text-slate-600"><?php echo htmlspecialchars($quantities[$i]); ?></span>
                                    </div>
                                    <span class="text-slate-800 font-medium">₹<?php echo number_format($prices[$i] * $quantities[$i], 2); ?></span>
                                </div>
                            <?php endfor; ?>
                        </div>
                        
                        <div class="border-t border-slate-200 my-4"></div>
                        
                        <div class="flex justify-between items-center">
                            <div class="text-sm">
                                <span class="text-slate-600">Seller:</span>
                                <span class="text-slate-800 font-medium ml-1"><?php echo htmlspecialchars($order['farmer_name']); ?></span>
                            </div>
                            <div class="text-lg font-semibold text-slate-800">
                                Total: ₹<?php echo number_format($order['total_amount'], 2); ?>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="glass-effect rounded-2xl p-8 text-center animate-fadeIn">
                    <svg class="w-16 h-16 text-slate-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                    </svg>
                    <h3 class="text-xl font-display font-semibold text-slate-800 mb-2">No Orders Yet</h3>
                    <p class="text-slate-600 mb-6">Start shopping to see your orders here</p>
                    <a href="dashboard.php" class="inline-block px-6 py-2.5 bg-teal-600 text-white rounded-full hover:bg-teal-700 transition-all shadow-md hover:shadow-lg font-medium text-sm">
                        Browse Products
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </main>
</body>
</html>

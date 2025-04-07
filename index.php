<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Farmer's Portal</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">
    <nav class="bg-green-600 text-white shadow-lg">
        <div class="container mx-auto px-4 py-3">
            <div class="flex justify-between items-center">
                <a href="/" class="text-xl font-bold">Farmer's Portal</a>
                <div class="space-x-4">
                    <?php
                    session_start();
                    if (isset($_SESSION['user_id'])) {
                        $dashboard = $_SESSION['user_type'] === 'farmer' ? '/farmer/dashboard.php' : '/retailer/dashboard.php';
                        echo '<a href="' . $dashboard . '" class="hover:text-gray-200">Dashboard</a>';
                        echo '<a href="/auth/logout.php" class="hover:text-gray-200">Logout</a>';
                    } else {
                        echo '<a href="auth/login.php" class="hover:text-gray-200">Login</a>';
                        echo '<a href="auth/register.php" class="hover:text-gray-200">Register</a>';
                    }
                    ?>
                </div>
            </div>
        </div>
    </nav>

    <main class="container mx-auto px-4 py-8">
        <div class="text-center mb-12">
            <h1 class="text-4xl font-bold text-gray-800 mb-4">Welcome to Farmer's Portal</h1>
            <p class="text-xl text-gray-600">Connect directly with farmers and retailers</p>
        </div>

        <div class="grid md:grid-cols-2 gap-8 max-w-4xl mx-auto">
            <div class="bg-white p-6 rounded-lg shadow-md">
                <h2 class="text-2xl font-semibold text-green-600 mb-4">For Farmers</h2>
                <ul class="space-y-2 text-gray-600">
                    <li>• List your products for direct sale</li>
                    <li>• Receive bids from retailers</li>
                    <li>• Manage orders and inventory</li>
                </ul>
                <a href="auth/register.php?type=farmer" class="mt-4 inline-block bg-green-600 text-white px-6 py-2 rounded hover:bg-green-700">Register as Farmer</a>
            </div>

            <div class="bg-white p-6 rounded-lg shadow-md">
                <h2 class="text-2xl font-semibold text-green-600 mb-4">For Retailers</h2>
                <ul class="space-y-2 text-gray-600">
                    <li>• Browse available products</li>
                    <li>• Place bids for bulk orders</li>
                    <li>• Direct communication with farmers</li>
                </ul>
                <a href="auth/register.php?type=retailer" class="mt-4 inline-block bg-green-600 text-white px-6 py-2 rounded hover:bg-green-700">Register as Retailer</a>
            </div>
        </div>
    </main>

    <footer class="bg-gray-100 mt-12">
        <div class="container mx-auto px-4 py-6 text-center text-gray-600">
            <p>&copy; 2025 Farmer's Portal. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>

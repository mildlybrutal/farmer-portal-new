<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Farmer's Portal</title>
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
                    },
                    backgroundImage: {
                        'gradient-radial': 'radial-gradient(var(--tw-gradient-stops))',
                        'gradient-conic': 'conic-gradient(var(--tw-gradient-stops))',
                    }
                }
            }
        }
    </script>
    <style>
        .transition-all { transition-duration: 400ms; }
        .hover-scale { transition: transform 0.4s cubic-bezier(0.4, 0, 0.2, 1); }
        .hover-scale:hover { transform: scale(1.03); }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        @keyframes slideIn {
            from { opacity: 0; transform: translateX(-20px); }
            to { opacity: 1; transform: translateX(0); }
        }
        
        @keyframes float {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
            100% { transform: translateY(0px); }
        }
        
        .animate-fadeIn { animation: fadeIn 0.8s cubic-bezier(0.4, 0, 0.2, 1) forwards; }
        .animate-slideIn { animation: slideIn 0.8s cubic-bezier(0.4, 0, 0.2, 1) forwards; }
        .animate-float { animation: float 3s ease-in-out infinite; }
        
        .bg-gradient {
            background: linear-gradient(135deg, #f0fdfa 0%, #ccfbf1 50%, #99f6e4 100%);
        }
        
        .card-shadow {
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            transition: box-shadow 0.4s ease, transform 0.4s ease;
        }
        
        .card-shadow:hover {
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.1);
            transform: translateY(-5px);
        }
        
        .glass-effect {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }
    </style>
</head>
<body class="bg-gradient font-sans min-h-screen">
    <div class="fixed inset-0 bg-gradient-conic from-teal-50 via-white to-emerald-50 opacity-60 pointer-events-none"></div>
    
    <nav class="glass-effect text-slate-800 shadow-sm fixed w-full top-0 z-50 transition-all">
        <div class="container mx-auto px-4 py-4">
            <div class="flex justify-between items-center">
                <a href="../index.php" class="text-2xl font-display font-bold text-teal-600 hover:text-teal-700 transition-all flex items-center gap-2">
                    <svg class="w-8 h-8 animate-float" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M10.394 2.08a1 1 0 00-.788 0l-7 3a1 1 0 000 1.84L5.25 8.051a.999.999 0 01.356-.257l4-1.714a1 1 0 11.788 1.838l-2.727 1.17 3.721 1.596a1 1 0 00.788 0l7-3a1 1 0 000-1.84l-7-3zM3.31 9.397L5 10.12v4.102a8.969 8.969 0 00-1.05-.174 1 1 0 01-.89-.89 11.115 11.115 0 01.25-3.762zM9.3 16.573A9.026 9.026 0 007 14.935v-3.957l1.818.78a3 3 0 002.364 0l5.508-2.361a11.026 11.026 0 01.25 3.762 1 1 0 01-.89.89 8.968 8.968 0 00-5.35 2.524 1 1 0 01-1.4 0zM6 18a1 1 0 001-1v-2.065a8.935 8.935 0 00-2-.712V17a1 1 0 001 1z"/>
                    </svg>
                    Farmer's Portal
                </a>
                <div class="space-x-6">
                    <?php
                    if (isset($_SESSION['user_id'])) {
                        $dashboard = $_SESSION['user_type'] === 'farmer' ? '/farmer/dashboard.php' : '/retailer/dashboard.php';
                        echo '<a href="' . $dashboard . '" class="font-medium text-slate-600 hover:text-teal-600 transition-all">Dashboard</a>';
                        echo '<a href="auth/logout.php" class="font-medium text-slate-600 hover:text-teal-600 transition-all">Logout</a>';
                    } else {
                        echo '<a href="auth/login.php" class="font-medium text-slate-600 hover:text-teal-600 transition-all">Login</a>';
                        echo '<a href="auth/register.php" class="px-6 py-2.5 bg-teal-600 text-white rounded-full hover:bg-teal-700 transition-all shadow-md hover:shadow-lg font-medium">Register</a>';
                    }
                    ?>
                </div>
            </div>
        </div>
    </nav>

    <main class="container mx-auto px-4 py-8 mt-24 relative z-10">
        <div class="text-center mb-20">
            <h1 class="text-6xl font-display font-bold text-slate-800 mb-6 opacity-0 animate-[fadeIn_1s_ease-in_forwards] leading-tight">
                Connect & Grow with<br><span class="text-teal-600 relative inline-block after:content-[''] after:absolute after:-bottom-2 after:left-0 after:w-full after:h-1 after:bg-teal-200 after:rounded-full">Farmer's Portal</span>
            </h1>
            <p class="text-xl text-slate-600 mb-12 opacity-0 animate-[fadeIn_1s_ease-in_0.3s_forwards] max-w-2xl mx-auto">
                Empowering farmers and retailers through direct connections, transparent pricing, and efficient agricultural trade.
            </p>
            <div class="flex justify-center gap-6 opacity-0 animate-[fadeIn_1s_ease-in_0.6s_forwards]">
                <a href="auth/register.php" class="px-8 py-3 bg-teal-600 text-white rounded-full hover:bg-teal-700 transition-all shadow-lg hover:shadow-xl font-medium text-lg hover:-translate-y-1">
                    Get Started
                </a>
                <a href="#features" class="px-8 py-3 bg-white text-teal-600 rounded-full hover:bg-teal-50 transition-all shadow-md hover:shadow-lg font-medium text-lg hover:-translate-y-1">
                    Learn More
                </a>
            </div>
        </div>

        <section id="features" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 mb-20">
            <div class="glass-effect rounded-2xl p-8 card-shadow opacity-0 animate-[fadeIn_1s_ease-in_0.9s_forwards]">
                <div class="text-teal-600 mb-4">
                    <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-slate-800 mb-4">List Your Products</h3>
                <p class="text-slate-600">Easily list your agricultural products and reach a wider market of potential buyers.</p>
            </div>
            <div class="glass-effect rounded-2xl p-8 card-shadow opacity-0 animate-[fadeIn_1s_ease-in_1.2s_forwards]">
                <div class="text-teal-600 mb-4">
                    <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3"/>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-slate-800 mb-4">Fair Bidding System</h3>
                <p class="text-slate-600">Transparent bidding process ensures fair prices for both farmers and retailers.</p>
            </div>
            <div class="glass-effect rounded-2xl p-8 card-shadow opacity-0 animate-[fadeIn_1s_ease-in_1.5s_forwards]">
                <div class="text-teal-600 mb-4">
                    <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z"/>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-slate-800 mb-4">Direct Communication</h3>
                <p class="text-slate-600">Connect directly with buyers and sellers to negotiate better deals.</p>
            </div>
        </section>
    </main>
</body>
</html>

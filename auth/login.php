<?php
session_start();
require_once '../config/database.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = $_POST['password'];

    $sql = "SELECT id, username, password, user_type FROM users WHERE username = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "s", $username);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($user = mysqli_fetch_assoc($result)) {
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['user_type'] = $user['user_type'];
            
            header("Location: ../" . $user['user_type'] . "/dashboard.php");
            exit();
        }
    }
    $error = 'Invalid username or password';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Farmer's Portal</title>
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
        
        .form-input {
            transition: all 0.3s ease;
        }
        
        .form-input:focus {
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
                <a href="register.php" class="font-medium text-slate-600 hover:text-teal-600 transition-all">Register</a>
            </div>
        </div>
    </nav>

    <main class="container mx-auto px-4 min-h-screen flex items-center justify-center py-16">
        <div class="w-full max-w-md animate-fadeIn" style="animation-delay: 0.2s;">
            <div class="glass-effect rounded-2xl p-8 shadow-xl">
                <div class="text-center mb-8">
                    <h1 class="text-3xl font-display font-bold text-slate-800 mb-2">Welcome Back</h1>
                    <p class="text-slate-600">Sign in to continue to your dashboard</p>
                </div>

                <?php if ($error): ?>
                    <div class="bg-red-50 text-red-700 p-4 rounded-xl mb-6 text-center text-sm">
                        <?php echo htmlspecialchars($error); ?>
                    </div>
                <?php endif; ?>

                <form method="POST" class="space-y-6">
                    <div>
                        <label for="username" class="block text-sm font-medium text-slate-700 mb-2">Username</label>
                        <input type="text" id="username" name="username" required 
                            class="form-input w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:ring-2 focus:ring-teal-500 focus:border-transparent transition-all"
                            placeholder="Enter your username">
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-medium text-slate-700 mb-2">Password</label>
                        <input type="password" id="password" name="password" required 
                            class="form-input w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:ring-2 focus:ring-teal-500 focus:border-transparent transition-all"
                            placeholder="Enter your password">
                    </div>
                    <div>
                        <a href="forgot.php"><label for="forgot_pass" class="text-sm">Forget Password?</label></a>
                    </div>

                    <button type="submit" 
                        class="w-full bg-teal-600 text-white py-3 px-4 rounded-xl hover:bg-teal-700 transition-all font-medium text-sm hover:-translate-y-0.5 transform focus:outline-none focus:ring-2 focus:ring-teal-500 focus:ring-offset-2">
                        Sign In
                    </button>

                    <p class="text-center text-sm text-slate-600">
                        Don't have an account? 
                        <a href="register.php" class="text-teal-600 hover:text-teal-700 font-medium">Register here</a>
                    </p>
                </form>
            </div>
        </div>
    </main>
</body>
</html>

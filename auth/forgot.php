<?php
session_start();
require_once '../config/database.php';

$error = '';
$success = '';
$showChangeForm = false;

// Step 1: User submits username and email to request password reset
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['request_reset'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);

    $sql = "SELECT id FROM users WHERE username = ? AND email = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ss", $username, $email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($user = mysqli_fetch_assoc($result)) {
        $_SESSION['reset_user_id'] = $user['id'];
        $_SESSION['reset_username'] = $username;
        $showChangeForm = true;
    } else {
        $error = 'Username and email do not match.';
    }
}

// Step 2: User submits new password
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_password'])) {
    $password = $_POST['password'];
    $password1 = $_POST['password1'];
    if ($password !== $password1) {
        $error = 'Passwords do not match.';
        $showChangeForm = true;
    } elseif (strlen($password) < 8) {
        $error = 'Password must be at least 8 characters.';
        $showChangeForm = true;
    } elseif (isset($_SESSION['reset_user_id'])) {
        $user_id = $_SESSION['reset_user_id'];
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $update_sql = "UPDATE users SET password = ? WHERE id = ?";
        $update_stmt = mysqli_prepare($conn, $update_sql);
        mysqli_stmt_bind_param($update_stmt, "si", $hashed_password, $user_id);
        if (mysqli_stmt_execute($update_stmt)) {
            $success = 'Password changed successfully!';
            unset($_SESSION['reset_user_id']);
            unset($_SESSION['reset_username']);
        } else {
            $error = 'Failed to update password.';
            $showChangeForm = true;
        }
    } else {
        $error = 'Session expired. Please try again.';
    }
}

if (isset($_SESSION['reset_user_id'])) {
    $showChangeForm = true;
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
                    <h1 class="text-3xl font-display font-bold text-slate-800 mb-2">Forgot Password</h1>
                    <p class="text-slate-600">Enter your username and email to reset your password.</p>
                </div>

                <?php if ($error): ?>
                    <div class="bg-red-50 text-red-700 p-4 rounded-xl mb-6 text-center text-sm">
                        <?php echo htmlspecialchars($error); ?>
                    </div>
                <?php endif; ?>
                <?php if ($success): ?>
                    <div class="bg-green-50 text-green-700 p-4 rounded-xl mb-6 text-center text-sm">
                        <?php echo htmlspecialchars($success); ?>
                        <div class="mt-2">
                            <a href="login.php" class="text-teal-600 hover:text-teal-700 font-medium underline">Return to Login</a>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if (!$showChangeForm && !$success): ?>
                <form method="POST" class="space-y-6">
                    <div>
                        <label for="username" class="block text-sm font-medium text-slate-700 mb-2">Username</label>
                        <input type="text" id="username" name="username" required 
                            class="form-input w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:ring-2 focus:ring-teal-500 focus:border-transparent transition-all"
                            placeholder="Enter your username">
                    </div>
                    <div>
                        <label for="email" class="block text-sm font-medium text-slate-700 mb-2">Email</label>
                        <input type="email" id="email" name="email" required 
                            class="form-input w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:ring-2 focus:ring-teal-500 focus:border-transparent transition-all"
                            placeholder="Enter your email">
                    </div>
                    <button type="submit" name="request_reset"
                        class="w-full bg-teal-600 text-white py-3 px-4 rounded-xl hover:bg-teal-700 transition-all font-medium text-sm hover:-translate-y-0.5 transform focus:outline-none focus:ring-2 focus:ring-teal-500 focus:ring-offset-2">
                        Request Password Reset
                    </button>
                    <p class="text-center text-sm text-slate-600">
                        Remembered your password? 
                        <a href="login.php" class="text-teal-600 hover:text-teal-700 font-medium">Login here</a>
                    </p>
                </form>
                <?php endif; ?>

                <?php if ($showChangeForm && !$success): ?>
                <form method="POST" class="space-y-6">
                    <div>
                        <label for="password" class="block text-sm font-medium text-slate-700 mb-2">New Password</label>
                        <input type="password" id="password" name="password" required 
                            class="form-input w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:ring-2 focus:ring-teal-500 focus:border-transparent transition-all"
                            placeholder="Enter new password">
                    </div>
                    <div>
                        <label for="password1" class="block text-sm font-medium text-slate-700 mb-2">Confirm New Password</label>
                        <input type="password" id="password1" name="password1" required 
                            class="form-input w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:ring-2 focus:ring-teal-500 focus:border-transparent transition-all"
                            placeholder="Confirm new password">
                    </div>
                    <button type="submit" name="change_password"
                        class="w-full bg-teal-600 text-white py-3 px-4 rounded-xl hover:bg-teal-700 transition-all font-medium text-sm hover:-translate-y-0.5 transform focus:outline-none focus:ring-2 focus:ring-teal-500 focus:ring-offset-2">
                        Change Password
                    </button>
                </form>
                <?php endif; ?>
            </div>
        </div>
    </main>
</body>
</html>

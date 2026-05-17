<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Eunoia IA</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="style.css">
</head>
<body class="bg-gray-50 flex flex-col min-h-screen">
    <header class="bg-white shadow-sm sticky top-0 z-50">
        <div class="container mx-auto px-4 py-3 flex justify-between items-center">
            <a href="index.php" class="flex items-center gap-3">
                <img src="images/logo.jpg" class="h-10 w-10 rounded-full border shadow-sm" alt="Logo">
                <span class="text-xl font-extrabold tracking-tight text-gray-900">EUNOIA IA</span>
            </a>

            <div class="flex-1 max-w-md mx-8 hidden md:block">
                <div class="relative">
                    <input type="text" id="searchInput" placeholder="Search products..." 
                           class="w-full pl-10 pr-4 py-2 rounded-lg border border-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
                    <div class="absolute left-3 top-2.5 text-gray-400">🔍</div>
                </div>
            </div>

            <nav class="flex items-center gap-6">
                <?php if (isset($_SESSION['role'])): ?>
                    <?php if ($_SESSION['role'] === 'customer'): ?>
                        <a href="cart.php" class="text-gray-600 hover:text-blue-600 transition font-medium">Cart 🛒</a>
                    <?php elseif ($_SESSION['role'] === 'seller'): ?>
                        <a href="seller_dashboard.php" class="text-gray-600 hover:text-blue-600 transition font-medium">Dashboard</a>
                    <?php elseif ($_SESSION['role'] === 'admin'): ?>
                        <a href="admin.php" class="text-gray-600 hover:text-blue-600 transition font-medium">Admin Panel</a>
                    <?php endif; ?>
                    <a href="logout.php" class="text-red-500 hover:text-red-700 transition font-medium">Logout</a>
                <?php else: ?>
                    <a href="login.php" class="text-gray-600 hover:text-blue-600 transition font-medium">Login</a>
                    <a href="register.php" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition font-medium">Sign Up</a>
                <?php endif; ?>
            </nav>
        </div>
    </header>
    <main class="flex-grow container mx-auto px-4 py-8">
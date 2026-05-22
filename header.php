<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$current_page = basename($_SERVER['PHP_SELF']);
$is_auth_page = ($current_page === 'login.php' || $current_page === 'register.php');
$is_seller_page = ($current_page === 'seller_dashboard.php' || $current_page === 'edit-product.php');
$role = $_SESSION['role'] ?? null;
?>
<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Eunoia IA — Premium E-Commerce</title>
    <!-- Compiled Tailwind CSS -->
    <link href="css/output.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Outfit:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            -webkit-font-smoothing: antialiased;
        }
        .font-heading {
            font-family: 'Outfit', sans-serif;
        }
        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }
        ::-webkit-scrollbar-track {
            background: #f1f5f9;
        }
        ::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 4px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
    </style>
</head>
<body class="bg-secondary-50 flex flex-col min-h-screen text-secondary-800">

    <!-- Active Banner Notification -->
    <?php if ($role === 'seller'): ?>
        <div class="bg-slate-900 text-white text-xs font-medium py-2 px-4 text-center tracking-wider font-heading uppercase flex items-center justify-center gap-2">
            <span class="inline-block w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
            Seller Workspace Active &bull; <a href="seller_dashboard.php" class="underline hover:text-indigo-400 transition">Go to Dashboard</a>
        </div>
    <?php endif; ?>

    <!-- Helper variables for dynamic branding -->
    <?php
    $shop_logo_path = '';
    if (!empty($shop_settings['shop_logo']) && file_exists(__DIR__ . '/images/' . $shop_settings['shop_logo'])) {
        $shop_logo_path = 'images/' . $shop_settings['shop_logo'];
    }
    ?>

    <!-- Contextual Headers -->
    <?php if ($is_auth_page): ?>
        <!-- AUTH HEADER (Minimalist, Focused, No Clutter) -->
        <header class="bg-white/80 backdrop-blur-md border-b border-slate-100 sticky top-0 z-50">
            <div class="container mx-auto px-6 py-4 flex justify-between items-center">
                <a href="index.php" class="flex items-center gap-3 group">
                    <?php if ($shop_logo_path): ?>
                        <img src="<?php echo $shop_logo_path; ?>" alt="<?php echo htmlspecialchars($shop_settings['shop_name']); ?>" class="h-10 w-10 rounded-full object-cover shadow-md group-hover:scale-105 transition-transform duration-300">
                    <?php else: ?>
                        <div class="h-10 w-10 rounded-full bg-gradient-to-tr from-primary-600 to-indigo-600 flex items-center justify-center text-white font-heading font-black text-lg shadow-md shadow-primary-200 group-hover:scale-105 transition-transform duration-300">
                            <?php echo strtoupper(substr($shop_settings['shop_name'] ?? 'E', 0, 1)); ?>
                        </div>
                    <?php endif; ?>
                    <span class="text-xl font-heading font-black tracking-tight text-secondary-900 group-hover:text-primary-600 transition-colors uppercase">
                        <?php echo htmlspecialchars($shop_settings['shop_name'] ?? 'eunoia_IA'); ?>
                    </span>
                </a>
                <a href="index.php" class="flex items-center gap-1.5 text-sm font-medium text-slate-500 hover:text-primary-600 transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" />
                    </svg>
                    Back to Store
                </a>
            </div>
        </header>

    <?php elseif ($is_seller_page): ?>
        <!-- SELLER HEADER (Executive Portal Layout) -->
        <header class="bg-slate-900 text-white sticky top-0 z-50 shadow-lg border-b border-slate-800">
            <div class="container mx-auto px-6 py-4 flex justify-between items-center">
                <a href="seller_dashboard.php" class="flex items-center gap-3 group">
                    <?php if ($shop_logo_path): ?>
                        <img src="<?php echo $shop_logo_path; ?>" alt="<?php echo htmlspecialchars($shop_settings['shop_name']); ?>" class="h-10 w-10 rounded-full object-cover shadow-md">
                    <?php else: ?>
                        <div class="h-10 w-10 rounded-full bg-indigo-600 flex items-center justify-center text-white font-heading font-black text-lg shadow-md group-hover:bg-indigo-500 transition-colors">
                            S
                        </div>
                    <?php endif; ?>
                    <div class="flex flex-col">
                        <span class="text-lg font-heading font-black tracking-tight leading-none text-white uppercase"><?php echo htmlspecialchars($shop_settings['shop_name'] ?? 'eunoia_IA'); ?></span>
                        <span class="text-[10px] uppercase font-bold tracking-widest text-indigo-400 mt-1">Seller Dashboard</span>
                    </div>
                </a>

                <nav class="flex items-center gap-8">
                    <a href="seller_dashboard.php" class="text-sm font-medium <?php echo $current_page === 'seller_dashboard.php' ? 'text-indigo-400 font-bold' : 'text-slate-400 hover:text-white'; ?> transition-colors">Dashboard</a>
                    <a href="index.php" class="text-sm font-medium text-slate-400 hover:text-white transition-colors flex items-center gap-1">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 003 8.25v10.5A2.25 2.25 0 005.25 21h10.5A2.25 2.25 0 0018 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25" />
                        </svg>
                        View Storefront
                    </a>
                    
                    <div class="h-4 w-px bg-slate-800"></div>

                    <div class="flex items-center gap-4">
                        <div class="flex flex-col text-right hidden sm:block">
                            <span class="text-xs font-semibold text-white leading-none"><?php echo htmlspecialchars($_SESSION['username']); ?></span>
                            <span class="text-[10px] text-indigo-400 font-medium uppercase mt-0.5">Merchant Partner</span>
                        </div>
                        <a href="logout.php" class="flex items-center gap-1 bg-red-500/10 hover:bg-red-500 text-red-500 hover:text-white px-3 py-1.5 rounded-lg text-xs font-bold transition-all">
                            Logout
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-3 h-3">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75" />
                            </svg>
                        </a>
                    </div>
                </nav>
            </div>
        </header>

    <?php else: ?>
        <!-- CUSTOMER / SHOP HEADER (Boutique Storefront Layout) -->
        <header class="bg-white/90 backdrop-blur-md sticky top-0 z-50 shadow-sm border-b border-slate-100">
            <div class="container mx-auto px-6 py-4 flex justify-between items-center gap-4">
                
                <!-- Logo -->
                <a href="index.php" class="flex items-center gap-3 group shrink-0">
                    <?php if ($shop_logo_path): ?>
                        <img src="<?php echo $shop_logo_path; ?>" alt="<?php echo htmlspecialchars($shop_settings['shop_name']); ?>" class="h-10 w-10 rounded-full object-cover shadow-md group-hover:scale-105 transition-transform duration-300">
                    <?php else: ?>
                        <div class="h-10 w-10 rounded-full bg-gradient-to-tr from-primary-600 to-indigo-600 flex items-center justify-center text-white font-heading font-black text-lg shadow-md shadow-primary-200 group-hover:scale-105 transition-transform duration-300">
                            <?php echo strtoupper(substr($shop_settings['shop_name'] ?? 'E', 0, 1)); ?>
                        </div>
                    <?php endif; ?>
                    <span class="text-xl font-heading font-black tracking-tight text-secondary-900 group-hover:text-primary-600 transition-colors uppercase">
                        <?php echo htmlspecialchars($shop_settings['shop_name'] ?? 'eunoia_IA'); ?>
                    </span>
                </a>

                <!-- Custom Search Bar (Only shown on storefront index.php or catalog pages) -->
                <div class="flex-1 max-w-md mx-8 hidden md:block">
                    <?php if ($current_page === 'index.php'): ?>
                        <div class="relative">
                            <input type="text" id="searchInput" placeholder="Search products..." 
                                   class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200/80 rounded-full text-sm focus:outline-none focus:ring-2 focus:ring-primary-600 focus:bg-white transition-all duration-300">
                            <div class="absolute left-3.5 top-3 text-slate-400">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-4 h-4">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.602 10.602z" />
                                </svg>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Navigation Controls -->
                <nav class="flex items-center gap-6 shrink-0">
                    <a href="index.php" class="text-sm font-semibold text-slate-600 hover:text-primary-600 transition-colors">Catalog</a>

                    <?php if (isset($_SESSION['role'])): ?>
                        <?php if ($_SESSION['role'] === 'customer'): ?>
                            <!-- Customer specific features -->
                            <a href="orders.php" class="text-sm font-semibold text-slate-600 hover:text-primary-600 transition-colors">My Purchases</a>
                            
                            <!-- Cart Icon with badge -->
                            <a href="cart.php" id="cartIconLink" class="relative group p-2 text-slate-600 hover:text-primary-600 transition-all rounded-full hover:bg-slate-50">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-6 h-6">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 10-7.5 0v4.5m11.356-1.993l1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 01-1.12-1.243l1.264-12A1.125 1.125 0 015.513 7.5h12.974c.576 0 1.059.435 1.119 1.007zM8.625 10.5a.375.375 0 11-.75 0 .375.375 0 01.75 0zm7.5 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
                                </svg>
                                <span id="cartBadgeCount" class="absolute -top-1 -right-1 bg-primary-600 text-white font-heading font-black text-[10px] w-5 h-5 rounded-full flex items-center justify-center border-2 border-white scale-0 transition-transform duration-300">0</span>
                            </a>
                        <?php endif; ?>

                        <div class="h-4 w-px bg-slate-200"></div>

                        <!-- User Info Dropdown & Logout -->
                        <div class="flex items-center gap-4">
                            <span class="text-xs font-semibold text-slate-500 hidden sm:inline">Hi, <span class="text-slate-800 font-bold"><?php echo htmlspecialchars($_SESSION['username']); ?></span></span>
                            <a href="logout.php" class="bg-rose-50 hover:bg-rose-500 text-rose-600 hover:text-white px-3.5 py-1.5 rounded-full text-xs font-bold transition-all duration-300">
                                Logout
                            </a>
                        </div>
                    <?php else: ?>
                        <!-- Guest features -->
                        <div class="h-4 w-px bg-slate-200"></div>
                        <a href="login.php" class="text-sm font-semibold text-slate-600 hover:text-primary-600 transition-colors">Login</a>
                        <a href="register.php" class="bg-primary-600 hover:bg-primary-700 text-white px-5 py-2 rounded-full text-sm font-bold shadow-md shadow-primary-100 hover:scale-[1.02] transition-all duration-300">
                            Register
                        </a>
                    <?php endif; ?>
                </nav>
            </div>
        </header>

        <!-- Dynamic Cart Badge Update Script for Customers -->
        <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'customer'): ?>
            <script>
                function updateCartBadge() {
                    const cart = JSON.parse(localStorage.getItem('cart')) || [];
                    const badge = document.getElementById('cartBadgeCount');
                    if (badge) {
                        const totalQty = cart.reduce((sum, item) => sum + parseInt(item.qty || 1), 0);
                        if (totalQty > 0) {
                            badge.innerText = totalQty;
                            badge.classList.remove('scale-0');
                            badge.classList.add('scale-100');
                        } else {
                            badge.classList.remove('scale-100');
                            badge.classList.add('scale-0');
                        }
                    }
                }
                document.addEventListener('DOMContentLoaded', updateCartBadge);
                // Watch for changes (e.g. from script.js)
                window.addEventListener('storage', updateCartBadge);
                // Custom event trigger support
                window.addEventListener('cartUpdated', updateCartBadge);
            </script>
        <?php endif; ?>
    <?php endif; ?>

    <!-- Main Container -->
    <main class="flex-grow">
<?php
include 'config.php';
if(isset($_POST['login'])){
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Prepared Statement for Security
    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ? OR email = ?");
    $stmt->bind_param("ss", $username, $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if($row = $result->fetch_assoc()){
        if(password_verify($password, $row['password'])){
            session_start();
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['username'] = $row['username'];
            $_SESSION['role'] = $row['role'];
            
            // Redirect based on role
            if($row['role'] == 'seller') {
                header('Location: seller_dashboard.php');
            } else {
                header('Location: index.php');
            }
            exit();
        } else {
            $error = "Invalid password!";
        }
    } else {
        $error = "User not found!";
    }
}
include 'header.php';
?>

<!-- CSS Animation Keyframes for Intro Slide-In -->
<style>
    @keyframes slideFromLeft {
        0% { opacity: 0; transform: translateX(-60px) scale(0.95); }
        100% { opacity: 1; transform: translateX(0) scale(1); }
    }
    @keyframes slideFromRight {
        0% { opacity: 0; transform: translateX(60px) scale(0.95); }
        100% { opacity: 1; transform: translateX(0) scale(1); }
    }
    @keyframes slideFromBottom {
        0% { opacity: 0; transform: translateY(60px) scale(0.95); }
        100% { opacity: 1; transform: translateY(0) scale(1); }
    }
    @keyframes fadeInScale {
        0% { opacity: 0; transform: scale(0.9); }
        100% { opacity: 1; transform: scale(1); }
    }
    
    .animate-slide-left {
        animation: slideFromLeft 1.2s cubic-bezier(0.16, 1, 0.3, 1) forwards;
    }
    .animate-slide-right {
        animation: slideFromRight 1.2s cubic-bezier(0.16, 1, 0.3, 1) forwards;
    }
    .animate-slide-bottom {
        animation: slideFromBottom 1.2s cubic-bezier(0.16, 1, 0.3, 1) forwards;
    }
    .animate-fade-scale {
        animation: fadeInScale 1s cubic-bezier(0.16, 1, 0.3, 1) forwards;
    }
</style>

<div class="min-h-[80vh] flex items-center justify-center py-12 px-6">
    <div class="w-full max-w-5xl bg-white border border-slate-100 shadow-2xl rounded-3xl overflow-hidden hover:shadow-primary-100/30 transition-all duration-500 grid grid-cols-1 lg:grid-cols-12 min-h-[580px]">
        
        <!-- LEFT COLUMN: Cool Introductory Visual Showcase (Sliding Images) -->
        <div class="lg:col-span-6 bg-slate-50 p-10 flex flex-col justify-between relative overflow-hidden hidden lg:flex border-r border-slate-100/60">
            <!-- Background aesthetic highlights -->
            <div class="absolute -top-40 -left-40 w-96 h-96 bg-primary-100/40 rounded-full blur-3xl"></div>
            <div class="absolute -bottom-40 -right-40 w-96 h-96 bg-indigo-100/40 rounded-full blur-3xl"></div>
            
            <div class="relative z-10 space-y-4">
                <span class="inline-block bg-primary-50 border border-primary-100 text-primary-600 text-[10px] uppercase font-black tracking-widest px-3 py-1 rounded-full">
                    Welcome to <?php echo htmlspecialchars($shop_settings['shop_name']); ?>
                </span>
                <h3 class="text-3xl font-heading font-black text-secondary-900 tracking-tight leading-tight">
                    A clean, modern shopping <br>
                    <span class="text-primary-600">experience at your fingertips.</span>
                </h3>
                <p class="text-xs text-slate-500 max-w-sm font-light">
                    Browse curations, customize your shopping cart, and secure your purchases with GCash or Cash on Delivery.
                </p>
            </div>

            <!-- Sliding Images Showcase Grid -->
            <div class="grid grid-cols-12 gap-4 my-8 relative z-10">
                <!-- Image 1: Clothing Apparel (Slides from Left) -->
                <div class="col-span-6 bg-white p-2.5 rounded-2xl shadow-md border border-slate-100/80 animate-slide-left opacity-0" style="animation-delay: 0.1s;">
                    <div class="aspect-[4/5] rounded-xl overflow-hidden bg-slate-100">
                        <img src="https://images.unsplash.com/photo-1596755094514-f87e34085b2c?auto=format&fit=crop&q=80&w=300" class="w-full h-full object-cover">
                    </div>
                    <span class="block text-[9px] font-bold text-slate-400 mt-2 uppercase tracking-wider text-center">Apparel Curations</span>
                </div>

                <!-- Image 2: Stone Vases / Lighting (Slides from Right/Top) -->
                <div class="col-span-6 space-y-4">
                    <div class="bg-white p-2.5 rounded-2xl shadow-md border border-slate-100/80 animate-slide-right opacity-0" style="animation-delay: 0.3s;">
                        <div class="aspect-[1.1] rounded-xl overflow-hidden bg-slate-100">
                            <img src="https://images.unsplash.com/photo-1578500494198-246f612d3b3d?auto=format&fit=crop&q=80&w=300" class="w-full h-full object-cover">
                        </div>
                        <span class="block text-[9px] font-bold text-slate-400 mt-2 uppercase tracking-wider text-center">Artisanal Decor</span>
                    </div>

                    <!-- Image 3: Cozy Furniture (Slides from Bottom) -->
                    <div class="bg-white p-2.5 rounded-2xl shadow-md border border-slate-100/80 animate-slide-bottom opacity-0" style="animation-delay: 0.5s;">
                        <div class="aspect-[1.3] rounded-xl overflow-hidden bg-slate-100">
                            <img src="https://images.unsplash.com/photo-1592078615290-033ee584e267?auto=format&fit=crop&q=80&w=300" class="w-full h-full object-cover">
                        </div>
                        <span class="block text-[9px] font-bold text-slate-400 mt-2 uppercase tracking-wider text-center">Modern Furniture</span>
                    </div>
                </div>
            </div>

            <div class="relative z-10 text-[10px] text-slate-400 font-medium">
                &copy; <?php echo date('Y'); ?> <?php echo htmlspecialchars($shop_settings['shop_name']); ?>. All rights reserved.
            </div>
        </div>

        <!-- RIGHT COLUMN: Secure Login Form Card -->
        <div class="lg:col-span-6 flex flex-col justify-center p-8 md:p-12 animate-fade-scale opacity-0" style="animation-delay: 0.1s;">
            <!-- Header -->
            <div class="mb-8 space-y-2">
                <div class="flex items-center gap-2 lg:hidden">
                    <div class="h-8 w-8 rounded-full bg-primary-600 flex items-center justify-center text-white font-heading font-black text-sm">
                        E
                    </div>
                    <span class="text-md font-heading font-black tracking-tight text-secondary-900"><?php echo htmlspecialchars($shop_settings['shop_name']); ?></span>
                </div>
                <h2 class="text-3xl font-heading font-black tracking-tight text-secondary-900 leading-tight">Welcome Back</h2>
                <p class="text-xs text-slate-500">Sign in to your account to browse, curate, or manage listings.</p>
            </div>

            <!-- Seller Help Notification -->
            <div class="bg-primary-50/70 border border-primary-100 p-3.5 rounded-xl text-xs text-primary-700 leading-relaxed font-medium mb-6 flex items-start gap-2.5">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-4 h-4 text-primary-600 shrink-0 mt-0.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 111.063.852l-.708.283a.75.75 0 00-.475.68v.553m-.25-6.75h.008v.008h-.008v-.008zM12 18a.375.375 0 110-.75.375.375 0 010.75 0zM21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <div>
                    <strong>Seller Login Help:</strong> Access the pre-existing store admin dashboard using username <code class="bg-primary-100 px-1 py-0.5 rounded text-[10px] font-mono font-bold">eunoia_IA</code> and password <code class="bg-primary-100 px-1 py-0.5 rounded text-[10px] font-mono font-bold">seller123</code>.
                </div>
            </div>

            <!-- Form Body -->
            <form method="POST" class="space-y-5">
                <?php if(isset($error)): ?>
                    <div class="bg-rose-50 border-l-4 border-rose-500 text-rose-700 p-4 rounded-r-lg text-sm flex items-center gap-3">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-5 h-5 shrink-0 text-rose-500">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
                        </svg>
                        <span><?php echo $error; ?></span>
                    </div>
                <?php endif; ?>

                <!-- Username/Email -->
                <div class="space-y-1.5">
                    <label class="text-xs font-bold font-heading text-slate-500 uppercase tracking-widest block">Username or Email</label>
                    <div class="relative">
                        <input type="text" name="username" placeholder="e.g. angeljoyce" required 
                               class="w-full pl-10 pr-4 py-3 bg-slate-50 border border-slate-200/80 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary-600 focus:bg-white transition-all">
                        <div class="absolute left-3.5 top-3.5 text-slate-400">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 01-7.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Password -->
                <div class="space-y-1.5">
                    <div class="flex justify-between items-center">
                        <label class="text-xs font-bold font-heading text-slate-500 uppercase tracking-widest block">Password</label>
                        <a href="#" class="text-[11px] font-semibold text-primary-600 hover:underline">Forgot password?</a>
                    </div>
                    <div class="relative">
                        <input type="password" name="password" placeholder="••••••••" required 
                               class="w-full pl-10 pr-4 py-3 bg-slate-50 border border-slate-200/80 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary-600 focus:bg-white transition-all">
                        <div class="absolute left-3.5 top-3.5 text-slate-400">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <button name="login" class="w-full bg-primary-600 hover:bg-primary-700 text-white font-bold py-3.5 rounded-xl shadow-lg shadow-primary-100 hover:shadow-xl hover:scale-[1.01] active:scale-[0.99] transition-all duration-300">
                    Sign In
                </button>

                <!-- Redirect Link -->
                <p class="text-center text-xs text-slate-500 font-medium pt-2">
                    Don't have an account? <a href="register.php" class="text-primary-600 hover:underline font-bold">Register</a>
                </p>
            </form>
        </div>

    </div>
</div>

<?php include 'footer.php'; ?>

<?php include 'footer.php'; ?>
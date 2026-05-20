<?php
include 'config.php';
if(isset($_POST['register'])){
    $username = $_POST['username'];
    $email = $_POST['email'];
    $role = $_POST['role']; // customer or seller
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // Prepared Statement to check existence
    $stmt = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
    $stmt->bind_param("ss", $username, $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if($result->num_rows > 0){
        $error = "Username or Email already exists!";
    } else {
        $stmt = $conn->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $username, $email, $password, $role);
        $stmt->execute();
        $success = "Registration successful! You can now <a href='login.php' class='font-bold underline'>login</a>.";
    }
}
include 'header.php';
?>

<div class="min-h-[85vh] flex items-center justify-center py-16 px-6">
    <div class="w-full max-w-lg bg-white border border-slate-100 shadow-xl rounded-2xl overflow-hidden hover:shadow-2xl hover:shadow-primary-100/30 transition-all duration-500">
        <!-- Banner Header -->
        <div class="bg-gradient-to-tr from-primary-600 to-indigo-600 py-10 px-8 text-center text-white relative">
            <div class="absolute inset-0 bg-[radial-gradient(ellipse_at_top_right,_var(--tw-gradient-stops))] from-white/10 via-transparent to-transparent"></div>
            <h2 class="text-3xl font-heading font-black tracking-tight mb-2">Create Account</h2>
            <p class="text-sm text-primary-100">Join our clean, modern store to explore and purchase high-quality products.</p>
        </div>

        <!-- Form Body -->
        <form method="POST" id="registerForm" class="p-8 space-y-6">
            <?php if(isset($error)): ?>
                <div class="bg-rose-50 border-l-4 border-rose-500 text-rose-700 p-4 rounded-r-lg text-sm flex items-center gap-3">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-5 h-5 shrink-0 text-rose-500">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
                    </svg>
                    <span><?php echo $error; ?></span>
                </div>
            <?php endif; ?>

            <?php if(isset($success)): ?>
                <div class="bg-emerald-50 border-l-4 border-emerald-500 text-emerald-700 p-4 rounded-r-lg text-sm flex items-center gap-3">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-5 h-5 shrink-0 text-emerald-500">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span><?php echo $success; ?></span>
                </div>
            <?php endif; ?>

            <!-- Default Role as Customer -->
            <input type="hidden" name="role" id="roleInput" value="customer">

            <!-- Username -->
            <div class="space-y-1.5">
                <label class="text-xs font-bold font-heading text-slate-500 uppercase tracking-widest">Username</label>
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

            <!-- Email -->
            <div class="space-y-1.5">
                <label class="text-xs font-bold font-heading text-slate-500 uppercase tracking-widest">Email Address</label>
                <div class="relative">
                    <input type="email" name="email" placeholder="e.g. angeljoyce@example.com" required 
                           class="w-full pl-10 pr-4 py-3 bg-slate-50 border border-slate-200/80 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary-600 focus:bg-white transition-all">
                    <div class="absolute left-3.5 top-3.5 text-slate-400">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" />
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Password -->
            <div class="space-y-1.5">
                <label class="text-xs font-bold font-heading text-slate-500 uppercase tracking-widest">Password</label>
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

            <!-- Register Button -->
            <button name="register" class="w-full bg-primary-600 hover:bg-primary-700 text-white font-bold py-3.5 rounded-xl shadow-lg shadow-primary-100 hover:shadow-xl hover:shadow-primary-100 hover:scale-[1.01] active:scale-[0.99] transition-all duration-300">
                Register Account
            </button>

            <!-- Redirect Link -->
            <p class="text-center text-xs text-slate-500 font-medium">
                Already have an account? <a href="login.php" class="text-primary-600 hover:underline font-bold">Sign In</a>
            </p>
        </form>
    </div>
</div>

<?php include 'footer.php'; ?>
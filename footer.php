    </main>
    
    <!-- Footer Section -->
    <footer class="bg-secondary-900 text-slate-400 pt-16 pb-8 border-t border-slate-800">
        <div class="container mx-auto px-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-12 mb-12">
                
                <!-- Brand Profile -->
                <div class="space-y-4">
                    <div class="flex items-center gap-3">
                        <?php
                        $shop_logo_path = '';
                        if (!empty($shop_settings['shop_logo']) && file_exists(__DIR__ . '/images/' . $shop_settings['shop_logo'])) {
                            $shop_logo_path = 'images/' . $shop_settings['shop_logo'];
                        }
                        if ($shop_logo_path): ?>
                            <img src="<?php echo $shop_logo_path; ?>" alt="<?php echo htmlspecialchars($shop_settings['shop_name']); ?>" class="h-8 max-w-[120px] object-contain">
                        <?php else: ?>
                            <div class="h-8 w-8 rounded-full bg-indigo-600 flex items-center justify-center text-white font-heading font-black text-sm">
                                <?php echo strtoupper(substr($shop_settings['shop_name'] ?? 'E', 0, 1)); ?>
                            </div>
                            <span class="text-lg font-heading font-black tracking-tight text-white uppercase"><?php echo htmlspecialchars($shop_settings['shop_name'] ?? 'eunoia_IA'); ?></span>
                        <?php endif; ?>
                    </div>
                    <p class="text-sm text-slate-500 leading-relaxed">
                        <?php echo htmlspecialchars($shop_settings['shop_description'] ?? 'Your clean, modern, and user-friendly standard e-commerce shop.'); ?>
                    </p>
                </div>

                <!-- Category Links -->
                <div>
                    <h4 class="text-white text-xs font-bold font-heading uppercase tracking-widest mb-4">Curations</h4>
                    <ul class="space-y-2 text-sm text-slate-500">
                        <li><a href="index.php" class="hover:text-primary-600 transition-colors">Apparel</a></li>
                        <li><a href="index.php" class="hover:text-primary-600 transition-colors">Decor</a></li>
                        <li><a href="index.php" class="hover:text-primary-600 transition-colors">Lighting</a></li>
                        <li><a href="index.php" class="hover:text-primary-600 transition-colors">Furniture</a></li>
                    </ul>
                </div>

                <!-- Company Details -->
                <div>
                    <h4 class="text-white text-xs font-bold font-heading uppercase tracking-widest mb-4">Bespoke Services</h4>
                    <ul class="space-y-2 text-sm text-slate-500">
                        <li><a href="#" class="hover:text-primary-600 transition-colors">Customer Care</a></li>
                        <li><a href="#" class="hover:text-primary-600 transition-colors">Secure Checkout</a></li>
                        <li><a href="#" class="hover:text-primary-600 transition-colors">Merchant Partnership</a></li>
                        <li><a href="#" class="hover:text-primary-600 transition-colors">Simulated GCash API</a></li>
                    </ul>
                </div>

                <!-- Newsletter Dummy -->
                <div class="space-y-4">
                    <h4 class="text-white text-xs font-bold font-heading uppercase tracking-widest mb-4">Newsletter</h4>
                    <p class="text-xs text-slate-500">Subscribe to receive curation updates and editorial releases.</p>
                    <div class="flex">
                        <input type="email" placeholder="Your email address" 
                               class="bg-slate-800 border border-slate-700/60 text-white placeholder-slate-500 text-xs px-4 py-2.5 rounded-l-full focus:outline-none focus:border-primary-600 w-full transition-colors">
                        <button class="bg-primary-600 hover:bg-primary-700 text-white font-bold text-xs px-5 py-2.5 rounded-r-full transition-colors duration-300">
                            Join
                        </button>
                    </div>
                </div>

            </div>

            <!-- Border & Copyright -->
            <div class="border-t border-slate-800/60 pt-8 flex flex-col sm:flex-row justify-between items-center gap-4">
                <p class="text-xs text-slate-600">
                    &copy; <?php echo date("Y"); ?> <?php echo htmlspecialchars($shop_settings['shop_name'] ?? 'eunoia_IA'); ?>. All rights reserved.
                </p>
                <div class="flex gap-4 text-xs text-slate-600">
                    <a href="#" class="hover:text-slate-400">Privacy Policy</a>
                    <span>&bull;</span>
                    <a href="#" class="hover:text-slate-400">Terms of Use</a>
                </div>
            </div>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="js/script.js"></script>
</body>
</html>
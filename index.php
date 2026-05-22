<?php
include 'config.php';
include 'header.php';

$role = $_SESSION['role'] ?? null;
?>

<!-- Shop Container -->
<div class="container mx-auto px-6 py-8 space-y-12">

    <!-- Modern Hero Section -->
    <div class="relative rounded-3xl overflow-hidden bg-gradient-to-br from-slate-900 via-secondary-900 to-indigo-950 text-white min-h-[380px] flex items-center shadow-xl">
        <div class="absolute inset-0 bg-[radial-gradient(circle_at_bottom_left,_var(--tw-gradient-stops))] from-primary-900/50 via-transparent to-transparent"></div>
        <div class="absolute inset-0 bg-[radial-gradient(ellipse_at_top_right,_var(--tw-gradient-stops))] from-white/10 via-transparent to-transparent"></div>
        
        <div class="relative z-10 max-w-2xl px-12 py-16 space-y-6">
            <span class="inline-block bg-primary-500/10 border border-primary-500/30 text-primary-400 text-[10px] uppercase font-bold tracking-widest px-3.5 py-1 rounded-full font-heading">
                <?php echo htmlspecialchars($shop_settings['shop_name'] ?? 'eunoia_IA'); ?> Curation
            </span>
            <h1 class="text-4xl md:text-5xl font-heading font-black tracking-tight leading-tight">
                Modern Products <br>
                <span class="text-white">For Your Lifestyle.</span>
            </h1>
            <p class="text-sm text-slate-300 leading-relaxed font-normal max-w-lg">
                <?php echo htmlspecialchars($shop_settings['shop_description'] ?? 'Discover curated products selected for quality and clean modern design.'); ?>
            </p>
            <div class="flex gap-4">
                <a href="#productCatalogue" class="bg-primary-600 hover:bg-primary-500 text-white font-bold text-xs uppercase tracking-widest px-6 py-3.5 rounded-full transition-all duration-300 shadow-md shadow-primary-900/20">
                    Explore Catalogue
                </a>
            </div>
        </div>
    </div>

    <!-- Catalogue Header & Filter Pills -->
    <div id="productCatalogue" class="space-y-6 pt-6 border-t border-slate-100">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h2 class="text-2xl font-heading font-black tracking-tight text-secondary-900">Curated Catalogue</h2>
                <p class="text-xs text-slate-500">Filter through our shop categories.</p>
            </div>
            
            <!-- Category Scroll Chips -->
            <div class="flex flex-wrap gap-2.5">
                <button onclick="filterCategory('All')" 
                        class="category-btn bg-primary-600 text-white text-xs font-bold px-5 py-2.5 rounded-full shadow-md shadow-primary-100 hover:scale-[1.03] transition-all">
                    All Curations
                </button>
                <?php
                $cat_query = mysqli_query($conn, "SELECT DISTINCT category FROM products");
                while($cat = mysqli_fetch_assoc($cat_query)) {
                    $catName = htmlspecialchars($cat['category']);
                    echo '<button onclick="filterCategory(\''.addslashes($cat['category']).'\')" 
                                  class="category-btn bg-white hover:bg-slate-50 text-slate-600 border border-slate-200/60 text-xs font-semibold px-5 py-2.5 rounded-full hover:scale-[1.03] transition-all">'
                                  .$catName.
                          '</button>';
                }
                ?>
            </div>
        </div>

        <!-- Catalogue Grid -->
        <section id="productGrid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8 transition-opacity duration-300">
            <?php
            $query = mysqli_query($conn, "SELECT p.*, u.username as seller_name FROM products p JOIN users u ON p.seller_id = u.id ORDER BY p.id DESC");
            while ($row = mysqli_fetch_assoc($query)) {
                $prodId = $row['id'];
                $prodName = htmlspecialchars($row['name']);
                $prodDesc = htmlspecialchars($row['description']);
                $prodPrice = number_format($row['price'], 2);
                $prodCategory = htmlspecialchars($row['category']);
                $sellerName = htmlspecialchars($row['seller_name']);
                $imgUrl = getProductImage($row['image'], $row['category']);
                $isOwner = ($role === 'seller' && $_SESSION['user_id'] == $row['seller_id']);
            ?>
                <!-- Elegant Card -->
                <div class="card group bg-white border border-slate-100 shadow-sm hover:shadow-xl hover:-translate-y-1 rounded-2xl p-4 transition-all duration-300 flex flex-col relative overflow-hidden">
                    
                    <!-- Category Badge Tag -->
                    <span class="absolute top-6 left-6 z-10 bg-white/90 backdrop-blur-md text-[9px] font-black uppercase tracking-widest text-slate-600 px-2.5 py-1 rounded-md border border-slate-100">
                        <?php echo $prodCategory; ?>
                    </span>

                    <!-- Product Image Container -->
                    <a href="product_details.php?id=<?php echo $prodId; ?>" class="block rounded-xl overflow-hidden aspect-square bg-slate-50 border border-slate-100 relative mb-4">
                        <img src="<?php echo $imgUrl; ?>" 
                             data-product-id="<?php echo $prodId; ?>"
                             class="product-card-img w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" 
                             alt="<?php echo $prodName; ?>">
                        <div class="absolute inset-0 bg-gradient-to-t from-slate-900/10 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                    </a>

                    <!-- Title & Details -->
                    <div class="flex-grow flex flex-col justify-between space-y-2">
                        <div>
                            <a href="product_details.php?id=<?php echo $prodId; ?>" class="block">
                                <h3 class="text-md font-heading font-bold text-secondary-900 group-hover:text-primary-600 transition-colors leading-snug line-clamp-1">
                                    <?php echo $prodName; ?>
                                </h3>
                            </a>
                            <p class="text-[10px] text-slate-400 mt-0.5">
                                Boutique: <span class="font-semibold text-slate-500"><?php echo $sellerName; ?></span>
                            </p>
                            <p class="text-xs text-slate-500 mt-2 line-clamp-2 leading-relaxed">
                                <?php echo $prodDesc; ?>
                            </p>
                        </div>

                        <!-- Price and Stock Details -->
                        <div class="pt-4 mt-2 border-t border-slate-100 flex items-center justify-between">

                            <span class="text-lg font-heading font-black text-primary-600">
                                ₱<?php echo $prodPrice; ?>
                            </span>
                            <span class="text-[10px] font-bold px-2 py-0.5 rounded <?php echo $row['stock'] > 0 ? 'bg-emerald-50 text-emerald-700' : 'bg-rose-50 text-rose-700'; ?>">
                                <?php echo $row['stock'] > 0 ? 'Stock: ' . $row['stock'] : 'Out of stock'; ?>
                            </span>
                        </div>

                        <!-- Available Colors -->
                        <div class="mt-2">
                            <?php
                                $colorsRaw = $row['available_colors'] ?? '';
                                $colors = array_values(array_filter(array_map('trim', explode(',', $colorsRaw))));
                            ?>
                            <?php if (!empty($colors)): ?>
                                <div class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Available colors</div>
                                <div class="flex flex-wrap gap-2">
                                    <?php foreach ($colors as $c): ?>
                                        <span class="px-2 py-1 text-[10px] font-semibold rounded-full bg-slate-100 text-slate-700 border border-slate-200"><?php echo htmlspecialchars($c); ?></span>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>

                        </div>

                        <!-- Dynamic Action Control depending on Account Role -->
                        <div class="pt-3 w-full">
                            <?php if ($role === 'seller'): ?>
                                <?php if ($isOwner): ?>
                                    <!-- Seller owns this product -->
                                    <a href="edit-product.php?id=<?php echo $prodId; ?>" 
                                       class="block w-full text-center bg-slate-800 hover:bg-primary-600 text-white text-xs font-bold py-2.5 rounded-xl transition-all duration-300">
                                        Edit Your Listing
                                    </a>
                                <?php else: ?>
                                    <!-- Seller viewing other product -->
                                    <a href="product_details.php?id=<?php echo $prodId; ?>" 
                                       class="block w-full text-center bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold py-2.5 rounded-xl transition-all">
                                        View Product Details
                                    </a>
                                <?php endif; ?>
                            <?php else: ?>
                                <!-- Guest or Customer -->
                                <?php if ($row['stock'] > 0): ?>
                                    <div class="space-y-2">
                                        <div class="flex gap-2">
                                            <input type="number" id="qty<?php echo $prodId; ?>" value="1" min="1" max="<?php echo $row['stock']; ?>" 
                                                   class="w-14 px-1 py-2 text-center text-xs bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-1 focus:ring-primary-600">
                                            <button onclick="handleAddCart(<?php echo $prodId; ?>, '<?php echo addslashes($prodName); ?>', <?php echo $row['price']; ?>, document.getElementById('qty<?php echo $prodId; ?>').value, event)" 
                                                    class="flex-grow bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold py-2 rounded-xl active:scale-[0.99] transition-all duration-300">
                                                Add to Cart
                                            </button>
                                        </div>
                                        <button onclick="handleBuyNow(<?php echo $prodId; ?>, '<?php echo addslashes($prodName); ?>', <?php echo $row['price']; ?>, document.getElementById('qty<?php echo $prodId; ?>').value)" 
                                                class="w-full bg-primary-600 hover:bg-primary-700 text-white text-xs font-bold py-2.5 rounded-xl active:scale-[0.99] transition-all duration-300 flex items-center justify-center gap-1 shadow-sm shadow-primary-100">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-4 h-4">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                                            </svg>
                                            Buy Now
                                        </button>
                                    </div>
                                <?php else: ?>
                                    <button disabled 
                                            class="w-full bg-slate-100 text-slate-400 text-xs font-bold py-2.5 rounded-xl cursor-not-allowed">
                                        Sold Out
                                    </button>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </section>
    </div>
</div>

<script>
    function handleAddCart(id, name, price, qty, event) {
        // Fallback to login if not customer role
        <?php if (!isset($_SESSION['role'])): ?>
            window.location.href = 'login.php';
            return;
        <?php elseif ($_SESSION['role'] !== 'customer'): ?>
            showToast('Only customer accounts can buy products.', 'warning');
            return;
        <?php endif; ?>
        
        // Pass event to trigger flying animation
        if (typeof animateFlyToCart === 'function') {
            animateFlyToCart(id, event);
        }
        addToCart(id, name, price, qty);
    }

    function handleBuyNow(id, name, price, qty) {
        // Fallback to login if not customer role
        <?php if (!isset($_SESSION['role'])): ?>
            window.location.href = 'login.php';
            return;
        <?php elseif ($_SESSION['role'] !== 'customer'): ?>
            showToast('Only customer accounts can buy products.', 'warning');
            return;
        <?php endif; ?>
        
        if (typeof buyNowDirect === 'function') {
            buyNowDirect(id, name, price, qty);
        }
    }
</script>

<?php include 'footer.php'; ?>

<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'config.php';

$category = $_GET['category'] ?? 'All';
$role = $_SESSION['role'] ?? null;

$sql = "SELECT p.*, u.username as seller_name FROM products p JOIN users u ON p.seller_id = u.id";
if ($category !== 'All') {
    $sql .= " WHERE p.category = '" . mysqli_real_escape_string($conn, $category) . "'";
}
$sql .= " ORDER BY p.id DESC";

$query = mysqli_query($conn, $sql);
if(mysqli_num_rows($query) === 0) {
    echo '<div class="col-span-full text-center py-16"><h3 class="text-slate-400 font-heading font-semibold text-lg">No products found in this category.</h3></div>';
    exit;
}

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

            <!-- Dynamic Action Control depending on Account Role -->
            <div class="pt-3 w-full">
                <?php if ($role === 'seller'): ?>
                    <?php if ($isOwner): ?>
                        <a href="edit-product.php?id=<?php echo $prodId; ?>" 
                           class="block w-full text-center bg-slate-800 hover:bg-primary-600 text-white text-xs font-bold py-2.5 rounded-xl transition-all duration-300">
                            Edit Your Listing
                        </a>
                    <?php else: ?>
                        <a href="product_details.php?id=<?php echo $prodId; ?>" 
                           class="block w-full text-center bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold py-2.5 rounded-xl transition-all">
                            View Product Details
                        </a>
                    <?php endif; ?>
                <?php else: ?>
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
<?php
}
?>
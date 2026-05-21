<?php
include 'config.php';
include 'header.php';

if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$id = $_GET['id'];
$stmt = $conn->prepare("SELECT p.*, u.username as seller_name FROM products p JOIN users u ON p.seller_id = u.id WHERE p.id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$product = $stmt->get_result()->fetch_assoc();

if (!$product) {
    echo "
    <div class='min-h-[60vh] flex flex-col items-center justify-center py-20 px-6'>
        <div class='text-6xl mb-4'>🔍</div>
        <h2 class='text-3xl font-heading font-black text-secondary-900 mb-2'>Product Not Found</h2>
        <p class='text-slate-500 mb-8'>The curated piece you are looking for does not exist or has been removed.</p>
        <a href='index.php' class='bg-primary-600 hover:bg-primary-700 text-white font-bold px-8 py-3 rounded-full text-sm shadow-lg shadow-primary-100 transition-all'>
            Return to Catalogue
        </a>
    </div>";
    include 'footer.php';
    exit();
}

$prodId = $product['id'];
$prodName = htmlspecialchars($product['name']);
$prodDesc = htmlspecialchars($product['description']);
$prodPrice = number_format($product['price'], 2);
$prodCategory = htmlspecialchars($product['category']);
$sellerName = htmlspecialchars($product['seller_name']);
$imgUrl = getProductImage($product['image'], $product['category']);
$role = $_SESSION['role'] ?? null;
$isOwner = ($role === 'seller' && $_SESSION['user_id'] == $product['seller_id']);
?>

<div class="container mx-auto px-6 py-12">
    <!-- Breadcrumb -->
    <nav class="flex text-xs font-semibold text-slate-400 gap-2 mb-8 items-center">
        <a href="index.php" class="hover:text-primary-600 transition-colors">Catalog</a>
        <span>&bull;</span>
        <span class="text-slate-600"><?php echo $prodCategory; ?></span>
        <span>&bull;</span>
        <span class="text-slate-800 line-clamp-1 max-w-[200px]"><?php echo $prodName; ?></span>
    </nav>

    <!-- Split Gallery Layout -->
    <div class="bg-white border border-slate-100 shadow-xl rounded-3xl overflow-hidden flex flex-col lg:flex-row max-w-5xl mx-auto">
        <!-- Left: Image Showcase -->
        <div class="lg:w-1/2 p-6 bg-slate-50 border-r border-slate-100 flex items-center justify-center min-h-[380px] lg:min-h-[500px]">
            <div class="relative w-full h-full rounded-2xl overflow-hidden shadow-md aspect-square bg-white">
                <img src="<?php echo $imgUrl; ?>" 
                     data-product-id="<?php echo $prodId; ?>"
                     class="product-card-img w-full h-full object-cover" 
                     alt="<?php echo $prodName; ?>">
                <span class="absolute top-4 left-4 bg-white/90 backdrop-blur-md text-[10px] font-black uppercase tracking-widest text-slate-600 px-3.5 py-1.5 rounded-lg border border-slate-100">
                    <?php echo $prodCategory; ?>
                </span>
            </div>
        </div>

        <!-- Right: Information & Buy Widget -->
        <div class="lg:w-1/2 p-8 md:p-12 flex flex-col justify-between space-y-8">
            <div class="space-y-6">
                <!-- Brand Meta & Title -->
                <div class="space-y-2">
                    <div class="flex items-center gap-2">
                        <span class="bg-primary-50 text-primary-700 text-[10px] font-black tracking-widest uppercase px-3 py-1 rounded-full">
                            Curated Selection
                        </span>
                    </div>
                    <h1 class="text-3xl md:text-4xl font-heading font-black text-secondary-900 tracking-tight leading-tight">
                        <?php echo $prodName; ?>
                    </h1>
                    <div class="flex items-center gap-1.5 text-xs text-slate-500 pt-1">
                        <span>Partner Merchant:</span>
                        <span class="font-bold text-slate-700 underline"><?php echo $sellerName; ?></span>
                        <?php if ($isOwner): ?>
                            <span class="bg-slate-800 text-white text-[9px] font-bold px-2 py-0.5 rounded font-heading ml-2">Your Shop</span>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Description -->
                <div class="space-y-2">
                    <h4 class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">About this piece</h4>
                    <p class="text-slate-600 text-sm leading-relaxed">
                        <?php echo nl2br($prodDesc); ?>
                    </p>
                </div>
            </div>

            <!-- Financial Details & Actions -->
            <div class="pt-8 border-t border-slate-100 space-y-6">
                <div class="flex items-center justify-between">
                    <div class="flex flex-col">
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Price</span>
                        <span class="text-3xl font-heading font-black text-primary-600">₱<?php echo $prodPrice; ?></span>
                    </div>
                    
                    <div class="text-right">
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest block">Availability</span>
                        <?php if ($product['stock'] > 0): ?>
                            <span class="inline-block text-xs font-bold text-emerald-600 bg-emerald-50 px-3 py-1 rounded-full mt-1">
                                <?php echo $product['stock']; ?> items remaining
                            </span>
                        <?php else: ?>
                            <span class="inline-block text-xs font-bold text-rose-500 bg-rose-50 px-3 py-1 rounded-full mt-1">
                                Out of Stock
                            </span>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Interactive Buying Controls depending on Account Role -->
                <div class="w-full">
                    <?php if ($role === 'seller'): ?>
                        <?php if ($isOwner): ?>
                            <!-- Current seller's own product listing -->
                            <div class="p-4 bg-slate-50 rounded-2xl border border-slate-100 space-y-3">
                                <p class="text-xs text-slate-500 font-medium">You are logged in as the seller of this product listing. Manage its details or update stock levels below.</p>
                                <a href="edit-product.php?id=<?php echo $prodId; ?>" 
                                   class="block w-full text-center bg-slate-950 hover:bg-primary-600 text-white text-sm font-bold py-3.5 rounded-xl transition-all duration-300 shadow-md">
                                    Edit Product Listing
                                </a>
                            </div>
                        <?php else: ?>
                            <!-- Logged in as different merchant -->
                            <div class="p-4 bg-slate-50 rounded-2xl border border-slate-100 text-center">
                                <p class="text-xs text-slate-500 font-medium italic">Viewing catalog in merchant workspace. Cart and purchasing features are reserved for customers.</p>
                            </div>
                        <?php endif; ?>
                    <?php else: ?>
                        <!-- Customer or Guest -->
                        <?php if ($product['stock'] > 0): ?>
                            <div class="flex gap-4">
                                <div class="flex flex-col gap-1 shrink-0">
                                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest block">Qty</label>
                                    <input type="number" id="detailQty" value="1" min="1" max="<?php echo $product['stock']; ?>" 
                                           class="w-20 px-3 py-3.5 bg-slate-50 border border-slate-200 rounded-xl text-center font-bold text-sm focus:outline-none focus:ring-2 focus:ring-primary-600 transition-all">
                                </div>
                                <div class="flex-grow flex flex-col gap-1">
                                    <label class="text-[10px] font-bold text-transparent select-none uppercase tracking-widest block">Buy</label>
                                    <div class="flex gap-2">
                                        <button onclick="handleDetailAddCart(<?php echo $prodId; ?>, '<?php echo addslashes($prodName); ?>', <?php echo $product['price']; ?>, document.getElementById('detailQty').value, event)" 
                                                class="flex-grow bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold py-3.5 rounded-xl transition-all duration-300">
                                            Add to Cart
                                        </button>
                                        <button onclick="handleDetailBuyNow(<?php echo $prodId; ?>, '<?php echo addslashes($prodName); ?>', <?php echo $product['price']; ?>, document.getElementById('detailQty').value)" 
                                                class="flex-grow bg-primary-600 hover:bg-primary-700 text-white font-bold py-3.5 rounded-xl shadow-lg shadow-primary-100 hover:shadow-xl hover:scale-[1.01] active:scale-[0.99] transition-all duration-300">
                                            Buy Now
                                        </button>
                                    </div>
                                </div>
                            </div>
                        <?php else: ?>
                            <button disabled 
                                    class="w-full bg-slate-100 text-slate-400 font-bold py-3.5 rounded-xl cursor-not-allowed">
                                Sold Out
                            </button>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function handleDetailAddCart(id, name, price, qty, event) {
        <?php if (!isset($_SESSION['role'])): ?>
            window.location.href = 'login.php';
            return;
        <?php elseif ($_SESSION['role'] !== 'customer'): ?>
            showToast('Only customer accounts can buy products.', 'warning');
            return;
        <?php endif; ?>
        
        if (typeof animateFlyToCart === 'function') {
            animateFlyToCart(id, event);
        }
        addToCart(id, name, price, qty);
    }

    function handleDetailBuyNow(id, name, price, qty) {
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
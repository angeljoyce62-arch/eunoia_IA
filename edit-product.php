<?php
include 'config.php';
include 'header.php';

// Enforce role separation & security
if(!isset($_SESSION['role']) || $_SESSION['role'] !== 'seller'){
    header("Location: login.php");
    exit();
}

if (!isset($_GET['id'])) {
    header("Location: seller_dashboard.php");
    exit();
}

$id = $_GET['id'];
$seller_id = $_SESSION['user_id'];

// Secure fetch: Only fetch if it belongs to this seller!
$stmt = $conn->prepare("SELECT * FROM products WHERE id = ? AND seller_id = ?");
$stmt->bind_param("ii", $id, $seller_id);
$stmt->execute();
$product = $stmt->get_result()->fetch_assoc();

if (!$product) {
    echo "
    <div class='container mx-auto px-6 py-20 flex flex-col items-center justify-center min-h-[60vh]'>
        <div class='bg-white p-12 border border-slate-100 rounded-3xl shadow-xl max-w-lg w-full text-center space-y-6 hover:shadow-2xl transition-all duration-300'>
            <div class='text-6xl text-rose-500'>⚠️</div>
            <h1 class='text-2xl font-heading font-black text-secondary-900 uppercase tracking-tight'>Listing Blocked</h1>
            <p class='text-sm text-slate-500 leading-relaxed'>
                The curation you are trying to edit does not exist or does not belong to your merchant account profile. Cross-merchant operations are strictly blocked.
            </p>
            <div class='pt-4'>
                <a href='seller_dashboard.php' class='inline-block w-full bg-slate-900 hover:bg-luxury-gold text-white hover:text-slate-900 font-bold py-3.5 rounded-xl transition-all duration-300 shadow-md'>
                    Return to Dashboard
                </a>
            </div>
        </div>
    </div>";
    include 'footer.php';
    exit();
}

// Update Logic
if(isset($_POST['update'])){
    $name = $_POST['name'];
    $price = $_POST['price'];
    $stock = $_POST['stock'];
    $category = $_POST['category'];
    $description = $_POST['description'];
    
    // Check if new image is uploaded
    if (!empty($_FILES['image']['name'])) {
        $image = $_FILES['image']['name'];
        $tmp = $_FILES['image']['tmp_name'];
        
        if (move_uploaded_file($tmp, "images/".$image)) {
            $stmt_update = $conn->prepare("UPDATE products SET name = ?, price = ?, stock = ?, category = ?, description = ?, image = ? WHERE id = ? AND seller_id = ?");
            $stmt_update->bind_param("sdisssii", $name, $price, $stock, $category, $description, $image, $id, $seller_id);
            $stmt_update->execute();
            $msg = "Curation listed details & image updated successfully!";
        } else {
            $msg_err = "Failed to upload new product image.";
        }
    } else {
        // Update details without changing image
        $stmt_update = $conn->prepare("UPDATE products SET name = ?, price = ?, stock = ?, category = ?, description = ? WHERE id = ? AND seller_id = ?");
        $stmt_update->bind_param("sdissii", $name, $price, $stock, $category, $description, $id, $seller_id);
        $stmt_update->execute();
        $msg = "Curation listed details updated successfully!";
    }

    if (isset($stmt_update) && $stmt_update->affected_rows >= 0) {
        // Refresh product info
        $stmt = $conn->prepare("SELECT * FROM products WHERE id = ? AND seller_id = ?");
        $stmt->bind_param("ii", $id, $seller_id);
        $stmt->execute();
        $product = $stmt->get_result()->fetch_assoc();
    }
}
?>

<div class="container mx-auto px-6 py-12 max-w-2xl">
    
    <!-- Breadcrumb back link -->
    <a href="seller_dashboard.php?tab=inventory" class="text-xs font-bold text-slate-400 hover:text-primary-600 transition flex items-center gap-1.5 mb-8">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-4 h-4">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" />
        </svg>
        Back to Curation Inventory
    </a>

    <!-- Editor card -->
    <div class="bg-white border border-slate-100 shadow-xl rounded-3xl p-8 md:p-10 relative">
        <div class="mb-8 border-b border-slate-100 pb-4">
            <h1 class="text-2xl font-heading font-black text-secondary-900 tracking-tight">Edit Curation Listing</h1>
            <p class="text-xs text-slate-400 mt-1">Modify details for listing reference ID #<?php echo str_pad($id, 4, '0', STR_PAD_LEFT); ?>.</p>
        </div>

        <!-- Alert messages -->
        <?php if(isset($msg)): ?>
            <div class="bg-emerald-50 border-l-4 border-emerald-500 text-emerald-700 p-4 rounded-r-lg text-xs font-semibold mb-6 flex items-center justify-between">
                <span><?php echo $msg; ?></span>
                <a href="seller_dashboard.php" class="underline">Dashboard &rarr;</a>
            </div>
        <?php endif; ?>
        <?php if(isset($msg_err)): ?>
            <div class="bg-rose-50 border-l-4 border-rose-500 text-rose-700 p-4 rounded-r-lg text-xs font-semibold mb-6">
                <?php echo $msg_err; ?>
            </div>
        <?php endif; ?>

        <!-- Form -->
        <form method="POST" enctype="multipart/form-data" class="space-y-6">
            
            <!-- Thumbnail View -->
            <div class="flex items-center gap-4 bg-slate-50 p-4 rounded-2xl border border-slate-100/50">
                <div class="w-16 h-16 bg-white border border-slate-100 rounded-xl overflow-hidden shrink-0">
                    <img src="<?php echo getProductImage($product['image'], $product['category']); ?>" class="w-full h-full object-cover">
                </div>
                <div>
                    <h5 class="text-xs font-bold text-slate-400 uppercase tracking-widest leading-none">Currently listed thumbnail</h5>
                    <p class="text-xs text-slate-500 mt-1.5 leading-relaxed"><?php echo htmlspecialchars($product['image'] ?: 'Default fallback Unsplash image active.'); ?></p>
                </div>
            </div>

            <!-- Name -->
            <div class="space-y-1.5">
                <label class="text-xs font-bold font-heading text-slate-500 uppercase tracking-widest block">Product Name</label>
                <input type="text" name="name" value="<?php echo htmlspecialchars($product['name']); ?>" required 
                       class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary-600 focus:bg-white transition-all">
            </div>

            <!-- Price & Stock Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Price -->
                <div class="space-y-1.5">
                    <label class="text-xs font-bold font-heading text-slate-500 uppercase tracking-widest block">Price (₱)</label>
                    <input type="number" step="0.01" name="price" value="<?php echo $product['price']; ?>" required 
                           class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary-600 focus:bg-white transition-all">
                </div>

                <!-- Stock -->
                <div class="space-y-1.5">
                    <label class="text-xs font-bold font-heading text-slate-500 uppercase tracking-widest block">Stock Quantity</label>
                    <input type="number" name="stock" value="<?php echo $product['stock']; ?>" required 
                           class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary-600 focus:bg-white transition-all">
                </div>
            </div>

            <!-- Category -->
            <div class="space-y-1.5">
                <label class="text-xs font-bold font-heading text-slate-500 uppercase tracking-widest block">Bespoke Category</label>
                <select name="category" required 
                        class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary-600 focus:bg-white transition-all">
                    <option value="Furniture" <?php echo $product['category'] === 'Furniture' ? 'selected' : ''; ?>>Furniture Selection</option>
                    <option value="Apparel" <?php echo $product['category'] === 'Apparel' ? 'selected' : ''; ?>>Luxury Apparel</option>
                    <option value="Decor" <?php echo $product['category'] === 'Decor' ? 'selected' : ''; ?>>Artisanal Decor</option>
                    <option value="Lighting" <?php echo $product['category'] === 'Lighting' ? 'selected' : ''; ?>>Ambient Lighting</option>
                    <option value="General" <?php echo $product['category'] === 'General' ? 'selected' : ''; ?>>General Curations</option>
                </select>
            </div>

            <!-- Description -->
            <div class="space-y-1.5">
                <label class="text-xs font-bold font-heading text-slate-500 uppercase tracking-widest block">Curation Narrative Description</label>
                <textarea name="description" required 
                          class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary-600 focus:bg-white transition-all h-36 resize-none"><?php echo htmlspecialchars($product['description']); ?></textarea>
            </div>

            <!-- Image Upload (Optional update) -->
            <div class="space-y-1.5">
                <label class="text-xs font-bold font-heading text-slate-500 uppercase tracking-widest block">Update Image (Optional)</label>
                <input type="file" name="image" 
                       class="w-full text-xs text-slate-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100 cursor-pointer">
                <p class="text-[10px] text-slate-400 italic">Leave empty to preserve the current listing image.</p>
            </div>

            <!-- Submit buttons -->
            <div class="pt-4 flex gap-4">
                <a href="seller_dashboard.php?tab=inventory" class="flex-1 text-center border border-slate-200 hover:bg-slate-50 text-slate-600 font-bold py-3.5 rounded-xl text-sm transition-all duration-300">
                    Cancel Edits
                </a>
                <button name="update" class="flex-grow bg-primary-600 hover:bg-primary-700 text-white font-bold py-3.5 rounded-xl shadow-lg shadow-primary-100 hover:shadow-xl hover:scale-[1.01] active:scale-[0.99] transition-all duration-300">
                    Save Changes
                </button>
            </div>

        </form>
    </div>
</div>

<?php include 'footer.php'; ?>
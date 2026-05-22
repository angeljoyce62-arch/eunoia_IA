<?php
include 'config.php';
include 'header.php';

// Enforce role separation
if(!isset($_SESSION['role']) || $_SESSION['role'] !== 'seller'){
    header("Location: login.php");
    exit();
}

$seller_id = $_SESSION['user_id'];
$active_tab = $_GET['tab'] ?? 'inventory'; // inventory, orders, add_product, settings

// Update Store Settings Logic
if (isset($_POST['update_settings'])) {
    $shop_name = $_POST['shop_name'];
    $shop_description = $_POST['shop_description'];
    $shop_phone = $_POST['shop_phone'];
    $shop_email = $_POST['shop_email'];
    $logo_name = $shop_settings['shop_logo']; // default to existing
    
    if (isset($_FILES['shop_logo']['name']) && !empty($_FILES['shop_logo']['name'])) {
        $logo_name = $_FILES['shop_logo']['name'];
        $tmp = $_FILES['shop_logo']['tmp_name'];
        
        if (!is_dir("images")) {
            mkdir("images", 0777, true);
        }
        
        if (move_uploaded_file($tmp, "images/" . $logo_name)) {
            // success
        } else {
            $msg_err = "Failed to upload store logo image.";
        }
    }
    
    // Save to DB
    $stmt_update = $conn->prepare("UPDATE shop_settings SET shop_name = ?, shop_description = ?, shop_phone = ?, shop_email = ?, shop_logo = ? WHERE id = 1");
    $stmt_update->bind_param("sssss", $shop_name, $shop_description, $shop_phone, $shop_email, $logo_name);
    if ($stmt_update->execute()) {
        $msg = "Store settings updated successfully!";
        // Refresh local $shop_settings variable so that changes are reflected in header immediately!
        $shop_query = mysqli_query($conn, "SELECT * FROM shop_settings WHERE id = 1");
        if ($shop_query && mysqli_num_rows($shop_query) > 0) {
            $shop_settings = mysqli_fetch_assoc($shop_query);
        }
    } else {
        $msg_err = "Failed to update store settings in database.";
    }
}


// Add Product Logic
if(isset($_POST['add'])){
    $name = trim($_POST['name']);
    $price = $_POST['price'];
    $description = trim($_POST['description']);
    $stock = intval($_POST['stock']);
    $category = $_POST['category'];
    $available_colors = trim($_POST['available_colors'] ?? '');

    $image = basename($_FILES['image']['name']);
    $tmp = $_FILES['image']['tmp_name'];
    $upload_error = $_FILES['image']['error'] ?? UPLOAD_ERR_NO_FILE;

    // Ensure images directory exists
    if (!is_dir("images")) {
        mkdir("images", 0777, true);
    }

    if (!empty($image) && $upload_error === UPLOAD_ERR_OK && is_uploaded_file($tmp)) {
        if (move_uploaded_file($tmp, "images/" . $image)) {
            $stmt = $conn->prepare("INSERT INTO products (name, price, image, description, stock, category, available_colors, seller_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("sdssissi", $name, $price, $image, $description, $stock, $category, $available_colors, $seller_id);

            if ($stmt->execute()) {
                $msg = "Luxury curation listed successfully!";
                $active_tab = 'inventory';
            } else {
                $msg_err = "Failed to save product listing. Please try again.";
            }
        } else {
            $msg_err = "Failed to upload curation image.";
        }
    } else {
        $msg_err = "Please choose a valid image before publishing your product.";
    }
}

// Deletion Logic (Security: Only delete if product belongs to this seller)
if(isset($_GET['delete'])){
    $prod_id = intval($_GET['delete']);
    // Check if product belongs to this seller
    $stmt_check = $conn->prepare("SELECT id FROM products WHERE id = ? AND seller_id = ?");
    $stmt_check->bind_param("ii", $prod_id, $seller_id);
    $stmt_check->execute();
    $result_check = $stmt_check->get_result();
    if ($result_check->num_rows === 0) {
        $msg_err = "Product not found or does not belong to your account.";
    } else {
        $conn->begin_transaction();
        try {
            // Delete related order items
            $stmt1 = $conn->prepare("DELETE FROM order_items WHERE product_id = ?");
            $stmt1->bind_param("i", $prod_id);
            $stmt1->execute();

            // Delete actual product
            $stmt2 = $conn->prepare("DELETE FROM products WHERE id = ? AND seller_id = ?");
            $stmt2->bind_param("ii", $prod_id, $seller_id);
            $stmt2->execute();

            if ($stmt2->affected_rows > 0) {
                $conn->commit();
                header("Location: seller_dashboard.php?tab=inventory&msg=deleted");
                exit();
            } else {
                $conn->rollback();
                $msg_err = "Failed to delete listing. It may be associated with active orders or already deleted.";
            }
        } catch (Exception $e) {
            $conn->rollback();
            $msg_err = "Failed to delete listing. It is associated with active customer orders.";
        }
    }
}

// Fetch Advanced Metrics
// Metrics 1 & 2: Active listings and total units
$stats_query = mysqli_query($conn, "SELECT COUNT(*) as total, SUM(stock) as total_stock FROM products WHERE seller_id = $seller_id");
$stats = mysqli_fetch_assoc($stats_query);

// Metrics 3: Real Calculated Business Revenue
$rev_stmt = $conn->prepare("
    SELECT SUM(oi.price * oi.quantity) as total_revenue 
    FROM order_items oi 
    JOIN products p ON oi.product_id = p.id 
    JOIN orders o ON oi.order_id = o.id 
    WHERE p.seller_id = ? AND o.status != 'Cancelled'
");
$rev_stmt->bind_param("i", $seller_id);
$rev_stmt->execute();
$rev_res = $rev_stmt->get_result()->fetch_assoc();
$total_revenue = $rev_res['total_revenue'] ?? 0.00;
?>

<div class="container mx-auto px-6 py-12">
    
    <!-- Dashboard Header -->
    <div class="mb-10 border-b border-slate-100 pb-6 flex flex-col md:flex-row justify-between items-start md:items-end gap-4">
        <div>
            <h1 class="text-3xl font-heading font-black text-secondary-900 tracking-tight">Business Operations</h1>
                <p class="text-xs text-slate-500">Oversee your boutique products catalog, stock, and monitor sales metrics.</p>
        </div>
        <div class="bg-luxury-gold/10 border border-luxury-gold/25 text-luxury-gold px-4 py-2 rounded-xl text-xs font-bold font-heading uppercase tracking-wider">
            Verified Merchant Workspace
        </div>
    </div>

    <!-- Executive KPI Blocks -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-12">
        <!-- Revenue Card -->
        <div class="bg-white border border-slate-100 p-6 rounded-2xl shadow-sm flex items-center gap-5 hover:shadow-md transition-shadow">
            <div class="h-12 w-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl shrink-0">
                ₱
            </div>
            <div>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest leading-none mb-1">Simulated Revenue</p>
                <p class="text-2xl font-heading font-black text-secondary-900">₱<?php echo number_format($total_revenue, 2); ?></p>
            </div>
        </div>

        <!-- Catalog items Count -->
        <div class="bg-white border border-slate-100 p-6 rounded-2xl shadow-sm flex items-center gap-5 hover:shadow-md transition-shadow">
            <div class="h-12 w-12 rounded-xl bg-primary-50 text-primary-600 flex items-center justify-center text-xl shrink-0">
                📦
            </div>
            <div>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest leading-none mb-1">Active Product Listings</p>
                <p class="text-2xl font-heading font-black text-secondary-900"><?php echo $stats['total'] ?? 0; ?> items</p>
            </div>
        </div>

        <!-- Inventory Volume -->
        <div class="bg-white border border-slate-100 p-6 rounded-2xl shadow-sm flex items-center gap-5 hover:shadow-md transition-shadow">
            <div class="h-12 w-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-xl shrink-0">
                📊
            </div>
            <div>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest leading-none mb-1">Total Stock Volume</p>
                <p class="text-2xl font-heading font-black text-secondary-900"><?php echo $stats['total_stock'] ?? 0; ?> units</p>
            </div>
        </div>
    </div>

    <!-- Interface Tabs -->
    <div class="flex border-b border-slate-100 gap-6 mb-8 text-sm font-semibold">
        <a href="seller_dashboard.php?tab=inventory" 
           class="pb-4 relative transition-all duration-200 <?php echo $active_tab === 'inventory' ? 'text-primary-600 font-bold border-b-2 border-primary-600' : 'text-slate-400 hover:text-slate-600'; ?>">
            Active Listings
        </a>
        <a href="seller_dashboard.php?tab=orders" 
           class="pb-4 relative transition-all duration-200 <?php echo $active_tab === 'orders' ? 'text-primary-600 font-bold border-b-2 border-primary-600' : 'text-slate-400 hover:text-slate-600'; ?>">
            Customer Orders Received
        </a>
        <a href="seller_dashboard.php?tab=add_product" 
           class="pb-4 relative transition-all duration-200 <?php echo $active_tab === 'add_product' ? 'text-primary-600 font-bold border-b-2 border-primary-600' : 'text-slate-400 hover:text-slate-600'; ?>">
            List New Product
        </a>
        <a href="seller_dashboard.php?tab=settings" 
           class="pb-4 relative transition-all duration-200 <?php echo $active_tab === 'settings' ? 'text-primary-600 font-bold border-b-2 border-primary-600' : 'text-slate-400 hover:text-slate-600'; ?>">
            Store Settings
        </a>
    </div>

    <!-- System Messages -->
    <?php if(isset($msg)): ?>
        <div class="bg-emerald-50 border-l-4 border-emerald-500 text-emerald-700 p-4 rounded-r-lg text-xs font-semibold mb-6">
            <?php echo $msg; ?>
        </div>
    <?php endif; ?>
    <?php if(isset($msg_err)): ?>
        <div class="bg-rose-50 border-l-4 border-rose-500 text-rose-700 p-4 rounded-r-lg text-xs font-semibold mb-6">
            <?php echo $msg_err; ?>
        </div>
    <?php endif; ?>

    <!-- Contextual Tab Screens -->
    <?php if ($active_tab === 'inventory'): ?>
        <!-- INVENTORY SCREEN -->
        <div class="bg-white border border-slate-100 shadow-lg rounded-3xl overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-100 text-slate-500 text-[10px] font-black uppercase tracking-widest">
                            <th class="px-6 py-4">Curation Details</th>
                            <th class="px-6 py-4">Bespoke Category</th>
                            <th class="px-6 py-4 text-center">Available Stock</th>
                            <th class="px-6 py-4">Unit price</th>
                            <th class="px-6 py-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-xs">
                        <?php
                        $stmt = $conn->prepare("SELECT * FROM products WHERE seller_id = ? ORDER BY id DESC");
                        $stmt->bind_param("i", $seller_id);
                        $stmt->execute();
                        $res = $stmt->get_result();
                        
                        if ($res->num_rows === 0) {
                            echo '<tr><td colspan="5" class="px-6 py-12 text-center text-slate-400 font-medium italic">No active products listed. Click "List New Product" to start.</td></tr>';
                        } else {
                            while($row = $res->fetch_assoc()){
                                $prodId = $row['id'];
                                $prodName = htmlspecialchars($row['name']);
                                $prodCategory = htmlspecialchars($row['category']);
                                $prodStock = $row['stock'];
                                $prodPrice = number_format($row['price'], 2);
                                $imgUrl = getProductImage($row['image'], $row['category']);
                        ?>
                            <tr class="hover:bg-slate-50/50 transition">
                                <!-- Product Card Summary -->
                                <td class="px-6 py-4 flex items-center gap-4">
                                    <div class="w-12 h-12 bg-slate-50 border border-slate-100 rounded-xl overflow-hidden shrink-0">
                                        <img src="<?php echo $imgUrl; ?>" class="w-full h-full object-cover">
                                    </div>
                                    <div class="min-w-0">
                                        <a href="product_details.php?id=<?php echo $prodId; ?>" class="font-heading font-bold text-secondary-900 hover:text-primary-600 line-clamp-1 truncate transition-colors"><?php echo $prodName; ?></a>
                                        <p class="text-[10px] text-slate-400 mt-0.5">ID: #<?php echo str_pad($prodId, 4, '0', STR_PAD_LEFT); ?></p>
                                    </div>
                                </td>
                                
                                <!-- Category -->
                                <td class="px-6 py-4 font-semibold text-slate-500">
                                    <?php echo $prodCategory; ?>
                                </td>

                                <!-- Stock Gauge -->
                                <td class="px-6 py-4 text-center">
                                    <span class="inline-block px-2.5 py-1 rounded text-[10px] font-bold <?php echo $prodStock < 5 ? 'bg-rose-50 text-rose-600 border border-rose-100' : 'bg-emerald-50 text-emerald-600 border border-emerald-100'; ?>">
                                        <?php echo $prodStock; ?> units
                                    </span>
                                </td>

                                <!-- Price -->
                                <td class="px-6 py-4 font-heading font-black text-secondary-900 text-sm">
                                    ₱<?php echo $prodPrice; ?>
                                </td>

                                <!-- Action buttons -->
                                <td class="px-6 py-4 text-right">
                                    <div class="flex justify-end gap-3.5">
                                        <a href="edit-product.php?id=<?php echo $prodId; ?>" class="text-primary-600 hover:text-primary-700 font-bold">Edit Details</a>
                                        <span class="text-slate-200">|</span>
                                        <a href="seller_dashboard.php?delete=<?php echo $prodId; ?>" 
                                           onclick="return confirm('Are you sure you want to permanently remove this listing?')" 
                                           class="text-rose-500 hover:text-rose-700 font-bold">Delete</a>
                                    </div>
                                </td>
                            </tr>
                        <?php 
                            }
                        } 
                        ?>
                    </tbody>
                </table>
            </div>
        </div>

    <?php elseif ($active_tab === 'orders'): ?>
        <!-- INCOMING CUSTOMER ORDERS RECEIVED SCREEN -->
        <div class="bg-white border border-slate-100 shadow-lg rounded-3xl overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-100 text-slate-500 text-[10px] font-black uppercase tracking-widest">
                            <th class="px-6 py-4">Receipt #</th>
                            <th class="px-6 py-4">Customer Detail</th>
                            <th class="px-6 py-4">Item Sold</th>
                            <th class="px-6 py-4 text-center">Units Sold</th>
                            <th class="px-6 py-4">Gross Revenue</th>
                            <th class="px-6 py-4 text-right">Sale Date</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-xs">
                        <?php
                        $order_query = "
                            SELECT o.id as order_id, o.status, o.created_at, o.customer_name, u.username, oi.quantity, oi.price as unit_price, p.name as prod_name 
                            FROM orders o 
                            JOIN order_items oi ON o.id = oi.order_id 
                            JOIN products p ON oi.product_id = p.id 
                            JOIN users u ON o.user_id = u.id 
                            WHERE p.seller_id = ? 
                            ORDER BY o.created_at DESC
                        ";
                        $stmt_orders = $conn->prepare($order_query);
                        $stmt_orders->bind_param("i", $seller_id);
                        $stmt_orders->execute();
                        $res_orders = $stmt_orders->get_result();
                        
                        if ($res_orders->num_rows === 0) {
                            echo '<tr><td colspan="6" class="px-6 py-12 text-center text-slate-400 font-medium italic">No customer orders containing your products have been placed yet.</td></tr>';
                        } else {
                            while($order = $res_orders->fetch_assoc()){
                                $orderId = $order['order_id'];
                                $custName = htmlspecialchars($order['customer_name'] ?: $order['username']);
                                $prodName = htmlspecialchars($order['prod_name']);
                                $qty = $order['quantity'];
                                $subtotal = number_format($order['unit_price'] * $qty, 2);
                                $date = date('M d, Y', strtotime($order['created_at']));
                        ?>
                            <tr class="hover:bg-slate-50/50 transition">
                                <td class="px-6 py-4 font-mono font-bold text-secondary-900">#<?php echo str_pad($orderId, 6, '0', STR_PAD_LEFT); ?></td>
                                <td class="px-6 py-4 font-semibold text-slate-700"><?php echo $custName; ?></td>
                                <td class="px-6 py-4 font-medium text-slate-800"><?php echo $prodName; ?></td>
                                <td class="px-6 py-4 text-center font-bold text-slate-600"><?php echo $qty; ?> units</td>
                                <td class="px-6 py-4 font-heading font-black text-emerald-600">₱<?php echo $subtotal; ?></td>
                                <td class="px-6 py-4 text-right text-slate-400 font-medium"><?php echo $date; ?></td>
                            </tr>
                        <?php 
                            }
                        } 
                        ?>
                    </tbody>
                </table>
            </div>
        </div>

    <?php elseif ($active_tab === 'add_product'): ?>
        <!-- LIST NEW PRODUCT FORM SCREEN -->
        <div class="bg-white border border-slate-100 shadow-xl rounded-3xl p-8 max-w-2xl mx-auto">
            <div class="mb-6 border-b border-slate-100 pb-4">
                <h3 class="text-lg font-heading font-black text-secondary-900">List New Product</h3>
                <p class="text-xs text-slate-400">Add detailed specs and high-resolution thumbnail upload to showcase your product.</p>
            </div>
            
            <form method="POST" enctype="multipart/form-data" class="space-y-6">
                <!-- Name -->
                <div class="space-y-1.5">
                    <label class="text-xs font-bold font-heading text-slate-500 uppercase tracking-widest block">Product Name</label>
                    <input type="text" name="name" placeholder="e.g. Stoneware Ceramic Vase" required 
                           class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary-600 focus:bg-white transition-all">
                </div>

                <!-- Price & Stock Grid -->
                <div class="grid grid-cols-2 gap-4">
                    <!-- Price -->
                    <div class="space-y-1.5">
                        <label class="text-xs font-bold font-heading text-slate-500 uppercase tracking-widest block">Price (₱)</label>
                        <input type="number" step="0.01" name="price" placeholder="e.g. 1950.00" required 
                               class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary-600 focus:bg-white transition-all">
                    </div>

                    <!-- Stock -->
                    <div class="space-y-1.5">
                        <label class="text-xs font-bold font-heading text-slate-500 uppercase tracking-widest block">Stock Quantity</label>
                        <input type="number" name="stock" placeholder="e.g. 15" required 
                               class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary-600 focus:bg-white transition-all">
                    </div>
                </div>

                <!-- Category -->
                <div class="space-y-1.5">
                    <label class="text-xs font-bold font-heading text-slate-500 uppercase tracking-widest block">Bespoke Category</label>
                    <select name="category" required 
                            class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary-600 focus:bg-white transition-all">
<option value="Furniture">Type Selection</option>
                    <option value="Apparel">Tops</option>
                    <option value="Decor">T-shirts</option>
                    <option value="Lighting">Jeans</option>
                    <option value="General">Perfume</option>
                    <option value="General">Sandals</option>
                    </select>
                </div>

                <!-- Colors -->
                <div class="space-y-1.5">
                    <label class="text-xs font-bold font-heading text-slate-500 uppercase tracking-widest block">Available Colors (comma-separated)</label>
                    <input type="text" name="available_colors" placeholder="e.g. Red,Blue,Black" 
                           class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary-600 focus:bg-white transition-all">
                    <p class="text-[10px] text-slate-400 italic">Enter colors separated by commas.</p>
                </div>

                <!-- Description -->
                <div class="space-y-1.5">
                    <label class="text-xs font-bold font-heading text-slate-500 uppercase tracking-widest block">Curation Narrative Description</label>
                    <textarea name="description" placeholder="Describe the crafting material, dimensions, inspiration..." required 
                              class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary-600 focus:bg-white transition-all h-32 resize-none"></textarea>
                </div>


                <!-- Image Upload -->
                <div class="space-y-1.5">
                    <label class="text-xs font-bold font-heading text-slate-500 uppercase tracking-widest block">High-Resolution Image</label>
                    <input type="file" name="image" required 
                           class="w-full text-xs text-slate-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100 cursor-pointer">
                </div>

                <!-- Submit Listing -->
                <button name="add" class="w-full bg-primary-600 hover:bg-primary-700 text-white font-bold py-3.5 rounded-xl shadow-lg shadow-primary-100 hover:shadow-xl hover:scale-[1.01] active:scale-[0.99] transition-all duration-300">
                    Publish Product Listing
                </button>
            </form>
        </div>
    <?php elseif ($active_tab === 'settings'): ?>
        <!-- STORE SETTINGS SCREEN -->
        <div class="bg-white border border-slate-100 shadow-xl rounded-3xl p-8 max-w-2xl mx-auto">
            <div class="mb-6 border-b border-slate-100 pb-4">
                <h3 class="text-lg font-heading font-black text-secondary-900 font-bold">Store Profile Settings</h3>
                <p class="text-xs text-slate-400">Configure your public store details, logo, description, and contact info.</p>
            </div>
            
            <form method="POST" enctype="multipart/form-data" class="space-y-6">
                <!-- Current Logo Preview if exists -->
                <div class="flex items-center gap-5 bg-slate-50 border border-slate-100 p-4 rounded-2xl">
                    <div class="h-16 w-16 bg-white border border-slate-200 rounded-2xl overflow-hidden flex items-center justify-center shrink-0">
                        <?php if (!empty($shop_settings['shop_logo']) && file_exists('images/' . $shop_settings['shop_logo'])): ?>
                            <img src="images/<?php echo htmlspecialchars($shop_settings['shop_logo']); ?>" class="h-full w-full object-cover">
                        <?php else: ?>
                            <span class="text-xl">🏬</span>
                        <?php endif; ?>
                    </div>
                    <div>
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest block mb-0.5">Current Store Brand</span>
                        <h4 class="font-heading font-black text-secondary-900 text-sm"><?php echo htmlspecialchars($shop_settings['shop_name']); ?></h4>
                        <p class="text-[10px] text-slate-400"><?php echo !empty($shop_settings['shop_logo']) ? htmlspecialchars($shop_settings['shop_logo']) : 'Using default text icon logo'; ?></p>
                    </div>
                </div>

                <!-- Shop Name -->
                <div class="space-y-1.5">
                    <label class="text-xs font-bold font-heading text-slate-500 uppercase tracking-widest block">Shop Name</label>
                    <input type="text" name="shop_name" value="<?php echo htmlspecialchars($shop_settings['shop_name']); ?>" required 
                           class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary-600 focus:bg-white transition-all">
                </div>

                <!-- Grid (Phone & Email) -->
                <div class="grid grid-cols-2 gap-4">
                    <!-- Contact Phone -->
                    <div class="space-y-1.5">
                        <label class="text-xs font-bold font-heading text-slate-500 uppercase tracking-widest block">Contact Phone Number</label>
                        <input type="text" name="shop_phone" value="<?php echo htmlspecialchars($shop_settings['shop_phone']); ?>" required 
                               class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary-600 focus:bg-white transition-all">
                    </div>

                    <!-- Contact Email -->
                    <div class="space-y-1.5">
                        <label class="text-xs font-bold font-heading text-slate-500 uppercase tracking-widest block">Contact Email Address</label>
                        <input type="email" name="shop_email" value="<?php echo htmlspecialchars($shop_settings['shop_email']); ?>" required 
                               class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary-600 focus:bg-white transition-all">
                    </div>
                </div>

                <!-- Shop Description -->
                <div class="space-y-1.5">
                    <label class="text-xs font-bold font-heading text-slate-500 uppercase tracking-widest block">Store Description Narrative</label>
                    <textarea name="shop_description" required 
                              class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary-600 focus:bg-white transition-all h-28 resize-none"><?php echo htmlspecialchars($shop_settings['shop_description']); ?></textarea>
                </div>

                <!-- Logo Upload -->
                <div class="space-y-1.5">
                    <label class="text-xs font-bold font-heading text-slate-500 uppercase tracking-widest block">Upload New Logo Image (Optional)</label>
                    <input type="file" name="shop_logo" 
                           class="w-full text-xs text-slate-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100 cursor-pointer">
                    <p class="text-[10px] text-slate-400 italic">Recommended format: Square png/jpg. Leave blank to retain current logo.</p>
                </div>

                <!-- Submit Settings -->
                <button name="update_settings" class="w-full bg-primary-600 hover:bg-primary-700 text-white font-bold py-3.5 rounded-xl shadow-lg shadow-primary-100 hover:shadow-xl hover:scale-[1.01] active:scale-[0.99] transition-all duration-300">
                    Save Store Settings & Updates
                </button>
            </form>
        </div>
    <?php endif; ?>

</div>

<?php include 'footer.php'; ?>
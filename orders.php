<?php
include 'config.php';
include 'header.php';

// Enforce role separation
if (!isset($_SESSION['role'])) {
    header("Location: login.php");
    exit();
}

$role = $_SESSION['role'];
if ($role !== 'customer') {
    header("Location: seller_dashboard.php");
    exit();
}

$user_id = $_SESSION['user_id'];
?>

<div class="container mx-auto px-6 py-12 max-w-4xl">
    <!-- Header -->
    <div class="mb-10 border-b border-slate-100 pb-6 flex justify-between items-end">
        <div>
            <h1 class="text-3xl font-heading font-black text-secondary-900 tracking-tight">Your Purchase History</h1>
            <p class="text-xs text-slate-500">Track and review all curation orders placed through your profile.</p>
        </div>
        <a href="index.php" class="text-xs font-bold text-primary-600 hover:text-primary-700 transition flex items-center gap-1">
            Browse Boutique
        </a>
    </div>

    <!-- Active Orders List -->
    <?php
    $stmt = $conn->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY id DESC");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res->num_rows === 0) {
    ?>
        <div class="text-center py-20 bg-white border border-slate-100 rounded-3xl p-8 shadow-sm space-y-6">
            <div class="text-6xl">🛍️</div>
            <h2 class="text-2xl font-heading font-black text-secondary-900">No Orders Placed</h2>
            <p class="text-xs text-slate-500 max-w-sm mx-auto leading-relaxed">You haven't placed any curated orders yet. Head back to the boutique storefront to choose your first pieces.</p>
            <div class="pt-4">
                <a href="index.php" class="bg-primary-600 hover:bg-primary-700 text-white font-bold px-8 py-3 rounded-full text-sm shadow-lg shadow-primary-100 transition-all">
                    Start Shopping
                </a>
            </div>
        </div>
    <?php
    } else {
    ?>
        <div class="space-y-4">
            <?php
            while ($order = $res->fetch_assoc()) {
                $orderId = $order['id'];
                $orderTotal = number_format($order['total'], 2);
                $orderDate = date('F d, Y', strtotime($order['created_at']));
                $orderTime = date('h:i A', strtotime($order['created_at']));
                $orderStatus = htmlspecialchars($order['status']);
                $paymentMethod = $order['payment_method'] ?? 'GCash';
                $deliveryAddress = $order['delivery_address'] ?? '';
                
                // Status Badge Color selection
                $badgeClass = 'bg-amber-50 border-amber-100 text-amber-700'; // Pending
                if (strtolower($orderStatus) === 'completed' || strtolower($orderStatus) === 'paid') {
                    $badgeClass = 'bg-emerald-50 border-emerald-100 text-emerald-700';
                } elseif (strtolower($orderStatus) === 'cancelled') {
                    $badgeClass = 'bg-rose-50 border-rose-100 text-rose-700';
                }

                // Payment Method Badge
                $payBadge = '<span class="inline-block text-[9px] font-semibold text-[#007DFE] bg-blue-50 border border-blue-100 px-2.5 py-0.5 rounded-full">📱 GCash</span>';
                if ($paymentMethod === 'COD') {
                    $payBadge = '<span class="inline-block text-[9px] font-semibold text-emerald-600 bg-emerald-50 border border-emerald-100 px-2.5 py-0.5 rounded-full">🚚 COD</span>';
                }
            ?>
                <!-- Order Row Card -->
                <div class="bg-white border border-slate-100 p-6 rounded-2xl shadow-sm hover:shadow-md hover:border-slate-200 transition-all duration-300 flex flex-col md:flex-row md:items-center justify-between gap-6">
                    
                    <!-- Metadata Block -->
                    <div class="space-y-1.5 flex-grow min-w-0">
                        <div class="flex items-center gap-3 flex-wrap">
                            <span class="text-sm font-heading font-black text-secondary-900">Order #<?php echo str_pad($orderId, 6, '0', STR_PAD_LEFT); ?></span>
                            <?php echo $payBadge; ?>
                            <span class="inline-block text-[9px] font-black uppercase tracking-widest px-2.5 py-0.5 rounded-full border <?php echo $badgeClass; ?>">
                                <?php echo $orderStatus; ?>
                            </span>
                        </div>
                        <p class="text-xs text-slate-400 font-medium">Placed on <?php echo $orderDate; ?> at <?php echo $orderTime; ?></p>
                        
                        <?php if ($paymentMethod === 'COD' && !empty($deliveryAddress)): ?>
                            <p class="text-[10px] text-slate-500 font-medium truncate max-w-lg mt-1">
                                📍 Delivery: <?php echo htmlspecialchars($deliveryAddress); ?>
                            </p>
                        <?php endif; ?>
                    </div>

                    <!-- Pricing Math & Action -->
                    <div class="flex items-center justify-between md:justify-end gap-8 shrink-0">
                        <!-- Settled Amount -->
                        <div class="text-left md:text-right">
                            <span class="block text-[9px] font-bold text-slate-400 uppercase tracking-widest">Amount Due</span>
                            <span class="font-heading font-black text-primary-600 text-md">₱<?php echo $orderTotal; ?></span>
                        </div>
                        
                        <!-- Receipt Link -->
                        <a href="receipt.php?id=<?php echo $orderId; ?>" 
                           class="bg-slate-50 hover:bg-primary-50 text-slate-600 hover:text-primary-700 border border-slate-100 hover:border-primary-100 px-4 py-2.5 rounded-xl text-xs font-bold transition-all duration-300 flex items-center gap-1.5 shadow-sm">
                            Invoice &rarr;
                        </a>
                    </div>

                </div>
            <?php } ?>
        </div>
    <?php } ?>

</div>

<?php include 'footer.php'; ?>
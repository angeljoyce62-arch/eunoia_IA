<?php
include 'config.php';
include 'header.php';

if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$order_id = $_GET['id'];

// Fetch order header
$stmt = $conn->prepare("SELECT o.*, u.username, u.email FROM orders o JOIN users u ON o.user_id = u.id WHERE o.id = ?");
$stmt->bind_param("i", $order_id);
$stmt->execute();
$order = $stmt->get_result()->fetch_assoc();

if (!$order) {
    echo "
    <div class='min-h-[60vh] flex flex-col items-center justify-center py-20 px-6'>
        <div class='text-6xl mb-4'>⚠️</div>
        <h2 class='text-3xl font-heading font-black text-secondary-900 mb-2'>Invoice Not Found</h2>
        <p class='text-slate-500 mb-8'>The invoice or order record requested does not exist.</p>
        <a href='index.php' class='bg-primary-600 hover:bg-primary-700 text-white font-bold px-8 py-3 rounded-full text-sm shadow-lg shadow-primary-100 transition-all'>
            Return to Store
        </a>
    </div>";
    include 'footer.php';
    exit();
}

$paymentMethod = $order['payment_method'] ?? 'GCash';

// Mask contact number for premium look (e.g. 0917••••567)
$rawPhone = $order['gcash_number'];
$maskedPhone = 'N/A';
if (!empty($rawPhone)) {
    if (strlen($rawPhone) >= 11) {
        $maskedPhone = substr($rawPhone, 0, 4) . ' •••• ' . substr($rawPhone, -3);
    } else {
        $maskedPhone = substr($rawPhone, 0, 3) . ' ••• ' . substr($rawPhone, -2);
    }
}
?>

<!-- Print Styles to isolate only the receipt itself -->
<style>
    @media print {
        header, footer, nav, button, .no-print {
            display: none !important;
        }
        body {
            background-color: white !important;
            color: black !important;
        }
        .print-container {
            border: none !important;
            box-shadow: none !important;
            padding: 0 !important;
            margin: 0 !important;
            max-width: 100% !important;
        }
    }
</style>

<div class="container mx-auto px-6 py-12 flex flex-col items-center justify-center">
    
    <!-- Success Banner (Conditional) -->
    <div class="text-center space-y-3 mb-10 no-print animate-fade-in">
        <?php if ($paymentMethod === 'GCash'): ?>
            <div class="h-16 w-16 bg-blue-50 rounded-full flex items-center justify-center text-blue-500 mx-auto border border-blue-100/50">
                📱
            </div>
            <h1 class="text-3xl font-heading font-black text-secondary-900 tracking-tight">Payment Authorized!</h1>
            <p class="text-xs text-slate-500 max-w-sm mx-auto leading-relaxed">Your simulated GCash payment has been authorized. A copy of your receipt is compiled below.</p>
        <?php else: ?>
            <div class="h-16 w-16 bg-emerald-50 rounded-full flex items-center justify-center text-emerald-500 mx-auto border border-emerald-100/50">
                🚚
            </div>
            <h1 class="text-3xl font-heading font-black text-secondary-900 tracking-tight">Order Placed Successfully!</h1>
            <p class="text-xs text-slate-500 max-w-sm mx-auto leading-relaxed">Your Cash on Delivery order is registered! Standard shipping starts soon. An invoice is compiled below.</p>
        <?php endif; ?>
    </div>

    <!-- Printable Invoice Ticket -->
    <div class="print-container bg-white border border-slate-100 shadow-xl rounded-3xl overflow-hidden max-w-xl w-full p-8 md:p-10 space-y-8 hover:shadow-2xl transition-all duration-300 relative">
        
        <!-- Ticket cut line decoration (Top & Bottom subtle indicators) -->
        <div class="absolute -left-3 top-1/2 -translate-y-1/2 h-6 w-6 rounded-full bg-secondary-50 border-r border-slate-100 hidden md:block"></div>
        <div class="absolute -right-3 top-1/2 -translate-y-1/2 h-6 w-6 rounded-full bg-secondary-50 border-l border-slate-100 hidden md:block"></div>

        <!-- Receipt Header -->
        <div class="flex justify-between items-start border-b border-slate-100 pb-6">
            <div>
                <h3 class="font-heading font-black text-xl text-secondary-900 tracking-tight">EUNOIA <span class="text-primary-600 font-light">IA</span></h3>
                <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest mt-1">Ref: invoice / receipt</p>
            </div>
            
            <div class="text-right">
                <span class="text-xs font-bold text-slate-800 font-mono uppercase tracking-wider block">#<?php echo str_pad($order['id'], 6, "0", STR_PAD_LEFT); ?></span>
                <span class="text-[10px] text-slate-400 mt-1 block"><?php echo date('M d, Y &bull; h:i A', strtotime($order['created_at'])); ?></span>
            </div>
        </div>

        <!-- Customer & Merchant details -->
        <div class="grid grid-cols-2 gap-6 text-xs leading-relaxed">
            <div>
                <h4 class="text-[9px] font-bold text-slate-400 uppercase tracking-widest mb-1.5 font-heading">Billed to</h4>
                <p class="font-bold text-slate-800 leading-tight"><?php echo htmlspecialchars($order['customer_name'] ?: $order['username']); ?></p>
                <p class="text-slate-500 mt-0.5"><?php echo htmlspecialchars($order['email']); ?></p>
            </div>
            
            <div class="text-right">
                <h4 class="text-[9px] font-bold text-slate-400 uppercase tracking-widest mb-1.5 font-heading">Payment Details</h4>
                <?php if ($paymentMethod === 'GCash'): ?>
                    <p class="font-bold text-[#007DFE] flex items-center justify-end gap-1.5 leading-tight">
                        <span class="inline-block w-1.5 h-1.5 rounded-full bg-blue-500 animate-pulse"></span>
                        GCash Digital
                    </p>
                    <p class="text-slate-500 mt-0.5">Acc: <?php echo $maskedPhone; ?></p>
                <?php else: ?>
                    <p class="font-bold text-emerald-600 flex items-center justify-end gap-1.5 leading-tight">
                        <span class="inline-block w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                        Cash on Delivery
                    </p>
                    <p class="text-slate-500 mt-0.5">Contact: <?php echo $maskedPhone; ?></p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Delivery Address (If COD) -->
        <?php if ($paymentMethod === 'COD' && !empty($order['delivery_address'])): ?>
            <div class="border-t border-slate-100 pt-5 text-xs">
                <h4 class="text-[9px] font-bold text-slate-400 uppercase tracking-widest mb-1.5 font-heading">Delivery Address</h4>
                <p class="text-slate-700 bg-slate-50/50 border border-slate-100 p-3.5 rounded-xl leading-relaxed">
                    <?php echo nl2br(htmlspecialchars($order['delivery_address'])); ?>
                </p>
            </div>
        <?php endif; ?>

        <!-- Itemized Table -->
        <div class="space-y-4">
            <h4 class="text-[9px] font-bold text-slate-400 uppercase tracking-widest font-heading">Curated Selections</h4>
            
            <div class="border-t border-b border-slate-100 py-3 space-y-3">
                <table class="w-full text-xs">
                    <thead>
                        <tr class="text-slate-400 font-bold uppercase tracking-wider text-[9px] text-left">
                            <th class="pb-2">Curation Item</th>
                            <th class="pb-2 text-center">Qty</th>
                            <th class="pb-2 text-right">Amount</th>
                        </tr>
                    </thead>
                    <tbody class="text-slate-700 divide-y divide-slate-100/50">
                        <?php
                        $stmt_items = $conn->prepare("SELECT oi.*, p.name FROM order_items oi JOIN products p ON oi.product_id = p.id WHERE oi.order_id = ?");
                        $stmt_items->bind_param("i", $order_id);
                        $stmt_items->execute();
                        $items = $stmt_items->get_result();
                        while ($item = $items->fetch_assoc()):
                            $subtotal = $item['price'] * $item['quantity'];
                        ?>
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="py-2.5 font-medium text-slate-800 leading-tight">
                                <?php echo htmlspecialchars($item['name']); ?><br>
                                <span class="text-[9px] text-primary-600 font-bold uppercase tracking-tighter">Color: <?php echo htmlspecialchars($item['color'] ?? 'Default'); ?></span>
                            </td>
                            <td class="py-2.5 text-center text-slate-500"><?php echo $item['quantity']; ?></td>
                            <td class="py-2.5 text-right font-mono font-bold text-slate-800">₱<?php echo number_format($subtotal, 2); ?></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Calculations & Sign Off -->
        <div class="flex justify-between items-center bg-slate-50 p-5 rounded-2xl border border-slate-100/50">
            <div class="flex flex-col">
                <span class="text-[9px] font-bold text-slate-400 uppercase tracking-widest font-heading">Amount Due</span>
                <span class="text-2xl font-heading font-black text-primary-600 font-mono">₱<?php echo number_format($order['total'], 2); ?></span>
            </div>
            
            <div class="text-right">
                <?php if ($paymentMethod === 'GCash'): ?>
                    <span class="inline-block text-[9px] font-black uppercase tracking-widest bg-blue-50 border border-blue-100 text-blue-700 px-3 py-1.5 rounded-lg">
                        Paid Securely
                    </span>
                <?php else: ?>
                    <span class="inline-block text-[9px] font-black uppercase tracking-widest bg-emerald-50 border border-emerald-100 text-emerald-700 px-3 py-1.5 rounded-lg">
                        COD Pending
                    </span>
                <?php endif; ?>
            </div>
        </div>

        <!-- Print Action Trigger (Isolated) -->
        <div class="pt-4 flex justify-between items-center text-xs text-slate-400 no-print border-t border-slate-100/60 mt-4">
            <button onclick="window.print()" class="text-primary-600 hover:text-primary-700 font-bold flex items-center gap-1.5 hover:scale-[1.02] transition-transform">
                🖨️ Print Invoice
            </button>
            <a href="index.php" class="text-slate-500 hover:text-slate-700 transition flex items-center gap-1.5">
                Return to Gallery &rarr;
            </a>
        </div>

    </div>
</div>

<?php include 'footer.php'; ?>
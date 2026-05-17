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
    echo "<div class='text-center py-20'><h2 class='text-2xl font-bold text-red-600'>Order Not Found</h2></div>";
    include 'footer.php';
    exit();
}
?>

<div class="max-w-2xl mx-auto py-12 px-4">
    <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden">
        <div class="bg-green-600 p-6 text-white text-center">
            <div class="text-4xl mb-2">✅</div>
            <h1 class="text-2xl font-bold">Payment Successful!</h1>
            <p class="opacity-90 text-sm">Thank you for your purchase, <?php echo htmlspecialchars($order['username']); ?>.</p>
        </div>

        <div class="p-8">
            <div class="flex justify-between items-start mb-8">
                <div>
                    <h2 class="text-gray-400 uppercase text-xs font-bold tracking-wider">Order Number</h2>
                    <p class="text-xl font-mono font-bold text-gray-800">#<?php echo str_pad($order['id'], 6, "0", STR_PAD_LEFT); ?></p>
                </div>
                <div class="text-right">
                    <h2 class="text-gray-400 uppercase text-xs font-bold tracking-wider">Date</h2>
                    <p class="text-gray-800 font-medium"><?php echo date('F d, Y', strtotime($order['created_at'])); ?></p>
                </div>
            </div>

            <div class="border-t border-b border-gray-100 py-4 mb-6">
                <table class="w-full">
                    <thead>
                        <tr class="text-left text-xs font-bold text-gray-400 uppercase">
                            <th class="pb-2">Item</th>
                            <th class="pb-2 text-center">Qty</th>
                            <th class="pb-2 text-right">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-700">
                        <?php
                        $stmt_items = $conn->prepare("SELECT oi.*, p.name FROM order_items oi JOIN products p ON oi.product_id = p.id WHERE oi.order_id = ?");
                        $stmt_items->bind_param("i", $order_id);
                        $stmt_items->execute();
                        $items = $stmt_items->get_result();
                        while ($item = $items->fetch_assoc()):
                        ?>
                        <tr>
                            <td class="py-2 text-sm font-medium"><?php echo htmlspecialchars($item['name']); ?></td>
                            <td class="py-2 text-sm text-center"><?php echo $item['quantity']; ?></td>
                            <td class="py-2 text-sm text-right font-mono">₱<?php echo number_format($item['price'] * $item['quantity'], 2); ?></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>

            <div class="flex justify-between items-center bg-gray-50 p-4 rounded-lg">
                <span class="font-bold text-gray-600">Total Paid:</span>
                <span class="text-2xl font-black text-blue-600 font-mono">₱<?php echo number_format($order['total'], 2); ?></span>
            </div>
        </div>

        <div class="bg-gray-50 px-8 py-4 border-t border-gray-100 flex justify-center">
            <button onclick="window.print()" class="text-blue-600 hover:text-blue-800 text-sm font-bold flex items-center gap-2">
                <span>🖨️ Print Receipt</span>
            </button>
        </div>
    </div>
    
    <div class="text-center mt-8">
        <a href="index.php" class="text-gray-500 hover:text-gray-800 text-sm font-medium">← Back to Shopping</a>
    </div>
</div>

<?php include 'footer.php'; ?>
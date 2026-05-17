<?php
include 'config.php';
include 'header.php';

if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
}
?>

<div class="max-w-xl mx-auto py-12 px-4">
    <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100">
        <div class="bg-blue-600 p-8 text-white text-center">
            <h1 class="text-3xl font-extrabold mb-2">Finalize Your Order</h1>
            <p class="opacity-80">Secure Payment via GCash</p>
        </div>
        
        <div class="p-8">
            <div id="checkoutSummary" class="mb-8 p-4 bg-gray-50 rounded-lg">
                <div class="flex justify-between items-center text-gray-600 mb-2">
                    <span>Items Subtotal:</span>
                    <span id="summaryTotal">₱0.00</span>
                </div>
                <div class="flex justify-between items-center text-lg font-bold text-gray-900 border-t pt-2">
                    <span>Grand Total:</span>
                    <span id="grandTotal" class="text-blue-600">₱0.00</span>
                </div>
            </div>

            <form id="checkoutForm" class="space-y-4">
                <div class="flex flex-col gap-1">
                    <label class="text-sm font-bold text-gray-700">GCash Registered Name</label>
                    <input type="text" id="customer_name" required class="w-full px-4 py-3 border rounded-xl focus:ring-2 focus:ring-blue-500 outline-none transition" placeholder="John Doe">
                </div>
                <div class="flex flex-col gap-1">
                    <label class="text-sm font-bold text-gray-700">GCash Mobile Number</label>
                    <input type="text" id="gcash_number" required class="w-full px-4 py-3 border rounded-xl focus:ring-2 focus:ring-blue-500 outline-none transition" placeholder="09XX XXX XXXX">
                </div>
                
                <button type="submit" class="w-full bg-blue-600 text-white py-4 rounded-xl font-bold text-lg hover:bg-blue-700 transition shadow-lg shadow-blue-100 mt-4">
                    Confirm & Pay
                </button>
            </form>
        </div>
    </div>
</div>

<script>
    const cart = JSON.parse(localStorage.getItem("cart")) || [];
    const total = cart.reduce((sum, item) => sum + (item.price * item.qty), 0);
    document.getElementById('summaryTotal').innerText = "₱" + total.toLocaleString();
    document.getElementById('grandTotal').innerText = "₱" + total.toLocaleString();

    document.getElementById('checkoutForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        fetch('process_order.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                cart: cart,
                total: total
            })
        })
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                localStorage.removeItem('cart');
                window.location.href = 'receipt.php?id=' + data.order_id;
            } else {
                alert('Error: ' + data.message);
            }
        });
    });
</script>

<?php include 'footer.php'; ?>
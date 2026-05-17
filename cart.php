<?php
include 'header.php';
?>

<div class="max-w-4xl mx-auto">
    <div class="mb-8 border-b pb-4">
        <h1 class="text-3xl font-bold text-gray-800">Your Shopping Cart</h1>
        <p class="text-gray-600">Review your items before proceeding to checkout.</p>
    </div>

    <div id="cartItems" class="space-y-4 mb-8">
        <!-- Items will be injected here by JS -->
    </div>

    <div id="cartSummary" class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 hidden">
        <div class="flex justify-between items-center mb-6">
            <span class="text-xl font-medium text-gray-600">Total Amount:</span>
            <span id="total" class="text-3xl font-extrabold text-blue-600">₱0.00</span>
        </div>
        <div class="flex flex-col sm:flex-row gap-4">
            <a href="index.php" class="flex-1 text-center py-3 border border-gray-200 rounded-lg font-medium text-gray-600 hover:bg-gray-50 transition">Continue Shopping</a>
            <a href="checkout.php" class="flex-1 text-center py-3 bg-blue-600 text-white rounded-lg font-bold hover:bg-blue-700 transition shadow-lg shadow-blue-200">Proceed to Checkout</a>
        </div>
    </div>

    <div id="emptyCart" class="text-center py-20 hidden">
        <div class="text-6xl mb-4">🛒</div>
        <h2 class="text-2xl font-bold text-gray-800">Your cart is empty</h2>
        <p class="text-gray-500 mb-8">Looks like you haven't added anything yet.</p>
        <a href="index.php" class="bg-blue-600 text-white px-8 py-3 rounded-lg font-bold hover:bg-blue-700 transition">Browse Products</a>
    </div>
</div>

<script>
    function renderCart() {
        const cart = JSON.parse(localStorage.getItem("cart")) || [];
        const container = document.getElementById("cartItems");
        const summary = document.getElementById("cartSummary");
        const empty = document.getElementById("emptyCart");
        
        if (cart.length === 0) {
            container.innerHTML = "";
            summary.classList.add("hidden");
            empty.classList.remove("hidden");
            return;
        }

        empty.classList.add("hidden");
        summary.classList.remove("hidden");
        
        let total = 0;
        container.innerHTML = cart.map((item, index) => {
            const subtotal = item.price * item.qty;
            total += subtotal;
            return `
                <div class="bg-white p-4 rounded-xl border border-gray-100 flex items-center justify-between shadow-sm">
                    <div class="flex-1">
                        <h3 class="font-bold text-gray-800 text-lg">${item.name}</h3>
                        <p class="text-gray-500">₱${parseFloat(item.price).toLocaleString()} × ${item.qty}</p>
                    </div>
                    <div class="text-right flex items-center gap-6">
                        <span class="font-bold text-gray-900">₱${subtotal.toLocaleString()}</span>
                        <button onclick="removeItem(${index})" class="text-red-500 hover:text-red-700 font-medium text-sm">Remove</button>
                    </div>
                </div>`;
        }).join('');

        document.getElementById("total").innerText = "₱" + total.toLocaleString();
    }

    function removeItem(index) {
        let cart = JSON.parse(localStorage.getItem("cart")) || [];
        cart.splice(index, 1);
        localStorage.setItem("cart", JSON.stringify(cart));
        renderCart();
    }

    document.addEventListener('DOMContentLoaded', renderCart);
</script>

<?php include 'footer.php'; ?>
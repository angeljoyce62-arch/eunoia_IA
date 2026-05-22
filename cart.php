<?php
include 'header.php';

// Enforce role separation
if (!isset($_SESSION['role'])) {
    header("Location: login.php");
    exit();
}

$role = $_SESSION['role'];
if ($role !== 'customer') {
    echo "
    <div class='container mx-auto px-6 py-20 flex flex-col items-center justify-center min-h-[60vh]'>
        <div class='bg-white p-12 border border-slate-100 rounded-3xl shadow-xl max-w-lg w-full text-center space-y-6 hover:shadow-2xl transition-all duration-300'>
            <div class='text-6xl text-amber-500'>🛡️</div>
            <h1 class='text-2xl font-heading font-black text-secondary-900 uppercase tracking-tight'>Access Restricted</h1>
            <p class='text-sm text-slate-500 leading-relaxed'>
                Merchant accounts are strictly separated from shopping operations. Shopping carts and checkout systems are exclusively accessible via Customer profiles.
            </p>
            <div class='pt-4'>
                <a href='seller_dashboard.php' class='inline-block w-full bg-slate-900 hover:bg-luxury-gold text-white hover:text-slate-900 font-bold py-3.5 rounded-xl transition-all duration-300 shadow-md'>
                    Return to Business Portal
                </a>
            </div>
        </div>
    </div>";
    include 'footer.php';
    exit();
}
?>

<div class="container mx-auto px-6 py-12">
    <!-- Header -->
    <div class="mb-10 border-b border-slate-100 pb-6 flex justify-between items-end">
        <div>
            <h1 class="text-3xl font-heading font-black text-secondary-900 tracking-tight">Your Shopping Cart</h1>
            <p class="text-xs text-slate-500">Review your selected curations before securing payment.</p>
        </div>
        <a href="index.php" class="text-xs font-bold text-primary-600 hover:text-primary-700 transition flex items-center gap-1">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-4 h-4">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" />
            </svg>
            Continue Shopping
        </a>
    </div>

    <!-- Empty Cart State -->
    <div id="emptyCart" class="hidden text-center py-20 space-y-6">
        <div class="text-7xl">🛒</div>
        <h2 class="text-2xl font-heading font-black text-secondary-900">Your cart is empty</h2>
        <p class="text-xs text-slate-500 max-w-sm mx-auto leading-relaxed">It seems you have not curated any items in your bag yet. Head back to the gallery to explore.</p>
        <div class="pt-4">
            <a href="index.php" class="bg-primary-600 hover:bg-primary-700 text-white font-bold px-8 py-3 rounded-full text-sm shadow-lg shadow-primary-100 hover:scale-[1.02] transition-all">
                Browse Masterpieces
            </a>
        </div>
    </div>

    <!-- Active Cart Layout -->
    <div id="cartLayout" class="hidden grid grid-cols-1 lg:grid-cols-3 gap-10">
        
        <!-- Cart Items List (Left Columns) -->
        <div class="lg:col-span-2 space-y-4" id="cartItemsList">
            <!-- Items injected by Javascript -->
        </div>

        <!-- Summary Widget (Right Column) -->
        <div class="lg:col-span-1">
            <div class="bg-white border border-slate-100 p-8 rounded-3xl shadow-lg space-y-6 sticky top-28">
                <h3 class="text-lg font-heading font-black text-secondary-900 border-b border-slate-100 pb-4">Order Summary</h3>
                
                <div class="space-y-3.5">
                    <div class="flex justify-between items-center text-xs text-slate-500 font-medium">
                        <span>Items Subtotal</span>
                        <span id="subtotal" class="font-bold text-slate-800">₱0.00</span>
                    </div>
                    <div class="flex justify-between items-center text-xs text-slate-500 font-medium">
                        <span>Secure Shipping</span>
                        <span class="text-emerald-600 font-bold uppercase tracking-widest text-[9px] bg-emerald-50 px-2 py-0.5 rounded">Complimentary</span>
                    </div>
                    <div class="flex justify-between items-center text-xs text-slate-500 font-medium">
                        <span>GCash Convenience Fee</span>
                        <span class="font-bold text-slate-800">₱0.00</span>
                    </div>
                    
                    <div class="h-px bg-slate-100 my-4"></div>
                    
                    <div class="flex justify-between items-end">
                        <div class="flex flex-col">
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Total Amount</span>
                            <span id="total" class="text-2xl font-heading font-black text-primary-600">₱0.00</span>
                        </div>
                        <span class="text-[9px] font-bold text-slate-400 bg-slate-50 px-2 py-1 rounded">VAT Included</span>
                    </div>
                          <div class="pt-4 space-y-3">
                    <button id="checkoutBtn" onclick="handleSecureCheckout()" class="w-full text-center bg-primary-600 hover:bg-primary-700 text-white font-bold py-3.5 rounded-xl shadow-lg shadow-primary-100 hover:shadow-xl hover:scale-[1.01] transition-all duration-300">
                        Secure Checkout
                    </button>
                    <button onclick="clearShoppingBag()" class="w-full text-slate-400 hover:text-slate-600 font-semibold text-xs py-2 transition-all">
                        Empty Shopping Bag
                    </button>
                </div>
            </div>
        </div>

    </div>
</div>

<script>
    function renderShoppingBag() {
        const cart = JSON.parse(localStorage.getItem("cart")) || [];
        const layout = document.getElementById("cartLayout");
        const empty = document.getElementById("emptyCart");
        const list = document.getElementById("cartItemsList");

        if (cart.length === 0) {
            layout.classList.add("hidden");
            empty.classList.remove("hidden");
            return;
        }

        empty.classList.add("hidden");
        layout.classList.remove("hidden");

        list.innerHTML = cart.map((item, index) => {
            const subtotal = item.price * item.qty;
            
            // Generate clean descriptions or categories if cached, otherwise fallback Unsplash
            let category = item.category || 'General';
            // We use client side fallback for Unsplash matching item category
            let imgUrl = 'https://images.unsplash.com/photo-1523275335684-37898b6baf30?auto=format&fit=crop&q=80&w=200';
            let nameLower = item.name.toLowerCase();
            if (nameLower.includes('chair') || nameLower.includes('oak')) {
                imgUrl = 'https://images.unsplash.com/photo-1592078615290-033ee584e267?auto=format&fit=crop&q=80&w=200';
            } else if (nameLower.includes('shirt') || nameLower.includes('linen')) {
                imgUrl = 'https://images.unsplash.com/photo-1596755094514-f87e34085b2c?auto=format&fit=crop&q=80&w=200';
            } else if (nameLower.includes('vase') || nameLower.includes('ceramic')) {
                imgUrl = 'https://images.unsplash.com/photo-1578500494198-246f612d3b3d?auto=format&fit=crop&q=80&w=200';
            } else if (nameLower.includes('lamp') || nameLower.includes('light')) {
                imgUrl = 'https://images.unsplash.com/photo-1507473885765-e6ed057f782c?auto=format&fit=crop&q=80&w=200';
            } else if (nameLower.includes('blanket') || nameLower.includes('silk')) {
                imgUrl = 'https://images.unsplash.com/photo-1523275335684-37898b6baf30?auto=format&fit=crop&q=80&w=200';
            } else if (nameLower.includes('brass') || nameLower.includes('incense')) {
                imgUrl = 'https://images.unsplash.com/photo-1602872030219-aa047913341b?auto=format&fit=crop&q=80&w=200';
            }

            return `
                <div class="bg-white border border-slate-100 p-5 rounded-2xl shadow-sm hover:shadow-md transition-shadow flex items-center justify-between gap-6">
                    <div class="flex items-center gap-4 flex-grow min-w-0">
                        <!-- Selection Checkbox -->
                        <input type="checkbox" data-index="${index}" class="cart-item-checkbox h-5 w-5 text-primary-600 border-slate-200 rounded focus:ring-primary-500 cursor-pointer shrink-0 focus:outline-none" checked onchange="recalculateCart()">
                        
                        <!-- Thumbnail -->
                        <div class="h-16 w-16 bg-slate-50 border border-slate-100 rounded-xl overflow-hidden shrink-0">
                            <img src="${imgUrl}" class="h-full w-full object-cover">
                        </div>
                        <!-- Name & Subdetails -->
                        <div class="min-w-0">
                            <h3 class="font-heading font-bold text-secondary-900 leading-snug truncate">${item.name}</h3>
                            <p class="text-xs text-slate-400 mt-0.5">₱${parseFloat(item.price).toLocaleString('en-US', {minimumFractionDigits: 2})} &bull; Qty: ${item.qty}</p>
                        </div>
                    </div>

                    <!-- Quantity Control & Price math -->
                    <div class="flex items-center gap-8 shrink-0">
                        <div class="text-right">
                            <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest">Subtotal</span>
                            <span class="font-heading font-black text-secondary-900 text-sm">₱${subtotal.toLocaleString('en-US', {minimumFractionDigits: 2})}</span>
                        </div>
                        <button onclick="removeShoppingItem(${index})" class="text-rose-500 hover:text-rose-700 bg-rose-50 hover:bg-rose-100 p-2 rounded-xl transition-all">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                            </svg>
                        </button>
                    </div>
                </div>`;
        }).join('');

        recalculateCart();
    }

    function recalculateCart() {
        const checkboxes = document.querySelectorAll('.cart-item-checkbox');
        let total = 0;
        const cart = JSON.parse(localStorage.getItem("cart")) || [];
        
        checkboxes.forEach(cb => {
            if (cb.checked) {
                const index = parseInt(cb.getAttribute('data-index'));
                const item = cart[index];
                if (item) {
                    total += item.price * item.qty;
                }
            }
        });

        document.getElementById("subtotal").innerText = "₱" + total.toLocaleString('en-US', {minimumFractionDigits: 2});
        document.getElementById("total").innerText = "₱" + total.toLocaleString('en-US', {minimumFractionDigits: 2});

        const checkoutBtn = document.getElementById("checkoutBtn");
        if (checkoutBtn) {
            if (total > 0) {
                checkoutBtn.classList.remove('opacity-50', 'cursor-not-allowed');
                checkoutBtn.removeAttribute('disabled');
            } else {
                checkoutBtn.classList.add('opacity-50', 'cursor-not-allowed');
                checkoutBtn.setAttribute('disabled', 'true');
            }
        }
    }

    function handleSecureCheckout() {
        const checkboxes = document.querySelectorAll('.cart-item-checkbox');
        const selectedItems = [];
        const cart = JSON.parse(localStorage.getItem("cart")) || [];
        
        checkboxes.forEach(cb => {
            if (cb.checked) {
                const index = parseInt(cb.getAttribute('data-index'));
                const item = cart[index];
                if (item) {
                    selectedItems.push(item);
                }
            }
        });

        if (selectedItems.length === 0) {
            showToast("Please select at least one item to checkout.", "warning");
            return;
        }

        // Store selected items specifically for checkout.php
        localStorage.setItem("checkout_items", JSON.stringify(selectedItems));
        window.location.href = "checkout.php";
    }

    function removeShoppingItem(index) {
        let cart = JSON.parse(localStorage.getItem("cart")) || [];
        const name = cart[index].name;
        cart.splice(index, 1);
        localStorage.setItem("cart", JSON.stringify(cart));
        window.dispatchEvent(new Event('cartUpdated'));
        renderShoppingBag();
        showToast(`Removed ${name} from your cart.`);
    }

    function clearShoppingBag() {
        if(confirm("Are you sure you want to empty your shopping cart?")) {
            localStorage.removeItem("cart");
            window.dispatchEvent(new Event('cartUpdated'));
            renderShoppingBag();
            showToast("Shopping cart cleared.");
        }
    }

    document.addEventListener('DOMContentLoaded', renderShoppingBag);
</script>
 
<?php include 'footer.php'; ?>
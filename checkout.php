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
?>

<div class="container mx-auto px-6 py-12 max-w-5xl">
    <!-- Header -->
    <div class="mb-10 border-b border-slate-100 pb-6">
        <h1 class="text-3xl font-heading font-black text-secondary-900 tracking-tight">Checkout Curation</h1>
        <p class="text-xs text-slate-500">Provide details for your preferred delivery and secure your selected masterpieces.</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-5 gap-10">
        <!-- Secure Payment Form (Left Column, span 3) -->
        <div class="lg:col-span-3 space-y-8">
            <!-- Payment Method Selector Cards -->
            <div class="bg-white border border-slate-100 shadow-xl rounded-3xl p-8 hover:shadow-2xl transition-all duration-300">
                <h2 class="text-sm font-heading font-black text-secondary-900 uppercase tracking-wider mb-6">Select Payment Method</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- GCash Option Card -->
                    <label for="pay_gcash" class="cursor-pointer">
                        <input type="radio" name="payment_method_select" id="pay_gcash" value="GCash" checked class="peer hidden">
                        <div class="p-5 border border-slate-200 rounded-2xl peer-checked:border-[#007DFE] peer-checked:bg-blue-50/30 hover:bg-slate-50 transition-all flex items-start gap-4 h-full relative">
                            <div class="h-10 w-10 bg-blue-100 text-[#007DFE] rounded-xl flex items-center justify-center shrink-0">
                                📱
                            </div>
                            <div class="space-y-1 pr-4">
                                <h3 class="font-heading font-bold text-sm text-secondary-900 flex items-center gap-1.5">
                                    GCash Digital
                                    <span class="bg-[#007DFE]/10 text-[#007DFE] text-[8px] font-black uppercase tracking-widest px-1.5 py-0.5 rounded">Secured</span>
                                </h3>
                                <p class="text-[10px] text-slate-400 leading-normal">Instant digital settlement with automated simulated payment confirmation.</p>
                            </div>
                            <div class="absolute right-4 top-4 h-5 w-5 rounded-full border-2 border-slate-200 peer-checked:border-[#007DFE] flex items-center justify-center">
                                <div class="h-2.5 w-2.5 rounded-full bg-[#007DFE] hidden peer-checked:block"></div>
                            </div>
                        </div>
                    </label>

                    <!-- COD Option Card -->
                    <label for="pay_cod" class="cursor-pointer">
                        <input type="radio" name="payment_method_select" id="pay_cod" value="COD" class="peer hidden">
                        <div class="p-5 border border-slate-200 rounded-2xl peer-checked:border-emerald-600 peer-checked:bg-emerald-50/20 hover:bg-slate-50 transition-all flex items-start gap-4 h-full relative">
                            <div class="h-10 w-10 bg-emerald-100 text-emerald-600 rounded-xl flex items-center justify-center shrink-0">
                                🚚
                            </div>
                            <div class="space-y-1 pr-4">
                                <h3 class="font-heading font-bold text-sm text-secondary-900 flex items-center gap-1.5">
                                    Cash on Delivery
                                </h3>
                                <p class="text-[10px] text-slate-400 leading-normal">Pay with cash upon physical delivery. Highly flexible and secure.</p>
                            </div>
                            <div class="absolute right-4 top-4 h-5 w-5 rounded-full border-2 border-slate-200 peer-checked:border-emerald-600 flex items-center justify-center">
                                <div class="h-2.5 w-2.5 rounded-full bg-emerald-600 hidden peer-checked:block"></div>
                            </div>
                        </div>
                    </label>
                </div>
            </div>

            <!-- Fields Container Card -->
            <div class="bg-white border border-slate-100 shadow-xl rounded-3xl overflow-hidden hover:shadow-2xl transition-all duration-300">
                <!-- GCash Header Decoration -->
                <div id="gcashHeader" class="bg-[#007DFE] p-8 text-white relative transition-all duration-300">
                    <div class="absolute right-6 top-6 bg-white/20 text-[9px] font-black uppercase tracking-widest px-2.5 py-1 rounded-full">
                        GCash Secured
                    </div>
                    <h2 class="text-2xl font-heading font-black tracking-tight mb-1">Simulated GCash Details</h2>
                    <p class="text-xs text-blue-100">Please provide your registered GCash name and mobile phone number.</p>
                </div>

                <!-- COD Header Decoration -->
                <div id="codHeader" class="hidden bg-emerald-600 p-8 text-white relative transition-all duration-300">
                    <div class="absolute right-6 top-6 bg-white/20 text-[9px] font-black uppercase tracking-widest px-2.5 py-1 rounded-full">
                        COD Delivery
                    </div>
                    <h2 class="text-2xl font-heading font-black tracking-tight mb-1">COD Delivery Information</h2>
                    <p class="text-xs text-emerald-100">Please specify your delivery address and recipient details.</p>
                </div>

                <!-- Checkout Form -->
                <form id="checkoutForm" class="p-8 space-y-6">
                    <!-- GCash Dynamic Fields -->
                    <div id="gcashFields" class="space-y-6">
                        <!-- GCash Registered Name -->
                        <div class="space-y-1.5">
                            <label class="text-xs font-bold font-heading text-slate-500 uppercase tracking-widest block">GCash Registered Name</label>
                            <div class="relative">
                                <input type="text" id="gcash_name" placeholder="e.g. Angel Joyce" 
                                       class="w-full pl-10 pr-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#007DFE] focus:bg-white transition-all">
                                <div class="absolute left-3.5 top-3.5 text-slate-400">
                                    👤
                                </div>
                            </div>
                        </div>

                        <!-- GCash Number -->
                        <div class="space-y-1.5">
                            <label class="text-xs font-bold font-heading text-slate-500 uppercase tracking-widest block">GCash Mobile Number</label>
                            <div class="relative">
                                <input type="text" id="gcash_number" placeholder="e.g. 09171234567" pattern="^(09)\d{9}$" title="Please enter a valid 11-digit GCash number starting with 09"
                                       class="w-full pr-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#007DFE] focus:bg-white transition-all">
                            </div>
                            <p class="text-[10px] text-slate-400 italic">Formatting requirement: 11 digits starting with 09 (e.g. 09XXXXXXXXX)</p>
                        </div>

                        <div class="bg-blue-50 border border-blue-100/50 p-4 rounded-2xl flex items-start gap-3">
                            <div class="text-blue-600 font-bold text-sm shrink-0">ℹ️</div>
                            <div class="text-[11px] text-blue-700 leading-relaxed font-medium">
                                <strong>Simulated Environment:</strong> Your transaction is running in secure sandbox mode. No real money will be charged from your GCash account.
                            </div>
                        </div>
                    </div>

                    <!-- COD Dynamic Fields -->
                    <div id="codFields" class="hidden space-y-6">
                        <!-- Recipient Name -->
                        <div class="space-y-1.5">
                            <label class="text-xs font-bold font-heading text-slate-500 uppercase tracking-widest block">Recipient Delivery Name</label>
                            <div class="relative">
                                <input type="text" id="cod_name" placeholder="e.g. Angel Joyce" 
                                       class="w-full pl-10 pr-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-emerald-600 focus:bg-white transition-all">
                                <div class="absolute left-3.5 top-3.5 text-slate-400">
                                    👤
                                </div>
                            </div>
                        </div>

                        <!-- Contact Phone -->
                        <div class="space-y-1.5">
                            <label class="text-xs font-bold font-heading text-slate-500 uppercase tracking-widest block">Recipient Phone Number</label>
                            <div class="relative">
                                    <input type="text" id="cod_phone" placeholder="e.g. 09171234567" pattern="^(09)\d{9}$" title="Please enter a valid 11-digit mobile number starting with 09"
                                        class="w-full pr-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-emerald-600 focus:bg-white transition-all">
                            </div>
                            <p class="text-[10px] text-slate-400 italic">Formatting requirement: 11 digits starting with 09 (e.g. 09XXXXXXXXX)</p>
                        </div>

                        <!-- Delivery Address -->
                        <div class="space-y-1.5">
                            <label class="text-xs font-bold font-heading text-slate-500 uppercase tracking-widest block">Full Delivery Address</label>
                            <div class="relative">
                                <textarea id="cod_address" placeholder="e.g. 123 Luxury Lane, Brgy. Premium, Makati City, Metro Manila" 
                                          class="w-full pl-10 pr-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-emerald-600 focus:bg-white transition-all h-28 resize-none"></textarea>
                                <div class="absolute left-3.5 top-3.5 text-slate-400">
                                    📍
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" id="submitBtn" class="w-full bg-[#007DFE] hover:bg-[#006bd9] text-white font-bold py-4 rounded-xl shadow-lg hover:shadow-xl hover:scale-[1.01] active:scale-[0.99] transition-all duration-300">
                        Authorize Simulated Payment
                    </button>
                </form>
            </div>
        </div>

        <!-- Checkout Summary (Right Column, span 2) -->
        <div class="lg:col-span-2">
            <div class="bg-white border border-slate-100 p-8 rounded-3xl shadow-lg space-y-6 sticky top-28">
                <h3 class="text-md font-heading font-black text-secondary-900 border-b border-slate-100 pb-4">Selected Curations</h3>
                
                <!-- Items list -->
                <div id="checkoutItemsList" class="space-y-4 max-h-[260px] overflow-y-auto pr-1">
                    <!-- Loaded dynamically -->
                </div>
                
                <div class="h-px bg-slate-100"></div>

                <!-- Calculations -->
                <div class="space-y-2.5">
                    <div class="flex justify-between items-center text-xs text-slate-500 font-medium">
                        <span>Items Subtotal</span>
                        <span id="itemsTotal" class="font-bold text-slate-800">₱0.00</span>
                    </div>
                    <div class="flex justify-between items-center text-xs text-slate-500 font-medium">
                        <span>Secure Shipping</span>
                        <span class="text-emerald-600 font-bold tracking-widest text-[9px] uppercase bg-emerald-50 px-2 py-0.5 rounded">Free</span>
                    </div>
                    <div class="flex justify-between items-center text-xs text-slate-500 font-medium">
                        <span>Transaction Fee</span>
                        <span class="text-slate-400 font-bold tracking-widest text-[9px] uppercase bg-slate-50 px-2 py-0.5 rounded">Free</span>
                    </div>
                    
                    <div class="h-px bg-slate-100 my-2"></div>
                    
                    <div class="flex justify-between items-end pt-2">
                        <div class="flex flex-col">
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Grand Total</span>
                            <span id="grandTotalAmount" class="text-2xl font-heading font-black text-primary-600">₱0.00</span>
                        </div>
                        <span class="text-[9px] font-bold text-slate-400 bg-slate-50 px-2 py-1 rounded">VAT Included</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Pull from checkout_items first, fallback to standard cart
    let checkoutItems = JSON.parse(localStorage.getItem("checkout_items")) || [];
    
    if (checkoutItems.length === 0) {
        // Fallback to primary cart if no specific checkout items are compiled
        checkoutItems = JSON.parse(localStorage.getItem("cart")) || [];
    }

    // Redirect if absolutely nothing in checkout pipeline
    if (checkoutItems.length === 0) {
        showToast("Your checkout list is empty.", "warning");
        setTimeout(() => {
            window.location.href = 'index.php';
        }, 1500);
    }

    // Calculate Grand Total
    const total = checkoutItems.reduce((sum, item) => sum + (item.price * item.qty), 0);
    document.getElementById('itemsTotal').innerText = "₱" + total.toLocaleString('en-US', {minimumFractionDigits: 2});
    document.getElementById('grandTotalAmount').innerText = "₱" + total.toLocaleString('en-US', {minimumFractionDigits: 2});

    // Populate checkout items visually
    const list = document.getElementById('checkoutItemsList');
    list.innerHTML = checkoutItems.map(item => `
        <div class="flex justify-between items-start gap-4">
            <div class="min-w-0 flex-grow">
                <p class="text-xs font-bold text-secondary-900 leading-snug truncate">${item.name}</p>
                <p class="text-[10px] text-slate-400 mt-0.5">Qty: ${item.qty} &bull; ₱${parseFloat(item.price).toLocaleString('en-US')}</p>
            </div>
            <span class="text-xs font-heading font-bold text-slate-700 shrink-0">₱${(item.price * item.qty).toLocaleString('en-US', {minimumFractionDigits: 2})}</span>
        </div>
    `).join('');

    // Toggle Payment Method view screens and inputs
    const gcashRadio = document.getElementById('pay_gcash');
    const codRadio = document.getElementById('pay_cod');
    
    const gcashHeader = document.getElementById('gcashHeader');
    const codHeader = document.getElementById('codHeader');
    
    const gcashFields = document.getElementById('gcashFields');
    const codFields = document.getElementById('codFields');
    
    const submitBtn = document.getElementById('submitBtn');

    // Setup input elements references for dynamic validations
    const gName = document.getElementById('gcash_name');
    const gNum = document.getElementById('gcash_number');
    const cName = document.getElementById('cod_name');
    const cPhone = document.getElementById('cod_phone');
    const cAddress = document.getElementById('cod_address');

    function togglePaymentFields() {
        if (gcashRadio.checked) {
            // Show GCash
            gcashHeader.classList.remove('hidden');
            gcashFields.classList.remove('hidden');
            codHeader.classList.add('hidden');
            codFields.classList.add('hidden');
            
            submitBtn.className = "w-full bg-[#007DFE] hover:bg-[#006bd9] text-white font-bold py-4 rounded-xl shadow-lg hover:shadow-xl hover:scale-[1.01] active:scale-[0.99] transition-all duration-300";
            submitBtn.innerText = "Authorize Simulated Payment";

            // Enforce required
            gName.required = true;
            gNum.required = true;
            cName.required = false;
            cPhone.required = false;
            cAddress.required = false;
        } else {
            // Show COD
            gcashHeader.classList.add('hidden');
            gcashFields.classList.add('hidden');
            codHeader.classList.remove('hidden');
            codFields.classList.remove('hidden');
            
            submitBtn.className = "w-full bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-4 rounded-xl shadow-lg hover:shadow-xl hover:scale-[1.01] active:scale-[0.99] transition-all duration-300";
            submitBtn.innerText = "Place Cash on Delivery Order";

            // Enforce required
            gName.required = false;
            gNum.required = false;
            cName.required = true;
            cPhone.required = true;
            cAddress.required = true;
        }
    }

    // Bind event listeners
    gcashRadio.addEventListener('change', togglePaymentFields);
    codRadio.addEventListener('change', togglePaymentFields);

    // Run initial setup on load
    togglePaymentFields();

    // Handle form submit
    document.getElementById('checkoutForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const paymentMethod = document.querySelector('input[name="payment_method_select"]:checked').value;
        let customerName = "";
        let contactNumber = "";
        let deliveryAddress = "";

        if (paymentMethod === "GCash") {
            customerName = gName.value.trim();
            contactNumber = gNum.value.trim();
            deliveryAddress = ""; // GCash digital has no delivery address recorded in same format
        } else {
            customerName = cName.value.trim();
            contactNumber = cPhone.value.trim();
            deliveryAddress = cAddress.value.trim();
        }

        // Validate values are not empty
        if (!customerName || !contactNumber || (paymentMethod === "COD" && !deliveryAddress)) {
            showToast("Please fill out all required details.", "warning");
            return;
        }

        // Show a loading state
        submitBtn.disabled = true;
        submitBtn.innerText = "Processing Order...";

        fetch('process_order.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                cart: checkoutItems,
                total: total,
                customer_name: customerName,
                gcash_number: contactNumber,
                payment_method: paymentMethod,
                delivery_address: deliveryAddress
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Splicing purchased items out of the main cart
                let mainCart = JSON.parse(localStorage.getItem("cart")) || [];
                
                // Exclude items that were purchased in this checkout batch
                mainCart = mainCart.filter(cartItem => {
                    const matchIdx = checkoutItems.findIndex(ci => ci.id === cartItem.id);
                    if (matchIdx !== -1) {
                        checkoutItems.splice(matchIdx, 1);
                        return false;
                    }
                    return true;
                });
                
                // Write back cleaned cart
                localStorage.setItem("cart", JSON.stringify(mainCart));
                localStorage.removeItem("checkout_items");
                window.dispatchEvent(new Event('cartUpdated'));
                
                showToast("Order placed successfully! Redirecting...");
                setTimeout(() => {
                    window.location.href = 'receipt.php?id=' + data.order_id;
                }, 1000);
            } else {
                alert('Order Error: ' + data.message);
                submitBtn.disabled = false;
                togglePaymentFields();
            }
        })
        .catch(err => {
            console.error(err);
            alert('A network error occurred while securing order.');
            submitBtn.disabled = false;
            togglePaymentFields();
        });
    });
</script>

<?php include 'footer.php'; ?>
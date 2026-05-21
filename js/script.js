let cart = JSON.parse(localStorage.getItem("cart")) || [];

// Custom Elegant Toast Notification System
function showToast(message, type = 'success') {
    let container = document.getElementById('toast-container');
    if (!container) {
        container = document.createElement('div');
        container.id = 'toast-container';
        container.className = 'fixed bottom-6 right-6 z-[100] flex flex-col gap-3 max-w-sm w-full';
        document.body.appendChild(container);
    }

    const toast = document.createElement('div');
    toast.className = 'bg-white/90 backdrop-blur-md border border-slate-100 shadow-xl rounded-2xl p-4 flex items-start gap-3.5 transform translate-y-2 opacity-0 transition-all duration-300 ease-out';
    
    let iconColor = type === 'success' ? 'text-emerald-500' : 'text-amber-500';
    let iconSvg = type === 'success' 
        ? `<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-5 h-5">
             <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
           </svg>`
        : `<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-5 h-5">
             <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
           </svg>`;

    toast.innerHTML = `
        <div class="${iconColor} shrink-0 pt-0.5">${iconSvg}</div>
        <div class="flex-grow">
            <h5 class="text-xs font-heading font-black text-secondary-900 uppercase tracking-wider">${type === 'success' ? 'Curated' : 'Notice'}</h5>
            <p class="text-xs text-slate-500 mt-1 leading-relaxed">${message}</p>
        </div>
    `;

    container.appendChild(toast);

    // Trigger animate-in
    setTimeout(() => {
        toast.classList.remove('translate-y-2', 'opacity-0');
        toast.classList.add('translate-y-0', 'opacity-100');
    }, 10);

    // Dismiss logic
    const dismiss = () => {
        toast.classList.remove('translate-y-0', 'opacity-100');
        toast.classList.add('translate-y-2', 'opacity-0');
        setTimeout(() => {
            toast.remove();
        }, 300);
    };

    const timer = setTimeout(dismiss, 3500);

    toast.addEventListener('click', () => {
        clearTimeout(timer);
        dismiss();
    });
}

// Add Item to Cart
function addToCart(id, name, price, qty){
    qty = parseInt(qty);
    if (isNaN(qty) || qty <= 0) {
        showToast("Please enter a valid quantity.", "warning");
        return;
    }

    let cart = JSON.parse(localStorage.getItem("cart")) || [];
    
    // Check if item already exists in cart
    let existingIndex = cart.findIndex(item => item.id === id);
    if (existingIndex !== -1) {
        cart[existingIndex].qty += qty;
    } else {
        cart.push({
            id: id,
            name: name,
            price: parseFloat(price),
            qty: qty
        });
    }

    localStorage.setItem("cart", JSON.stringify(cart));
    
    // Dispatch custom event to notify header badge
    window.dispatchEvent(new Event('cartUpdated'));
    
    showToast(`Added ${qty} × ${name} to your cart successfully!`);
}

/* LIVE STORE SEARCH */
document.addEventListener("DOMContentLoaded", () => {
    let searchInput = document.getElementById("searchInput");
    if (searchInput) {
        searchInput.addEventListener("input", function() {
            let value = this.value.toLowerCase().trim();
            let cards = document.querySelectorAll(".card");
            
            cards.forEach(card => {
                let cardTitle = card.querySelector("h3") ? card.querySelector("h3").innerText.toLowerCase() : "";
                let cardDesc = card.querySelector("p") ? card.querySelector("p").innerText.toLowerCase() : "";
                let seller = card.querySelector(".font-semibold") ? card.querySelector(".font-semibold").innerText.toLowerCase() : "";
                
                if (cardTitle.includes(value) || cardDesc.includes(value) || seller.includes(value)) {
                    card.style.display = "flex"; // cards are flex containers
                    card.classList.remove("opacity-0", "scale-95");
                } else {
                    card.style.display = "none";
                }
            });
        });
    }
});

/* CATEGORY FILTERING VIA AJAX */
function filterCategory(category) {
    const buttons = document.querySelectorAll('.category-btn');
    buttons.forEach(btn => {
        if (btn.innerText.trim() === category || (category === 'All' && btn.innerText.includes('All'))) {
            btn.className = 'category-btn bg-primary-600 text-white text-xs font-bold px-5 py-2.5 rounded-full shadow-md shadow-primary-100 hover:scale-[1.03] transition-all';
        } else {
            btn.className = 'category-btn bg-white hover:bg-slate-50 text-slate-600 border border-slate-200/60 text-xs font-semibold px-5 py-2.5 rounded-full hover:scale-[1.03] transition-all';
        }
    });

    const grid = document.getElementById('productGrid');
    if (grid) {
        grid.style.opacity = '0.4';
    }

    fetch(`fetch_products.php?category=${encodeURIComponent(category)}`)
        .then(response => response.text())
        .then(html => {
            setTimeout(() => {
                if (grid) {
                    grid.innerHTML = html;
                    grid.style.opacity = '1';
                    
                    // Attach search keyup bindings again if needed
                    let searchInput = document.getElementById("searchInput");
                    if (searchInput && searchInput.value) {
                        searchInput.dispatchEvent(new Event('input'));
                    }
                }
            }, 100);
        })
        .catch(err => {
            console.error(err);
            if (grid) grid.style.opacity = '1';
        });
}

/* PREMIUM FLY-TO-CART MICRO-ANIMATION */
function animateFlyToCart(productId, event) {
    if (!event) return;

    // Find the product image within the same card container
    const clickedBtn = event.currentTarget || event.target;
    const card = clickedBtn.closest('.card') || clickedBtn.closest('.bg-white') || document;
    let img = card.querySelector(`img[data-product-id="${productId}"], .product-card-img`);
    
    // Fallback if not found in parent card
    if (!img) {
        img = document.querySelector(`img[data-product-id="${productId}"]`);
    }
    
    // Fallback to first image on details page
    if (!img) {
        img = document.querySelector('.product-card-img');
    }
    
    const cartIcon = document.getElementById('cartIconLink');
    if (!img || !cartIcon) return;

    // Calculate dimensions
    const imgRect = img.getBoundingClientRect();
    const cartRect = cartIcon.getBoundingClientRect();

    // Create thumbnail clone
    const clone = img.cloneNode(true);
    clone.style.position = 'fixed';
    clone.style.left = imgRect.left + 'px';
    clone.style.top = imgRect.top + 'px';
    clone.style.width = imgRect.width + 'px';
    clone.style.height = imgRect.height + 'px';
    clone.style.zIndex = '9999';
    clone.style.pointerEvents = 'none';
    clone.style.transition = 'all 0.8s cubic-bezier(0.25, 1, 0.5, 1)';
    clone.style.borderRadius = '50%';
    clone.style.opacity = '0.9';
    clone.style.boxShadow = '0 10px 25px -5px rgba(124, 58, 237, 0.3)';

    document.body.appendChild(clone);

    // Trigger translate-scale transition
    setTimeout(() => {
        clone.style.left = (cartRect.left + cartRect.width / 2 - 15) + 'px';
        clone.style.top = (cartRect.top + cartRect.height / 2 - 15) + 'px';
        clone.style.width = '30px';
        clone.style.height = '30px';
        clone.style.opacity = '0.15';
        clone.style.transform = 'rotate(360deg)';
    }, 30);

    // Remove clone and trigger bounce
    setTimeout(() => {
        clone.remove();
        cartIcon.classList.add('scale-125', 'rotate-12', 'text-primary-600');
        setTimeout(() => {
            cartIcon.classList.remove('scale-125', 'rotate-12', 'text-primary-600');
        }, 150);
    }, 850);
}

/* DIRECT BUY-NOW FUNCTION */
function buyNowDirect(id, name, price, qty) {
    qty = parseInt(qty);
    if (isNaN(qty) || qty <= 0) {
        showToast("Please enter a valid quantity.", "warning");
        return;
    }

    let cart = JSON.parse(localStorage.getItem("cart")) || [];
    
    // Add or update quantity in permanent cart
    let existingIndex = cart.findIndex(item => item.id === id);
    if (existingIndex !== -1) {
        cart[existingIndex].qty = qty;
    } else {
        cart.push({
            id: id,
            name: name,
            price: parseFloat(price),
            qty: qty
        });
    }

    localStorage.setItem("cart", JSON.stringify(cart));
    window.dispatchEvent(new Event('cartUpdated'));

    // Compile single checkout item
    const checkoutItem = {
        id: id,
        name: name,
        price: parseFloat(price),
        qty: qty
    };
    
    // Store only this item for checkout.php to pull
    localStorage.setItem("checkout_items", JSON.stringify([checkoutItem]));

    // Secure redirect
    window.location.href = 'checkout.php';
}
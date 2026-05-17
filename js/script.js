let cart = JSON.parse(localStorage.getItem("cart")) || [];

function addToCart(id, name, price, qty){
    qty = parseInt(qty);
    if(qty <= 0) return;

    cart.push({
        id: id,
        name:name,
        price:parseFloat(price),
        qty:qty
    });

    localStorage.setItem("cart", JSON.stringify(cart));
    alert("Added to Cart!");
}

/* SEARCH */

let searchInput = document.getElementById("searchInput");

if(searchInput){

searchInput.addEventListener("keyup", function(){

    let value = this.value.toLowerCase();

    let cards = document.querySelectorAll(".card");

    cards.forEach(card => {

        let text = card.innerText.toLowerCase();

        card.style.display =
        text.includes(value)
        ? "block"
        : "none";

    });

});
}

/* CATEGORY FILTERING */
function filterCategory(category) {
    const buttons = document.querySelectorAll('.category-btn');
    buttons.forEach(btn => {
        if (btn.innerText === category) {
            btn.classList.remove('bg-white', 'text-gray-600', 'border-gray-200');
            btn.classList.add('bg-blue-600', 'text-white');
        } else {
            btn.classList.add('bg-white', 'text-gray-600', 'border-gray-200');
            btn.classList.remove('bg-blue-600', 'text-white');
        }
    });

    const grid = document.getElementById('productGrid');
    if(grid) grid.style.opacity = '0.5';

    fetch(`fetch_products.php?category=${encodeURIComponent(category)}`)
        .then(response => response.text())
        .then(html => {
            setTimeout(() => {
                if(grid) {
                    grid.innerHTML = html;
                    grid.style.opacity = '1';
                }
            }, 150);
        });
}
let cart = JSON.parse(localStorage.getItem("cart")) || [];

function addToCart(name, price, qty){

    cart.push({
        name:name,
        price:price,
        qty:qty
    });

    localStorage.setItem("cart", JSON.stringify(cart));

    alert("Added to Cart!");
    window.location='receipt.php';
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
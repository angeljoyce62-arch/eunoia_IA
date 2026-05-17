<!DOCTYPE html>
<html>

<head>

<title>Cart</title>

<link rel="stylesheet" href="style.css">
<link rel="stylesheet" href="ui_improvements.css">
<link rel="stylesheet" href="ui_improvements.css">

</head>

<body>


<div class="logo-container" style="justify-content:center;">
	<img src="images/logo.jpg" class="logo" alt="Eunoia IA Logo">
	<span style="font-size:2em;font-weight:bold;vertical-align:middle;">Shopping Cart</span>
</div>

<div id="cartItems"
class="products"></div>

<div style="text-align:center;">

<h2 id="total"></h2>

<a href="checkout.php">

<button>
Proceed to Checkout
</button>

</a>

</div>

<script>

let cart =
JSON.parse(localStorage.getItem("cart")) || [];

let output = "";

let total = 0;

cart.forEach((item,index)=>{

let subtotal =
item.price * item.qty;

total += subtotal;

output += `

<div class="card">

<h3>${item.name}</h3>

<p>₱${item.price}</p>

<p>Quantity: ${item.qty}</p>

<p>Subtotal: ₱${subtotal}</p>

<button onclick="removeItem(${index})">

Remove

</button>

</div>

`;

});

document.getElementById("cartItems")
.innerHTML = output;

document.getElementById("total")
.innerHTML = "Total: ₱" + total;

function removeItem(index){

cart.splice(index,1);

localStorage.setItem(
"cart",
JSON.stringify(cart)
);

location.reload();

}

</script>

</body>
</html>
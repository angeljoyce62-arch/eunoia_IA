<?php
include 'config.php';

$id = $_GET['id'];

$query = mysqli_query($conn,
"SELECT * FROM products
WHERE id='$id'");

$product = mysqli_fetch_assoc($query);

if(isset($_POST['update'])){

$name = $_POST['name'];
$price = $_POST['price'];
$stock = $_POST['stock'];
$sizes = $_POST['sizes'];
$colors = $_POST['colors'];

mysqli_query($conn,

"UPDATE products SET

product_name='$name',
price='$price',
stock='$stock',
sizes='$sizes',
colors='$colors'

WHERE id='$id'");

header("Location:admin.php");

}
?>

<form method="POST">

<h1>Edit Product</h1>

<input type="text"
name="name"
value="<?php echo $product['product_name']; ?>">

<br><br>

<input type="number"
name="price"
value="<?php echo $product['price']; ?>">

<br><br>

<input type="number"
name="stock"
value="<?php echo $product['stock']; ?>">

<br><br>

<input type="text"
name="sizes"
value="<?php echo $product['sizes']; ?>"
placeholder="S,M,L">

<br><br>

<input type="text"
name="colors"
value="<?php echo $product['colors']; ?>"
placeholder="Black,White">

<br><br>

<button name="update">

Update Product

</button>

</form>
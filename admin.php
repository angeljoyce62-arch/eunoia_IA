<?php
session_start();
if(!isset($_SESSION['admin'])){
    header("Location:admin-login.php");
    exit();
}
include 'config.php';
include 'facebook_util.php';

if(isset($_POST['add'])){
    $name = $_POST['name'];
    $price = $_POST['price'];
    $description = $_POST['description'];
    $image = $_FILES['image']['name'];
    $tmp = $_FILES['image']['tmp_name'];
    move_uploaded_file($tmp, "images/".$image);
    mysqli_query($conn,
        "INSERT INTO products (product_name,price,image,description)
        VALUES ('$name','$price','$image','$description')"
    );
    // Facebook auto-post
    $fb_message = "New Product: $name\n₱$price\n$description";
    $fb_image_url = (isset($image) && $image) ? (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://" . $_SERVER['HTTP_HOST'] . "/images/" . $image : null;
    postToFacebook($fb_message, $fb_image_url);
    echo "Product Added!";
}
// DELETE PRODUCT
if(isset($_GET['delete'])){
    $id = $_GET['delete'];
    mysqli_query($conn, "DELETE FROM products WHERE id='$id'");
}
?>
<a href="edit-product.php?id=<?php echo $row['id']; ?>">

<button>

Edit

</button>

</a>

<!DOCTYPE html>
<html>

<head>

<title>Admin Panel</title>
<link rel="stylesheet" href="style.css">
<link rel="stylesheet" href="ui_improvements.css">

</head>

<body>

<div class="logo-container" style="justify-content:center;">
    <img src="images/logo.jpg" class="logo" alt="Eunoia IA Logo">
    <span style="font-size:2em;font-weight:bold;vertical-align:middle;">Eunoia IA Admin</span>
</div>

<form method="POST"
enctype="multipart/form-data"
style="width:300px;margin:auto;">

    <input type="text"
    name="name"
    placeholder="Product Name"
    required>

    <br><br>

    <input type="number"
    name="price"
    placeholder="Price"
    required>

    <br><br>

    <input type="file"
    name="image"
    required>

    <br><br>

    <textarea
    name="description"
    placeholder="Description"></textarea>

    <br><br>

    <button name="add">
        Add Product
    </button>

</form>

<hr>

<h2 style="text-align:center;">Products</h2>

<div class="products">

<?php

$query = mysqli_query($conn,
"SELECT * FROM products");

while($row = mysqli_fetch_assoc($query)){

?>

<div class="card">

    <img src="images/<?php echo $row['image']; ?>">

    <h3><?php echo $row['product_name']; ?></h3>

    <p>₱<?php echo $row['price']; ?></p>

    <a href="admin.php?delete=<?php echo $row['id']; ?>">

        <button>
            Delete
        </button>

    </a>

</div>

<?php } ?>

</div>

</body>
</html>
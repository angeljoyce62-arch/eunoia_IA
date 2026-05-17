<?php include 'config.php'; ?>

<?php

if(isset($_POST['submit'])){

    $name = $_POST['name'];
    $gcash = $_POST['gcash'];
    $total = $_POST['total'];

    mysqli_query($conn,
    "INSERT INTO orders(customer_name,gcash_number,total)
    VALUES('$name','$gcash','$total')");

    echo "<script>alert('Order Placed!')</script>";
}

?>

<!DOCTYPE html>
<html>

<head>
    <title>Checkout</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="ui_improvements.css">
</head>

<body>


<div class="logo-container" style="justify-content:center;">
    <img src="images/logo.jpg" class="logo" alt="Eunoia IA Logo">
    <span style="font-size:2em;font-weight:bold;vertical-align:middle;">GCash Checkout</span>
</div>

<form method="POST" style="width:300px;margin:auto;">
    <img src="images/gcash.png" style="width:100%;margin-bottom:20px;">
    <input type="text" name="name" placeholder="Full Name" required>
    <br><br>
    <input type="text" name="gcash" placeholder="Your GCash Number" required>
    <br><br>
    <input type="number" name="total" placeholder="Total Amount" required>
    <br><br>
    <button name="submit">Pay Now</button>

<?php

if(isset($_POST['submit'])){

$name = $_POST['name'];
$gcash = $_POST['gcash'];
$total = $_POST['total'];

mysqli_query($conn,

"INSERT INTO orders
(customer_name,gcash_number,total)

VALUES
('$name','$gcash','$total')");

echo "<script>
alert('Order Placed!');
</script>";

}

?>

<!DOCTYPE html>
<html>

<head>

<title>Checkout</title>

<link rel="stylesheet"
href="style.css">

</head>

<body>

<form method="POST"
style="width:300px;margin:auto;">

<h1>GCash Checkout</h1>

<img src="images/gcash.png"
style="width:100%;">

<br><br>

<input type="text"
name="name"
placeholder="Full Name"
required>

<br><br>

<input type="text"
name="gcash"
placeholder="Your GCash Number"
required>

<br><br>

<input type="number"
name="total"
placeholder="Total Amount"
required>

<br><br>

<button name="submit">

Pay Now

</button>

</form>

</body>
</html>

</form>

</body>
</html>
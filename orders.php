<?php include 'config.php'; ?>


<div class="logo-container" style="justify-content:center;">
	<img src="images/logo.jpg" class="logo" alt="Eunoia IA Logo">
	<span style="font-size:2em;font-weight:bold;vertical-align:middle;">Orders</span>
</div>
<link rel="stylesheet" href="ui_improvements.css">

<?php

$query =
mysqli_query($conn,
"SELECT * FROM orders");

while($row=mysqli_fetch_assoc($query)){

?>

<div class="card">

<h3>
<?php echo $row['customer_name']; ?>
</h3>

<p>
GCash:
<?php echo $row['gcash_number']; ?>
</p>

<p>
₱<?php echo $row['total']; ?>
</p>

<p>
<?php echo $row['order_date']; ?>
</p>

</div>

<?php } ?>
<?php
session_start();
if (!isset($_SESSION['user_id']) && !isset($_SESSION['admin'])) {
    // Not logged in, show login/register links
    echo '<!DOCTYPE html><html><head><title>Welcome - Eunoia IA</title>';
    echo '<link rel="stylesheet" href="style.css">';
    echo '<link rel="stylesheet" href="ui_improvements.css">';
    echo '</head><body>';
    echo '<div class="logo-container" style="justify-content:center; margin-top:60px;">';
    echo '<img src="images/logo.jpg" class="logo" alt="Eunoia IA Logo">';
    echo '<span style="font-size:2em;font-weight:bold;vertical-align:middle;">Eunoia IA</span>';
    echo '</div>';
    echo '<div style="text-align:center;margin-top:40px;">';
    echo '<a href="login.php"><button style="margin:10px;">Login</button></a>';
    echo '<a href="register.php"><button style="margin:10px;">Register</button></a>';
    echo '</div>';
    echo '</body></html>';
    exit();
}

include 'config.php';
?>

<!DOCTYPE html>
<html>
<head>
    <title>Eunoia IA</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="ui_improvements.css">
</head>
<body>

<header>
    <div class="logo-container">
        <img src="images/logo.jpg" class="logo" alt="Eunoia IA Logo">
        <span style="font-size:2em;font-weight:bold;vertical-align:middle;">Eunoia IA</span>
    </div>
    <input type="text" id="searchInput" placeholder="Search clothes...">
    <div>
        <a href="cart.php">Cart 🛒</a>
        &nbsp;&nbsp;
        <a href="login.php">Login</a>
    </div>
</header>

<section class="products">
<?php
$query = mysqli_query($conn, "SELECT * FROM products");
while ($row = mysqli_fetch_assoc($query)) {
?>
    <div class="card">
        <img src="images/<?php echo $row['image']; ?>">
        <h3><?php echo $row['product_name']; ?></h3>
        <p>₱<?php echo $row['price']; ?></p>
        <p>Sizes: <?php echo $row['sizes']; ?></p>
        <p>Colors: <?php echo $row['colors']; ?></p>
        <p>Stock: <?php echo $row['stock']; ?></p>
        <input type="number" id="qty<?php echo $row['id']; ?>" value="1" min="1" max="<?php echo $row['stock']; ?>">
        <br><br>
        <button onclick="addToCart(
            '<?php echo $row['product_name']; ?>',
            '<?php echo $row['price']; ?>',
            document.getElementById('qty<?php echo $row['id']; ?>').value
        )">Add To Cart</button>
    </div>
<?php
} // closes while loop properly
?>
</section>

<script src="js/script.js"></script>
</body>
</html>

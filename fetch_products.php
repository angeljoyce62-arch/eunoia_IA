<?php
include 'config.php';

$category = $_GET['category'] ?? 'All';

$sql = "SELECT * FROM products";
if ($category !== 'All') {
    $sql .= " WHERE category = '" . mysqli_real_escape_string($conn, $category) . "'";
}

$query = mysqli_query($conn, $sql);
while ($row = mysqli_fetch_assoc($query)) {
?>
    <div class="card bg-white border border-gray-100 rounded-xl p-4 shadow-sm hover:shadow-md transition-shadow flex flex-col">
        <a href="product_details.php?id=<?php echo $row['id']; ?>" class="block">
            <img src="images/<?php echo $row['image']; ?>" class="w-full h-48 object-cover rounded-lg mb-4">
            <h3 class="text-lg font-bold text-gray-800 mb-1 hover:text-blue-600 transition"><?php echo htmlspecialchars($row['name']); ?></h3>
        </a>
        <p class="text-gray-500 text-sm mb-3 line-clamp-2"><?php echo htmlspecialchars($row['description']); ?></p>
        <div class="mt-auto">
            <div class="flex items-center justify-between mb-4">
                <span class="text-xl font-extrabold text-blue-600">₱<?php echo number_format($row['price'], 2); ?></span>
                <span class="text-xs font-medium px-2 py-1 bg-gray-100 rounded text-gray-600">Stock: <?php echo $row['stock']; ?></span>
            </div>
            <input type="number" id="qty<?php echo $row['id']; ?>" value="1" min="1" max="<?php echo $row['stock']; ?>" class="w-full px-3 py-2 border rounded-lg mb-2 focus:ring-2 focus:ring-blue-500 outline-none">
            <button onclick="addToCart(<?php echo $row['id']; ?>, '<?php echo addslashes($row['name']); ?>', <?php echo $row['price']; ?>, document.getElementById('qty<?php echo $row['id']; ?>').value)" class="w-full bg-blue-600 text-white py-2 rounded-lg font-bold hover:bg-blue-700 transition">Add To Cart</button>
        </div>
    </div>
<?php
}
?>
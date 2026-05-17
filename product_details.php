<?php
include 'config.php';
include 'header.php';

if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$id = $_GET['id'];
$stmt = $conn->prepare("SELECT p.*, u.username as seller_name FROM products p JOIN users u ON p.seller_id = u.id WHERE p.id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$product = $stmt->get_result()->fetch_assoc();

if (!$product) {
    echo "<div class='text-center py-20'><h2 class='text-2xl font-bold text-red-600'>Product Not Found</h2></div>";
    include 'footer.php';
    exit();
}
?>

<div class="max-w-5xl mx-auto py-8">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden flex flex-col md:flex-row">
        <div class="md:w-1/2 p-4">
            <img src="images/<?php echo $product['image']; ?>" class="w-full h-96 object-cover rounded-xl" alt="<?php echo htmlspecialchars($product['name']); ?>">
        </div>
        <div class="md:w-1/2 p-8 flex flex-col">
            <div class="mb-6">
                <span class="text-xs font-bold text-blue-600 bg-blue-50 px-3 py-1 rounded-full uppercase tracking-wider"><?php echo htmlspecialchars($product['category']); ?></span>
                <h1 class="text-4xl font-black text-gray-900 mt-2"><?php echo htmlspecialchars($product['name']); ?></h1>
                <p class="text-sm text-gray-500 mt-1">Sold by: <span class="font-medium text-gray-800"><?php echo htmlspecialchars($product['seller_name']); ?></span></p>
            </div>

            <div class="mb-8">
                <h2 class="text-gray-400 uppercase text-xs font-bold mb-2">Description</h2>
                <p class="text-gray-600 leading-relaxed"><?php echo nl2br(htmlspecialchars($product['description'])); ?></p>
            </div>

            <div class="mt-auto">
                <div class="flex items-center justify-between mb-6">
                    <span class="text-3xl font-black text-blue-600">₱<?php echo number_format($product['price'], 2); ?></span>
                    <span class="text-sm font-medium text-gray-500">Stock available: <span class="text-gray-900"><?php echo $product['stock']; ?></span></span>
                </div>
                <div class="flex gap-4">
                    <input type="number" id="detailQty" value="1" min="1" max="<?php echo $product['stock']; ?>" class="w-24 px-4 py-3 border rounded-xl focus:ring-2 focus:ring-blue-500 outline-none">
                    <button onclick="addToCart(<?php echo $product['id']; ?>, '<?php echo addslashes($product['name']); ?>', <?php echo $product['price']; ?>, document.getElementById('detailQty').value)" class="flex-1 bg-blue-600 text-white py-4 rounded-xl font-bold hover:bg-blue-700 transition shadow-lg shadow-blue-100">Add to Cart</button>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
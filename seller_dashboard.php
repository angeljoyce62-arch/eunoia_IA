<?php
include 'config.php';
include 'header.php';

if(!isset($_SESSION['role']) || $_SESSION['role'] !== 'seller'){
    header("Location: login.php");
    exit();
}

$seller_id = $_SESSION['user_id'];

// Add Product Logic
if(isset($_POST['add'])){
    $name = $_POST['name'];
    $price = $_POST['price'];
    $description = $_POST['description'];
    $stock = $_POST['stock'];
    $image = $_FILES['image']['name'];
    $tmp = $_FILES['image']['tmp_name'];
    
    if(move_uploaded_file($tmp, "images/".$image)){
        $stmt = $conn->prepare("INSERT INTO products (name, price, image, description, stock, seller_id) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sdssii", $name, $price, $image, $description, $stock, $seller_id);
        $stmt->execute();
        $msg = "Product added successfully!";
    }
}

// Deletion Logic (Security: Only delete if product belongs to this seller)
if(isset($_GET['delete'])){
    $prod_id = $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM products WHERE id = ? AND seller_id = ?");
    $stmt->bind_param("ii", $prod_id, $seller_id);
    $stmt->execute();
    header("Location: seller_dashboard.php");
}

// Fetch Stats
$stats_query = mysqli_query($conn, "SELECT COUNT(*) as total, SUM(stock) as total_stock FROM products WHERE seller_id = $seller_id");
$stats = mysqli_fetch_assoc($stats_query);
?>

<div class="max-w-6xl mx-auto">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-800">Seller Dashboard</h1>
        <p class="text-gray-600">Manage your shop inventory and track your sales.</p>
    </div>

    <!-- Stats Bar -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
            <p class="text-sm text-gray-500 font-medium uppercase">Total Products</p>
            <p class="text-2xl font-bold text-gray-900"><?php echo $stats['total'] ?? 0; ?></p>
        </div>
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
            <p class="text-sm text-gray-500 font-medium uppercase">Inventory Volume</p>
            <p class="text-2xl font-bold text-blue-600"><?php echo $stats['total_stock'] ?? 0; ?> units</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Add Product Form -->
        <div class="lg:col-span-1">
            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                <h2 class="text-xl font-bold mb-4">Add New Item</h2>
                <?php if(isset($msg)) echo "<p class='text-green-600 mb-4 text-sm'>$msg</p>"; ?>
                <form method="POST" enctype="multipart/form-data" class="space-y-4">
                    <input type="text" name="name" placeholder="Product Name" required class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
                    <input type="number" step="0.01" name="price" placeholder="Price (₱)" required class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
                    <input type="number" name="stock" placeholder="Stock Quantity" required class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
                    <textarea name="description" placeholder="Short Description" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none h-24"></textarea>
                    <input type="file" name="image" required class="w-full text-sm">
                    <button name="add" class="w-full bg-blue-600 text-white py-2 rounded-lg font-bold hover:bg-blue-700 transition">List Product</button>
                </form>
            </div>
        </div>

        <!-- Inventory List -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-gray-50 border-b">
                        <tr>
                            <th class="px-6 py-4 font-bold text-gray-700 text-sm">Product</th>
                            <th class="px-6 py-4 font-bold text-gray-700 text-sm text-center">Stock</th>
                            <th class="px-6 py-4 font-bold text-gray-700 text-sm">Price</th>
                            <th class="px-6 py-4 font-bold text-gray-700 text-sm text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <?php
                        $stmt = $conn->prepare("SELECT * FROM products WHERE seller_id = ? ORDER BY id DESC");
                        $stmt->bind_param("i", $seller_id);
                        $stmt->execute();
                        $res = $stmt->get_result();
                        while($row = $res->fetch_assoc()){
                        ?>
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4 flex items-center gap-3">
                                <img src="images/<?php echo $row['image']; ?>" class="w-10 h-10 rounded object-cover border">
                                <span class="font-medium text-gray-800"><?php echo htmlspecialchars($row['name']); ?></span>
                            </td>
                            <td class="px-6 py-4 text-center text-sm font-semibold <?php echo $row['stock'] < 5 ? 'text-red-500' : 'text-gray-600'; ?>"><?php echo $row['stock']; ?></td>
                            <td class="px-6 py-4 font-bold text-blue-600">₱<?php echo number_format($row['price'], 2); ?></td>
                            <td class="px-6 py-4 text-right">
                                <a href="seller_dashboard.php?delete=<?php echo $row['id']; ?>" onclick="return confirm('Remove this listing?')" class="text-red-500 hover:text-red-700 text-sm font-bold">Remove</a>
                            </td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Order Management Section -->
    <div class="mt-12">
        <h2 class="text-2xl font-bold text-gray-800 mb-6">Recent Orders</h2>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <table class="w-full text-left border-collapse">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="px-6 py-4 font-bold text-gray-700 text-sm">Order ID</th>
                        <th class="px-6 py-4 font-bold text-gray-700 text-sm">Customer</th>
                        <th class="px-6 py-4 font-bold text-gray-700 text-sm">Product</th>
                        <th class="px-6 py-4 font-bold text-gray-700 text-sm">Qty</th>
                        <th class="px-6 py-4 font-bold text-gray-700 text-sm">Status</th>
                        <th class="px-6 py-4 font-bold text-gray-700 text-sm text-right">Date</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php
                    $order_query = "SELECT o.id, o.status, o.created_at, u.username, oi.quantity, p.name 
                                   FROM orders o 
                                   JOIN order_items oi ON o.id = oi.order_id 
                                   JOIN products p ON oi.product_id = p.id 
                                   JOIN users u ON o.user_id = u.id 
                                   WHERE p.seller_id = ? 
                                   ORDER BY o.created_at DESC";
                    $stmt_orders = $conn->prepare($order_query);
                    $stmt_orders->bind_param("i", $seller_id);
                    $stmt_orders->execute();
                    $res_orders = $stmt_orders->get_result();
                    while($order = $res_orders->fetch_assoc()){
                    ?>
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4 text-sm font-bold text-gray-900">#<?php echo $order['id']; ?></td>
                        <td class="px-6 py-4 text-sm text-gray-600"><?php echo htmlspecialchars($order['username']); ?></td>
                        <td class="px-6 py-4 text-sm text-gray-800 font-medium"><?php echo htmlspecialchars($order['name']); ?></td>
                        <td class="px-6 py-4 text-sm text-gray-600"><?php echo $order['quantity']; ?></td>
                        <td class="px-6 py-4"><span class="px-2 py-1 text-xs font-bold rounded-full bg-yellow-100 text-yellow-700"><?php echo $order['status']; ?></span></td>
                        <td class="px-6 py-4 text-sm text-gray-500 text-right"><?php echo date('M d, Y', strtotime($order['created_at'])); ?></td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php include 'footer.php'; ?>
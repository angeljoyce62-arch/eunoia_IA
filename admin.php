<?php
if(!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin'){
    header("Location: login.php");
    exit();
}
include 'config.php';
include 'facebook_util.php';
include 'header.php';

// ADD PRODUCT (Prepared Statement)
if(isset($_POST['add'])){
    $name = $_POST['name'];
    $price = $_POST['price'];
    $description = $_POST['description'];
    $stock = $_POST['stock'] ?? 0;
    $image = $_FILES['image']['name'];
    $tmp = $_FILES['image']['tmp_name'];
    
    if(move_uploaded_file($tmp, "images/".$image)){
        $stmt = $conn->prepare("INSERT INTO products (name, price, image, description, stock, seller_id) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sdssii", $name, $price, $image, $description, $stock, $_SESSION['user_id']);
        $stmt->execute();

        // Facebook auto-post
        $fb_message = "New Product: $name\n₱$price\n$description";
        $fb_image_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://" . $_SERVER['HTTP_HOST'] . "/images/" . $image;
        postToFacebook($fb_message, $fb_image_url);
        $msg = "Product Added Successfully!";
    }
}

// DELETE PRODUCT (Prepared Statement)
if(isset($_GET['delete'])){
    $id = $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM products WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    header("Location: admin.php");
    exit();
}
?>

<div class="max-w-6xl mx-auto">
    <div class="flex justify-between items-center mb-8">
        <h1 class="text-3xl font-bold text-gray-800">Global Administration</h1>
        <div class="bg-blue-100 text-blue-800 px-4 py-2 rounded-lg font-medium">Administrator Mode</div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <div class="lg:col-span-1">
            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                <h2 class="text-xl font-bold mb-4">Add Platform Product</h2>
                <?php if(isset($msg)) echo "<p class='text-green-600 mb-4 text-sm'>$msg</p>"; ?>
                <form method="POST" enctype="multipart/form-data" class="space-y-4">
                    <input type="text" name="name" placeholder="Product Name" required class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
                    <input type="number" step="0.01" name="price" placeholder="Price" required class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
                    <input type="number" name="stock" placeholder="Initial Stock" required class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
                    <textarea name="description" placeholder="Description" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none h-24"></textarea>
                    <input type="file" name="image" required class="text-sm">
                    <button name="add" class="w-full bg-blue-600 text-white py-2 rounded-lg font-bold hover:bg-blue-700 transition">Upload Product</button>
                </form>
            </div>
        </div>

        <div class="lg:col-span-2">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-gray-50 border-b">
                        <tr>
                            <th class="px-6 py-4 font-bold text-gray-700 text-sm">Product</th>
                            <th class="px-6 py-4 font-bold text-gray-700 text-sm">Seller ID</th>
                            <th class="px-6 py-4 font-bold text-gray-700 text-sm text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <?php
                        $query = mysqli_query($conn, "SELECT * FROM products ORDER BY id DESC");
                        while($row = mysqli_fetch_assoc($query)){
                        ?>
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4 flex items-center gap-3">
                                <img src="images/<?php echo $row['image']; ?>" class="w-10 h-10 rounded object-cover border">
                                <span class="font-medium text-gray-800"><?php echo htmlspecialchars($row['name']); ?></span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">User #<?php echo $row['seller_id']; ?></td>
                            <td class="px-6 py-4 text-right">
                                <a href="admin.php?delete=<?php echo $row['id']; ?>" onclick="return confirm('Delete this product?')" class="text-red-500 hover:text-red-700 text-sm font-bold">Delete</a>
                            </td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php include 'footer.php'; ?>
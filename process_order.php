<?php
session_start();
include 'config.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Please login first.']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$cart = $data['cart'] ?? [];
$total = $data['total'] ?? 0;
$customer_name = $data['customer_name'] ?? '';
$gcash_number = $data['gcash_number'] ?? '';
$payment_method = $data['payment_method'] ?? 'GCash';
$delivery_address = $data['delivery_address'] ?? null;
$user_id = $_SESSION['user_id'];

if (empty($cart)) {
    echo json_encode(['success' => false, 'message' => 'Your checkout list is empty.']);
    exit;
}

if (empty($customer_name)) {
    echo json_encode(['success' => false, 'message' => 'Customer name is required.']);
    exit;
}

if (empty($gcash_number)) {
    echo json_encode(['success' => false, 'message' => 'Contact phone number is required.']);
    exit;
}

if ($payment_method === 'COD' && empty($delivery_address)) {
    echo json_encode(['success' => false, 'message' => 'Delivery address is required for Cash on Delivery orders.']);
    exit;
}

// Start Transaction
mysqli_begin_transaction($conn);

try {
    // 1. Create Order including newly migrated details
    $stmt = $conn->prepare("INSERT INTO orders (user_id, customer_name, gcash_number, total, status, payment_method, delivery_address) VALUES (?, ?, ?, ?, 'Pending', ?, ?)");
    $stmt->bind_param("issdss", $user_id, $customer_name, $gcash_number, $total, $payment_method, $delivery_address);
    $stmt->execute();
    $order_id = $conn->insert_id;

    // 2. Create Order Items & Update Stock
    foreach ($cart as $item) {
        $itemId = $item['id'];
        $itemQty = $item['qty'];
        $itemPrice = $item['price'];

        // Insert item details into order_items
        $stmt_item = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
        $stmt_item->bind_param("iiid", $order_id, $itemId, $itemQty, $itemPrice);
        $stmt_item->execute();

        // Update active stock in products (ensure it doesn't drop below 0 by checking)
        $stmt_stock = $conn->prepare("UPDATE products SET stock = GREATEST(0, stock - ?) WHERE id = ?");
        $stmt_stock->bind_param("ii", $itemQty, $itemId);
        $stmt_stock->execute();
    }

    mysqli_commit($conn);
    echo json_encode(['success' => true, 'order_id' => $order_id]);

} catch (Exception $e) {
    mysqli_rollback($conn);
    echo json_encode(['success' => false, 'message' => 'Failed to process transaction. Exception: ' . $e->getMessage()]);
}
?>
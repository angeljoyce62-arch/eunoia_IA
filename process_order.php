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
$user_id = $_SESSION['user_id'];

if (empty($cart)) {
    echo json_encode(['success' => false, 'message' => 'Cart is empty.']);
    exit;
}

// Start Transaction
mysqli_begin_transaction($conn);

try {
    // 1. Create Order
    $stmt = $conn->prepare("INSERT INTO orders (user_id, total, status) VALUES (?, ?, 'Pending')");
    $stmt->bind_param("id", $user_id, $total);
    $stmt->execute();
    $order_id = $conn->insert_id;

    // 2. Create Order Items & Update Stock
    foreach ($cart as $item) {
        // Insert item
        $stmt_item = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
        $stmt_item->bind_param("iiid", $order_id, $item['id'], $item['qty'], $item['price']);
        $stmt_item->execute();

        // Update stock
        $stmt_stock = $conn->prepare("UPDATE products SET stock = stock - ? WHERE id = ?");
        $stmt_stock->bind_param("ii", $item['qty'], $item['id']);
        $stmt_stock->execute();
    }

    mysqli_commit($conn);
    echo json_encode(['success' => true, 'order_id' => $order_id]);

} catch (Exception $e) {
    mysqli_rollback($conn);
    echo json_encode(['success' => false, 'message' => 'Failed to process order.']);
}
?>
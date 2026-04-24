<?php
require_once '../config/db.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $product_id = $_POST['product_id'] ?? null;
    $quantity = intval($_POST['quantity'] ?? 0);
    $note = $_POST['note'] ?? 'Nhập hàng bổ sung';

    if (!$product_id || $quantity <= 0) {
        echo json_encode(['status' => 'error', 'message' => 'Dữ liệu không hợp lệ!']);
        exit;
    }

    try {
        $pdo->beginTransaction();

        // 1. Cập nhật số lượng trong bảng products
        $sqlUp = "UPDATE products SET stock_quantity = ? WHERE id = ?";
        $pdo->prepare($sqlUp)->execute([$quantity, $product_id]);

        // 2. Ghi lịch sử vào bảng inventory_log
        $sqlLog = "INSERT INTO inventory_log (product_id, type, quantity, note) VALUES (?, 'import', ?, ?)";
        $pdo->prepare($sqlLog)->execute([$product_id, $quantity, $note]);

        $pdo->commit();
        echo json_encode(['status' => 'success', 'message' => 'Nhập hàng thành công!']);

    } catch (Exception $e) {
        $pdo->rollBack();
        echo json_encode(['status' => 'error', 'message' => 'Lỗi: ' . $e->getMessage()]);
    }
}
?>
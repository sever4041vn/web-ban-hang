<?php
require_once '../config/db.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $order_id = $_POST['order_id'] ?? null;
    $date_custom = $_POST['date_custom'] ?? null;

    if (!$order_id || !$date_custom ) {
        echo json_encode(['status' => 'error', 'message' => 'Dữ liệu không hợp lệ!']);
        exit;
    }

    try {
        $pdo->beginTransaction();

        // 1. Cập nhật ngày trong hóa đơn
        $sqlUp = "UPDATE orders SET date_custom = ? WHERE id = ?";
        $pdo->prepare($sqlUp)->execute([$date_custom, $order_id]);

        $pdo->commit();
        echo json_encode(['status' => 'success', 'message' => 'Cập nhật thành công!']);

    } catch (Exception $e) {
        $pdo->rollBack();
        echo json_encode(['status' => 'error', 'message' => 'Lỗi: ' . $e->getMessage()]);
    }
}
?>
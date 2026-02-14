<?php
require_once '../config/db.php';
header('Content-Type: application/json');

try {
    // Lấy danh sách hóa đơn mới nhất lên đầu
    $sql = "SELECT o.*, c.name as customer_name 
            FROM orders o 
            LEFT JOIN customers c ON o.customer_id = c.id 
            ORDER BY o.created_at DESC";
    
    $stmt = $pdo->query($sql);
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode(['status' => 'error', 'message' => json_encode($orders)]);
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
?>
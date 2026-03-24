<?php
require_once '../config/db.php';
header('Content-Type: application/json');

try {
    $invoice_no = $_GET["invoice_no"]?? 0;
    $customer_name = $_GET["customer_name"]?? "";
    $orders;
    $items;
    // Lấy danh sách hóa đơn mới nhất lên đầu
    if ($invoice_no) {
        $sql = "SELECT o.*, c.name as customer_name 
                FROM orders o 
                LEFT JOIN customers c ON o.customer_id = c.id 
                WHERE o.invoice_no = ?";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$invoice_no]);
        $orders = $stmt->fetch(PDO::FETCH_ASSOC);

        $itemSql = "SELECT *
                FROM order_items 
                WHERE order_id = ?";
        $itemStmt = $pdo->prepare($itemSql);
        $itemStmt->execute([$orders["id"]]);
        $items = $itemStmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode(['status' => 'success', 'message' => json_encode($orders), 'items'=>json_encode($items)]);

    }else{

        $sql = "SELECT o.*, c.name as customer_name 
                FROM orders o 
                LEFT JOIN customers c ON o.customer_id = c.id 
                WHERE c.name LIKE ? OR c.id LIKE ?
                ORDER BY o.created_at DESC";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute(["%$customer_name%","%$customer_name%"]);
        $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode(['status' => 'success', 'message' => json_encode($orders)]);
    }
    
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
?>
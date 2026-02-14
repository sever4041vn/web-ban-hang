<?php
require_once '../config/db.php';

try {

    $type = $_GET['type'] ?? 'import';
    
    $sql = "SELECT l.*, p.name as product_name 
        FROM inventory_log l 
        JOIN products p ON l.product_id = p.id 
        WHERE l.type = ? 
        ORDER BY l.created_at DESC LIMIT 10";

$stmt = $pdo->prepare($sql);
$stmt->execute([$type]);
echo json_encode(['status' => 'success', 'message' => json_encode($stmt->fetchAll(PDO::FETCH_ASSOC))]);
die;

} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}

?>
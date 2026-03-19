<?php
require_once '../config/db.php';

try {
    $search = $_GET['search'] ?? '';
    //Lấy sản phẩm
    if ($search) {
        $stmt = $pdo->prepare("SELECT * FROM products WHERE (name LIKE ? OR sku LIKE ?) ORDER BY id DESC");
        $stmt->execute(["%$search%", "%$search%"]);
    } else {
        $stmt = $pdo->query("SELECT * FROM products WHERE NOT id = 1 ORDER BY id DESC");
    }

    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['status' => 'success', 'message' => json_encode($products)]);
    die;


} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}

?>
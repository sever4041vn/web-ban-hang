<?php
require_once '../config/db.php';

try {
    $search = $_GET['search'] ?? '';
    //Lấy sản phẩm
    if ($search) {
        $stmt = $pdo->prepare("SELECT * FROM customers WHERE (id LIKE ? OR name LIKE ?) ORDER BY id DESC");
        $stmt->execute(["%$search%", "%$search%"]);
    } else {
        $stmt = $pdo->query("SELECT * FROM customers ORDER BY id DESC");
    }

    $customers = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['status' => 'success', 'message' => json_encode($customers)]);
    die;


} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}

?>
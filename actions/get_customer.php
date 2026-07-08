<?php
require_once '../config/db.php';
header('Content-Type: application/json');

try {
    $search = trim($_GET['search'] ?? '');
    // Giới hạn số dòng trả về: trước đây khi mở trang Khách hàng, API này
    // trả về TOÀN BỘ bảng customers không giới hạn -> càng nhiều khách hàng
    // càng lag khi mở trang / gõ tìm kiếm. LIMIT 100 vẫn đủ dùng cho UI hiện tại.
    if ($search !== '') {
        $stmt = $pdo->prepare("SELECT * FROM customers WHERE (id LIKE ? OR name LIKE ?) ORDER BY id DESC LIMIT 100");
        $stmt->execute(["%$search%", "%$search%"]);
    } else {
        $stmt = $pdo->query("SELECT * FROM customers ORDER BY id DESC LIMIT 100");
    }

    $customers = $stmt->fetchAll();

    echo json_encode(['status' => 'success', 'message' => json_encode($customers)]);

} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
?>
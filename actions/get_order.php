<?php
require_once '../config/db.php';
header('Content-Type: application/json');

try {
    $invoice_no = $_GET["invoice_no"] ?? 0;
    $customer_name = trim($_GET["customer_name"] ?? "");

    // Phân trang: trước đây API này luôn trả về TOÀN BỘ bảng orders
    // (không LIMIT) mỗi khi mở trang Đơn hàng hoặc gõ tìm kiếm.
    // Đây là nguyên nhân chính gây khựng/lag khi số lượng đơn hàng tăng lên,
    // vì trình duyệt phải nhận + parse + dựng HTML cho hàng nghìn dòng cùng lúc.
    $page = $_GET['page'] ?? 1;
    if ($page === "" || $page === "undefined" || $page <= 0) {
        $page = 1;
    }
    $limit = 30;
    $offset = ($page - 1) * $limit;

    if ($invoice_no) {
        $sql = "SELECT o.*, c.name as customer_name_real 
                FROM orders o 
                LEFT JOIN customers c ON o.customer_id = c.id 
                WHERE o.invoice_no = ?";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([$invoice_no]);
        $orders = $stmt->fetch();

        $items = [];
        if ($orders) {
            $itemSql = "SELECT * FROM order_items WHERE order_id = ?";
            $itemStmt = $pdo->prepare($itemSql);
            $itemStmt->execute([$orders["id"]]);
            $items = $itemStmt->fetchAll();
        }
        echo json_encode(['status' => 'success', 'message' => json_encode($orders), 'items' => json_encode($items)]);

    } else {
        // Lưu ý: PDO không cho phép trộn placeholder có tên (:limit) với placeholder
        // dấu chấm hỏi (?) trong cùng 1 câu lệnh, nên dùng toàn bộ placeholder có tên.
        $sql = "SELECT o.*, c.name as customer_name_real 
                FROM orders o 
                LEFT JOIN customers c ON o.customer_id = c.id 
                WHERE o.customer_name LIKE :cn1 OR c.id LIKE :cn2
                ORDER BY o.created_at DESC
                LIMIT :limit OFFSET :offset";

        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':cn1', "%$customer_name%");
        $stmt->bindValue(':cn2', "%$customer_name%");
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        $orders = $stmt->fetchAll();
        echo json_encode(['status' => 'success', 'message' => json_encode($orders)]);
    }

} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
?>
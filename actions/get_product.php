<?php
require_once '../config/db.php';
header('Content-Type: application/json');

// Chỉ lấy đúng cột frontend cần, giảm dung lượng JSON trả về
// (bỏ category_id vì không dùng ở các trang đang gọi API này)
const PRODUCT_COLUMNS = "id, sku, name, unit, cost_price, selling_price_1, selling_price_2, stock_quantity";

try {
    $search = trim($_GET['search'] ?? '');
    $page = $_GET['page'] ?? 1;
    if ($page === "" || $page === "undefined" || $page <= 0) {
        $page = 1;
    }
    $limit = 25;

    if ($search !== '') {
        // Giới hạn kết quả tìm kiếm để tránh trả về hàng nghìn dòng cùng lúc
        // (nếu người dùng gõ 1-2 ký tự phổ biến) làm chậm cả DB lẫn render UI
        $stmt = $pdo->prepare("SELECT " . PRODUCT_COLUMNS . " FROM products WHERE (name LIKE ? OR sku LIKE ?) ORDER BY id DESC LIMIT 50");
        $stmt->execute(["%$search%", "%$search%"]);
    } else {
        $offset = ($page - 1) * $limit;
        $stmt = $pdo->prepare("SELECT " . PRODUCT_COLUMNS . " FROM products WHERE id <> 1 ORDER BY id DESC LIMIT :limit OFFSET :offset");
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
    }

    $products = $stmt->fetchAll();

    echo json_encode(['status' => 'success', 'message' => json_encode($products)]);

} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
?>
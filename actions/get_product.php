<?php
require_once '../config/db.php';

try {
    $search = $_GET['search'] ?? '';
    $page = $_GET['page'] ?? 1;
    if ($page=="") {
        $page=1;
    }elseif ($page<=0){
        $page=1;
    }elseif ($page=="undefined"){
        $page=1;
    }
    //Lấy sản phẩm
    if ($search) {
        $stmt = $pdo->prepare("SELECT * FROM products WHERE (name LIKE ? OR sku LIKE ?) ORDER BY id DESC");
        $stmt->execute(["%$search%", "%$search%"]);
    } else {
        $limit = 25;
        $offset = ($page-1)*$limit;
        $stmt = $pdo->prepare("SELECT * FROM products WHERE NOT id = 1 ORDER BY id DESC LIMIT :limit OFFSET :offset");
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
    }

    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['status' => 'success', 'message' => json_encode($products)]);
    die;


} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}

?>
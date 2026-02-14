<?php
require_once '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $sku = $_POST['sku'];
    $name = $_POST['name'];
    $category_id = $_POST['category'];
    $cost_price = $_POST['cost_price'];
    $selling_price = $_POST['selling_price'];
    $unit = $_POST['unit'];

    if ($sku=="" || $name =="" || $category_id=="" || $cost_price =="" || $selling_price =="" || $unit=="") {
        echo json_encode(['status' => 'error', 'message' => "Vui lòng nhập đầy đủ trường thông tin"]);
        die;
    }

    try {
        // 1. Kiểm tra SKU đã tồn tại chưa
        $check = $pdo->prepare("SELECT id FROM products WHERE sku = ?");
        $check->execute([$sku]);
        
        if ($check->rowCount() > 0) {
            echo json_encode(['status' => 'error', 'message' => "Mã sản phẩm đã có vui lòng thử lại"]);
            die;
        }

        // 2. Chèn dữ liệu mới
        $sql = "INSERT INTO products (sku, name, category_id, cost_price, selling_price, unit) 
                VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$sku, $name, $category_id, $cost_price, $selling_price, $unit]);

        echo json_encode(['status' => 'success', 'message' => "Thêm thành công"]);
        die;


    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
}
?>
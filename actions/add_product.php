<?php
require_once '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $sku = isset($_POST['sku'])?$_POST['sku']:"";
    $name = isset($_POST['name'])?$_POST['name']:"";
    $category_id = isset($_POST['category'])?$_POST['category']:0;
    $cost_price = isset($_POST['cost_price'])?$_POST['cost_price']:0;
    $selling_price_1 = isset($_POST['selling_price_1'])?$_POST['selling_price_1']:0;
    $selling_price_2 = isset($_POST['selling_price_2'])?$_POST['selling_price_2']:0;
    $unit = isset($_POST['unit'])?$_POST['unit']:"";

    if ($sku=="" || $name =="" || $category_id=="" || $cost_price =="" || $selling_price_1 =="" || $selling_price_2 =="" || $unit=="") {
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
        $sql = "INSERT INTO products (sku, name, category_id, cost_price, selling_price_1, selling_price_2, unit) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$sku, $name, $category_id, $cost_price, $selling_price_1, $selling_price_2, $unit]);

        echo json_encode(['status' => 'success', 'message' => "Thêm thành công"]);
        die;


    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
}
?>
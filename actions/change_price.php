<?php
require_once '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $product_id = $_POST['product_id'];
    $cost_price = $_POST['cost_price'];
    $selling_price_1 = $_POST['selling_price_1'];
    $selling_price_2 = $_POST['selling_price_2'];


    if ($product_id=="" || $cost_price =="" || $selling_price_1=="" || $selling_price_2=="") {
        echo json_encode(['status' => 'error', 'message' => "Vui lòng nhập đầy đủ trường thông tin"]);
        die;
    }

    try {
        // 1. Cập nhật giá
        $sql = "UPDATE products SET `cost_price` = ?, `selling_price_1` = ?, `selling_price_2` = ? WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$cost_price, $selling_price_1, $selling_price_2,$product_id]);

        echo json_encode(['status' => 'success', 'message' => "Chỉnh sửa giá thành công"]);
        die;


    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
}
?>
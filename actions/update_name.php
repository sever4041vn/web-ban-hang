<?php
require_once '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $product_id = $_POST['product_id'];
    $product_name = $_POST['product_name'];


    if ($product_id=="" || $product_name =="") {
        echo json_encode(['status' => 'error', 'message' => "Vui lòng nhập đầy đủ trường thông tin"]);
        die;
    }

    try {
        // 1. Cập nhật giá
        $sql = "UPDATE products SET `name` = ? WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$product_name ,$product_id]);

        echo json_encode(['status' => 'success', 'message' => "Chỉnh sửa tên hàng thành công"]);
        die;


    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
}
?>
<?php
require_once '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $sku = $_POST['sku'];
    $name = $_POST['name'];

    if ($sku=="" || $name =="") {
        echo json_encode(['status' => 'error', 'message' => "Vui lòng nhập đầy đủ trường thông tin"]);
        die;
    }

    try {
        // 1. Kiểm tra SKU đã tồn tại chưa
        $check = $pdo->prepare("DELETE FROM products WHERE sku = ? AND name = ?");
        $check->execute([$sku,$name]);

        echo json_encode(['status' => 'success', 'message' => "Xóa thành công"]);
        die;


    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
}
?>
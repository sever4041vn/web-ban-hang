<?php
require_once '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $sku = $_POST['sku'];
    $name = $_POST['name'];
    $id = $_POST['id'];

    if ($sku=="" || $name =="" || $id == "") {
        echo json_encode(['status' => 'error', 'message' => "Vui lòng nhập đầy đủ trường thông tin"]);
        die;
    }
    $pdo->beginTransaction();
    try {

        //Thay đổi dữ liệu
        $stmtUpdate = $pdo->prepare("UPDATE `order_items` SET `product_id`= 0 WHERE `product_id` = ?");
        $stmtUpdate->execute([$id]);
        
        $stmtDelete = $pdo->prepare("DELETE FROM `inventory_log` WHERE `product_id` = ?;");
        $stmtDelete->execute([$id]);

        if(!$stmtUpdate||!$stmtDelete){
            echo json_encode(['status' => 'error', 'message' => "Xóa không thành công"]);
            die;
        }
        
        // 3. Xóa
        $delete = $pdo->prepare("DELETE FROM products WHERE id = ?");
        $delete->execute([$id]);
        $pdo->commit();
        echo json_encode(['status' => 'success', 'message' => "Xóa thành công"]);
        die;


    } catch (PDOException $e) {
        $pdo->commit();
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
}
?>
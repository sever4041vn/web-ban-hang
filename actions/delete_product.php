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

    try {
        // 1. Kiểm tra sản phẩm đã có tồn tại trong hoá đơn 
        $Check = $pdo->prepare("SELECT id FROM order_items WHERE id = ?");
        $Check->execute([$id]);
        
        if ($Check->rowCount() > 0) {
            $sqlUpdate = "UPDATE `order_items` SET `product_id`= 0 WHERE `product_id` = ?;
                            DELETE FROM `inventory_log` WHERE `product_id` = ?;
            
            ";
            $stmtUpdate = $pdo->prepare($sqlUpdate);
            $sqlUpdate->execute([$id,$id]);
            
            if(!$stmtUpdate){
                echo json_encode(['status' => 'error', 'message' => "Xóa không thành công"]);
                die;
            }
        }
        
        // 3. Xóa
        $delete = $pdo->prepare("DELETE FROM products WHERE sku = ? AND name = ?");
        $delete->execute([$sku,$name]);

        echo json_encode(['status' => 'success', 'message' => "Xóa thành công"]);
        die;


    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
}
?>
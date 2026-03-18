<?php
require_once '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $unit = isset($_POST['unit'])?$_POST['unit']:"";

    if ($unit=="") {
        echo json_encode(['status' => 'error', 'message' => "Vui lòng nhập đầy đủ trường thông tin"]);
        die;
    }

    try {
        // 1. Chèn dữ liệu mới
        $sql = "INSERT INTO unit (name) 
                VALUES (?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$unit]);

        echo json_encode(['status' => 'success', 'message' => "Thêm thành công"]);
        die;


    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
}
?>
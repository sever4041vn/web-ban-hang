<?php
require_once '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
try {
    $message = array();
    $file = $_FILES["file"]["tmp_name"];
    if (($handle = fopen($file,"r"))) {
            //Bỏ qua hàng đầu
            fgetcsv($handle, 1000, ",");
    
            $pdo->beginTransaction();
    
            $sqlAdd = "INSERT INTO products (sku, name, stock_quantity, unit, cost_price, selling_price) 
                    VALUES (?, ?, ?, ?, ?, ?)";
            $stmtAdd = $pdo->prepare($sqlAdd);
            $sqlUpdate = "UPDATE `products` SET name = ?, stock_quantity=stock_quantity+?, unit=?, cost_price=?, selling_price=? WHERE sku = ?";
            $stmtUpdate = $pdo->prepare($sqlUpdate);
            $successRow = 0;
            $errorRow = 0;
            //MÃ,TÊN HÀNG HOÁ,Số Lượng,ĐTV,Gia nhập, GIÁ BÁN
            while ($data = fgetcsv($handle, 1000, ",")) {
                if ($data[0]!="") {
                    $sku = $data[0];
                    $name = $data[1];
                    $quanity = $data[2];
                    $unit = $data[3];
                    $cost_price = str_replace(['.', ','], '', $data[4]);
                    $selling_price = str_replace(['.', ','], '', $data[5]);
                    // 1. Kiểm tra SKU đã tồn tại chưa
                    $check = $pdo->prepare("SELECT id FROM products WHERE sku = ?");
                    $check->execute([$sku]);
                    if ($check->rowCount() > 0) {
                        // 2. Cập nhật dữ liệu mới
                        $stmtUpdate->execute([$name,$quanity,$unit,$cost_price,$selling_price,$sku]);
                        $errorRow++;
                        array_push($message,"$sku đã cập nhật thàng công");
                    }else{
                        // 2. Chèn dữ liệu mới
                        $stmtAdd->execute([$sku,$name,$quanity,$unit,$cost_price,$selling_price]);
                        $successRow++;
                        array_push($message,"$sku đã thêm thàng công");
                    }
                }else{
                    break;
                }
            }
            $pdo->commit();
            echo json_encode(['status' => 'success', 'message' => "Đã thêm $successRow, đã cập nhật $errorRow", "proudcts" => $message]);
            die;
    }
}catch (PDOException $e) {
    $pdo->rollBack();
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
}
?>
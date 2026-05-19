<?php
require_once '../config/db.php';
require_once './libs/SimpleXLSX.php';

use Shuchkin\SimpleXLSX;

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['file'])) {
    try {

        $xlsx = SimpleXLSX::parse($_FILES['file']['tmp_name']);
                
        $pdo->beginTransaction();

        // OPTIMIZATION: Use "ON DUPLICATE KEY UPDATE"
        // This requires your 'sku' column to have a UNIQUE index in MySQL
        $sql = "INSERT INTO products (sku, name, stock_quantity, unit, cost_price, selling_price_1, selling_price_2) 
                        VALUES (:sku, :name, :qty, :unit, :cost, :selling_1, :selling_2)
                        ON DUPLICATE KEY UPDATE 
                        name = VALUES(name), 
                        stock_quantity = stock_quantity + VALUES(stock_quantity),
                        unit = VALUES(unit), 
                        cost_price = VALUES(cost_price), 
                        selling_price_1 = VALUES(selling_price_1),
                        selling_price_2 = VALUES(selling_price_2)";
                
        $stmt = $pdo->prepare($sql);
        $count = 0;
        // Chuyển toàn bộ file excel thành mảng (Array)
        $rows = $xlsx->rows();

        for ($i = 1; $i < count($rows); $i++) {
            $cells = $rows[$i];
            if (empty($cells[0])) continue;

            $stmt->execute([
                ':sku'  => $cells[0],
                ':name' => $cells[1],
                ':qty'  => (float)$cells[2],
                ':unit' => $cells[3],
                ':cost' => str_replace(['.', ','], '', $cells[4]),
                ':selling_1' => str_replace(['.', ','], '', $cells[5]),
                ':selling_2' => str_replace(['.', ','], '', $cells[6]),
            ]);
            $count++;
        }

        $pdo->commit();

        echo json_encode(['status' => 'success', 'message' => "Xử lý thành công $count sản phẩm."]);
    } catch (Exception $e) {
        if ($pdo->inTransaction()) $pdo->rollBack();
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
}
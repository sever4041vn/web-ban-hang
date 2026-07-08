<?php
require_once '../config/db.php';
require_once './libs/SimpleXLSX.php';

use Shuchkin\SimpleXLSX;
function parsePrice($value)
{
    if (is_numeric($value)) {
        return (float)$value;
    }

    $value = trim((string)$value);

    if ($value === '') {
        return null;
    }

    $lastComma = strrpos($value, ',');
    $lastDot   = strrpos($value, '.');

    if ($lastComma !== false && $lastDot !== false) {
        // Dấu xuất hiện sau cùng được xem là dấu thập phân
        if ($lastComma > $lastDot) {
            // 18.000,3
            $value = str_replace('.', '', $value);
            $value = str_replace(',', '.', $value);
        } else {
            // 18,000.3
            $value = str_replace(',', '', $value);
        }
    } elseif ($lastComma !== false) {
        // 18000,3
        $value = str_replace(',', '.', $value);
    } elseif (substr_count($value, '.') > 1) {
        // 18.000.000
        $value = str_replace('.', '', $value);
    }

    return (float)$value;
}


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
            $cost_price = str_replace('.', '', $cells[4]);
            $cost_price = str_replace(',','.', $cost_price);
            $selling_price_1 = parsePrice($cells[5]);
            $selling_price_2 = parsePrice($cells[6]);
            $stmt->execute([
                ':sku'  => $cells[0],
                ':name' => $cells[1],
                ':qty'  => (float)$cells[2],
                ':unit' => $cells[3],
                ':cost' =>  $cost_price,
                ':selling_1' =>  $selling_price_1,
                ':selling_2' =>  $selling_price_2,
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
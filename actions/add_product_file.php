<?php
require_once '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['file'])) {
    try {
        $targetFilePath = 'uploads/' . basename($_FILES['file']['name']);
        if (pathinfo($targetFilePath, PATHINFO_EXTENSION) != "xlsx") throw new Exception("Không đúng định dạng");

        if (move_uploaded_file($_FILES['file']['tmp_name'], $targetFilePath)) {
            // Note: If the low-end PC struggles with Python, consider a native PHP Excel library like Spout
            $command = "python py/excel.py " . escapeshellarg($targetFilePath);
            $csvFilePath = trim(shell_exec($command));

            if ($csvFilePath && ($handle = fopen($csvFilePath, "r"))) {
                fgetcsv($handle, 1000, ","); // Skip header
                
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

                while ($data = fgetcsv($handle, 1000, ",")) {
                    if (empty($data[0])) continue;

                    $stmt->execute([
                        ':sku'  => $data[0],
                        ':name' => $data[1],
                        ':qty'  => (float)$data[2],
                        ':unit' => $data[3],
                        ':cost' => str_replace(['.', ','], '', $data[4]),
                        ':selling_1' => str_replace(['.', ','], '', $data[5]),
                        ':selling_2' => str_replace(['.', ','], '', $data[6]),
                    ]);
                    $count++;
                }

                $pdo->commit();
                fclose($handle);
                @unlink($targetFilePath);
                @unlink($csvFilePath);

                echo json_encode(['status' => 'success', 'message' => "Xử lý thành công $count sản phẩm."]);
            }
        }
    } catch (Exception $e) {
        if ($pdo->inTransaction()) $pdo->rollBack();
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
}
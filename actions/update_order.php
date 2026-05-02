<?php
require_once '../config/db.php';
date_default_timezone_set('Asia/Ho_Chi_Minh');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $invoice_no = $_POST["invoice_no"] ?? '';
    $customer = json_decode($_POST["customer"], true);
    $items = json_decode($_POST["items"], true);
    $order = json_decode($_POST["order"], true);

    if (empty($items)) {
        echo json_encode(['status' => 'error', 'message' => "Vui lòng nhập đủ trường thông tin"]);
        exit;
    }

    try {
        $pdo->beginTransaction();

        // 1. Lấy ID
        $stmtOrder = $pdo->prepare("SELECT id FROM orders WHERE invoice_no = ?");
        $stmtOrder->execute([$invoice_no]);
        $orderRow = $stmtOrder->fetch(PDO::FETCH_ASSOC);
        
        if (!$orderRow) {
            throw new Exception("Không tìm thấy hóa đơn");
        }
        $order_id = $orderRow["id"];

        // 2. 
        // 2.1 Trả về kho
        $stmtRestoreStock = $pdo->prepare("UPDATE products SET stock_quantity = stock_quantity + ? WHERE id = ?");
        // 2.2 Lấy từ kho
        $stmtReduceStock  = $pdo->prepare("UPDATE products SET stock_quantity = stock_quantity - ? WHERE id = ?");
        // 2.3 Thêm từng sản phẩm vào đơn hàng
        $stmtInsertItem   = $pdo->prepare("INSERT INTO order_items (order_id, product_id, name, unit, quantity, cost_price, selling_price, subtotal) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        // 2.4 Nhật ký kho
        $stmtLog          = $pdo->prepare("INSERT INTO inventory_log (product_id, type, quantity, reference_id, note) VALUES (?, 'export', ?, ?, ?)");

        // 3. Trả kho từ các sản phẩm cũ
        $stmtOldItems = $pdo->prepare("SELECT product_id, quantity FROM order_items WHERE order_id = ?");
        $stmtOldItems->execute([$order_id]);
        while ($item = $stmtOldItems->fetch(PDO::FETCH_ASSOC)) {
            $stmtRestoreStock->execute([$item['quantity'], $item['product_id']]);
        }

        // 4. Bỏ các sản phẩm cũ
        $pdo->prepare("DELETE FROM order_items WHERE order_id = ?")->execute([$order_id]);

        // 5. Tính tiền hóa đơn
        $total_amount = 0;
        $cost_amount = 0;
        foreach ($items as $item) {
            $total_amount += $item['quantity'] * $item['selling_price'];
            $cost_amount += $item['quantity'] * $item['cost_price'];
        }
        $profit_amount = $total_amount - $cost_amount;

        $sqlUpdateOrder = "UPDATE orders SET customer_id = ?, customer_name = ?, address = ?, total_amount = ?, final_amount = ?, profit_amount = ?, extra_label_1 = ?, extra_value_1 = ?, extra_label_2 = ?, debt_amount = ? WHERE id = ?";
        $pdo->prepare($sqlUpdateOrder)->execute([
            $customer["customer_id"], $customer["customer_name"], $customer["address"], 
            $total_amount, $total_amount, $profit_amount, 
            $order["extra_label_1"], $order["extra_value_1"], $order["extra_label_2"], 
            $order["debt_amount"], $order_id
        ]);

        // 6. Thêm sản phẩm vào đơn hàng
        $note = "Cập nhật hóa đơn: $invoice_no";
        foreach ($items as $item) {
            $subtotal = $item['quantity'] * $item['selling_price'];

            // Insert Item
            $stmtInsertItem->execute([
                $order_id, $item['product_id'], $item['name'], $item['unit'], 
                $item['quantity'], $item['cost_price'], $item['selling_price'], $subtotal
            ]);

            // Reduce Stock
            $stmtReduceStock->execute([$item['quantity'], $item['product_id']]);

            // Log change
            $stmtLog->execute([$item['product_id'], $item['quantity'], $order_id, $note]);
        }

        $pdo->commit();
        echo json_encode(['status' => 'success', 'message' => 'Cập nhật hóa đơn thành công!', 'invoice_no' => $invoice_no]);

    } catch (Exception $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
}
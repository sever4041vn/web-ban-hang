<?php
require_once '../config/db.php';
date_default_timezone_set('Asia/Ho_Chi_Minh');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $customer = json_decode($_POST["customer"], true);
    $items = json_decode($_POST["items"], true);
    $order = json_decode($_POST["order"], true);

    if (empty($items)) {
        echo json_encode(['status' => 'error', 'message' => "Vui lòng nhập đủ trường thông tin"]);
        exit;
    }

    try {
        $pdo->beginTransaction();

        // Lấy id đơn hàng
        $today = date('Y-m-d');
        $tomorrow = date('Y-m-d', strtotime('+1 day'));
        $sqlOrderCount = "SELECT COUNT(*) AS total FROM orders WHERE created_at >= ? AND created_at < ?";
        $stmtOrderCount = $pdo->prepare($sqlOrderCount);
        $stmtOrderCount->execute([$today, $tomorrow]);
        $total = $stmtOrderCount->fetch(PDO::FETCH_ASSOC);

        $invoice_no = "HD-" . date("Ymd") . "-" . sprintf('%04d', $total["total"] + 1);

        $total_amount = 0;
        $cost_amount = 0;
        // Duyệt qua thông tin từng sản phẩm
        foreach ($items as $item) {
            $total_amount += $item['quantity'] * $item['selling_prices'][$item["choice"]-1];
            $cost_amount += $item['quantity'] * $item['cost_price'];
        }
        $final_amount = $total_amount - $order["paid_amount"];
        $profit_amount = $total_amount - $cost_amount;
        //Lưu đơn hàng
        $sqlOrder = "INSERT INTO orders (invoice_no, customer_id, customer_name, address, total_amount, final_amount, profit_amount, extra_label_1, extra_value_1, extra_label_2, debt_amount, extra_label_3, paid_amount) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmtOrder = $pdo->prepare($sqlOrder);
        $stmtOrder->execute([
            $invoice_no, $customer["customer_id"], $customer["customer_name"], 
            $customer["address"], $total_amount, $final_amount, $profit_amount, 
            $order["extra_label_1"], $order["extra_value_1"], $order["extra_label_2"], $order["debt_amount"], $order["extra_label_3"], $order["paid_amount"]
        ]);
        $order_id = $pdo->lastInsertId();

        // Lưu thông tin sản phẩm trong hóa đơn
        $stmtItem = $pdo->prepare("INSERT INTO order_items (order_id, product_id, name, unit, quantity, cost_price, selling_price_1, selling_price_2, choice, subtotal) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        // Trừ trong kho
        $stmtStock = $pdo->prepare("UPDATE products SET stock_quantity = stock_quantity - ? WHERE id = ?");
        // Ghi nhật ký trong kho
        $stmtLog = $pdo->prepare("INSERT INTO inventory_log (product_id, type, quantity, reference_id, note) VALUES (?, 'export', ?, ?, ?)");

        $note = "Xuất kho: $invoice_no";

        foreach ($items as $item) {
            $subtotal = $item['quantity'] * $item['selling_prices'][$item["choice"]-1];
            
            $stmtItem->execute([$order_id, $item['product_id'], $item['name'], $item['unit'], $item['quantity'], $item['cost_price'], $item['selling_prices'][0], $item['selling_prices'][1], $item['choice'], $subtotal]);
            $stmtStock->execute([$item['quantity'], $item['product_id']]);
            $stmtLog->execute([$item['product_id'], $item['quantity'], $order_id, $note]);
        }

        $pdo->commit();
        echo json_encode(['status' => 'success', 'message' => 'Thành công!', 'invoice_no' => $invoice_no]);
    } catch (Exception $e) {
        $pdo->rollBack();
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
}
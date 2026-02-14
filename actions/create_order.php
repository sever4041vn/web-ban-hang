<?php
require_once '../config/db.php';
date_default_timezone_set('Asia/Ho_Chi_Minh');
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $customer = json_decode($_POST["customer"], true);
    $items = json_decode($_POST["items"], true);

    if (count($items)<=0) {
        echo json_encode(['status' => 'error', 'message' => "Vui lòng nhập đủ trường thông tin"]);
        die;

    }

    try {
        //Tính tổng
        $timestamp = time();
        $searchDate = date('Y-m-d',$timestamp);
        $sqlOrderCount = "SELECT COUNT(*) AS total FROM orders WHERE created_at LIKE ?";
        $stmtOrderCount = $pdo->prepare($sqlOrderCount);
        $stmtOrderCount->execute(["%$searchDate%"]);        
        $total = $stmtOrderCount->fetch(PDO::FETCH_ASSOC);
        // 2. Tạo mã hóa đơn duy nhất (VD: HD-20240212-1234)
        $invoice_no = "HD-" . date("Ymd",$timestamp) . "-" . sprintf('%04d', $total["total"]+1);

        // 1. Bắt đầu Transaction
        $pdo->beginTransaction();

        // 3. Tính tổng tiền tạm tính
        $total_amount = 0;
        $cost_amount = 0;
        foreach ($items as $item) {
            $total_amount += $item['quantity'] * $item['selling_price'];
            $cost_amount += $item['quantity'] * $item['cost_price'];
        }
        $profit_amount = $total_amount - $cost_amount;
        // 4. Lưu vào bảng orders
        $sqlOrder = "INSERT INTO orders (invoice_no, customer_id, address, total_amount, final_amount, profit_amount) 
                    VALUES (?, ?, ?, ?, ?, ?)";
        $stmtOrder = $pdo->prepare($sqlOrder);
        $stmtOrder->execute([$invoice_no, $customer["customer_id"], $customer["address"], $total_amount, $total_amount, $profit_amount]);
        $order_id = $pdo->lastInsertId();

        // 5. Lặp qua từng sản phẩm để xử lý
        foreach ($items as $item) {
            $p_id = $item['product_id'];
            $unit = $item['unit'];
            $qty = $item['quantity'];
            $cost_price = $item['cost_price'];
            $selling_price = $item['selling_price'];
            $subtotal = $qty * $selling_price;

            // --- LƯU CHI TIẾT HÓA ĐƠN ---
            $sqlItem = "INSERT INTO order_items (order_id, product_id, unit, quantity, cost_price, selling_price, subtotal) 
                        VALUES (?, ?, ?, ?, ?, ?, ?)";
            $pdo->prepare($sqlItem)->execute([$order_id, $p_id, $unit, $qty, $cost_price, $selling_price, $subtotal]);

            // --- TRỪ KHO SẢN PHẨM ---
            $sqlUpdateStock = "UPDATE products SET stock_quantity = stock_quantity - ? WHERE id = ?";
            $pdo->prepare($sqlUpdateStock)->execute([$qty, $p_id]);

            // --- GHI NHẬT KÝ KHO (LOG) ---
            $sqlLog = "INSERT INTO inventory_log (product_id, type, quantity, reference_id, note) 
                    VALUES (?, 'export', ?, ?, ?)";
            $note = "Xuất kho cho hóa đơn: $invoice_no";
            $pdo->prepare($sqlLog)->execute([$p_id, $qty, $order_id, $note]);
        }

        // 6. Nếu mọi thứ thành công, xác nhận lưu vào DB
        $pdo->commit();
        
        echo json_encode(['status' => 'success', 'message' => 'Tạo hóa đơn thành công!', 'invoice_no' => $invoice_no]);
        die;
    } catch (Exception $e) {
        // 7. Nếu có bất kỳ lỗi nào, hủy bỏ toàn bộ các thao tác trên
        $pdo->rollBack();
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
}
?>
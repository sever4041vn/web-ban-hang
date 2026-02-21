<?php
require_once '../config/db.php';
date_default_timezone_set('Asia/Ho_Chi_Minh');
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $invoice_no = $_POST["invoice_no"];
    $items = json_decode($_POST["items"], true);

    if (count($items)<=0) {
        echo json_encode(['status' => 'error', 'message' => "Vui lòng nhập đủ trường thông tin"]);
        die;

    }

    try {


        // 1. Bắt đầu Transaction
        $pdo->beginTransaction();
        // 2. Lấy id
        $sqlOrder = "SELECT id FROM orders WHERE invoice_no = ?";
        $stmtOrder = $pdo->prepare($sqlOrder);
        $stmtOrder->execute([$invoice_no]);
        $order_id = $stmtOrder->fetch(PDO::FETCH_ASSOC)["id"];
        // 3. Thêm lại vào kho hàng
        $sqlOldItems = "SELECT `product_id`, `quantity` FROM order_items WHERE `id` = ?";
        $stmtOldItems = $pdo->prepare($sqlOldItems);
        $stmtOldItems->execute([$order_id]);
        $oldItems = $stmtOldItems->fetchAll(PDO::FETCH_ASSOC);
        foreach ($oldItems as $item) {
            $updateStock = $pdo->prepare("UPDATE products SET stock_quantity = stock_quantity + ? WHERE id = ?");
            $updateStock->execute([$item['quantity'], $item['product_id']]);
        }
        // 4. Xóa khỏi đơn hàng
        $deleteItems = $pdo->prepare("DELETE FROM `order_items` WHERE order_id = ?");
        $deleteItems->execute([$order_id]);
        // 5. Tính tổng tiền tạm tính
        $total_amount = 0;
        $cost_amount = 0;
        foreach ($items as $item) {
            $total_amount += $item['quantity'] * $item['selling_price'];
            $cost_amount += $item['quantity'] * $item['cost_price'];
        }
        $profit_amount = $total_amount - $cost_amount;
        // 6. Lưu vào bảng orders
        $sqlOrder = "UPDATE orders SET total_amount = ?, final_amount = ?, profit_amount = ?
                    WHERE id = ?";
        $stmtOrder = $pdo->prepare($sqlOrder);
        $stmtOrder->execute([$total_amount, $total_amount, $profit_amount, $order_id]);
        // 6. Lặp qua từng sản phẩm để xử lý
        foreach ($items as $item) {
            $p_id = $item['product_id'];
            $name = $item['name'];
            $unit = $item['unit'];
            $qty = $item['quantity'];
            $cost_price = $item['cost_price'];
            $selling_price = $item['selling_price'];
            $subtotal = $qty * $selling_price;

            // --- LƯU CHI TIẾT HÓA ĐƠN ---
            $sqlItem = "INSERT INTO order_items (order_id, product_id, name, unit, quantity, cost_price, selling_price, subtotal) 
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            $pdo->prepare($sqlItem)->execute([$order_id, $p_id, $name, $unit, $qty, $cost_price, $selling_price, $subtotal]);

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
<?php
require_once '../config/db.php';
header('Content-Type: application/json');

$from_date = $_GET['from_date'] ?? date('Y-m-01'); // Mặc định từ đầu tháng
$to_date = $_GET['to_date'] ?? date('Y-m-d');     // Mặc định đến hôm nay

try {
    // Dùng khoảng nửa-mở [from, to+1day) thay vì bọc DATE(created_at):
    // bọc hàm DATE() quanh cột created_at khiến MySQL KHÔNG dùng được index
    // idx_orders_created_at (phải scan toàn bảng để tính DATE() cho từng dòng).
    // So sánh trực tiếp created_at >= ? AND created_at < ? thì dùng được index.
    $from = $from_date . ' 00:00:00';
    $to = date('Y-m-d', strtotime($to_date . ' +1 day')) . ' 00:00:00';

    // Gộp 2 query SUM (doanh thu + lợi nhuận) thành 1 query duy nhất
    // -> giảm 1 round-trip tới DB mỗi lần xem báo cáo
    $sqlTotal = "SELECT SUM(final_amount) as total_revenue, SUM(profit_amount) as total_profit
                 FROM orders WHERE created_at >= ? AND created_at < ?";
    $stmtTotal = $pdo->prepare($sqlTotal);
    $stmtTotal->execute([$from, $to]);
    $totals = $stmtTotal->fetch();
    $totalRevenue = $totals['total_revenue'] ?? 0;
    $totalProfit = $totals['total_profit'] ?? 0;

    // Danh sách hóa đơn trong khoảng thời gian để hiển thị bên dưới
    $sqlOrders = "SELECT invoice_no, created_at, final_amount, profit_amount
                  FROM orders 
                  WHERE created_at >= ? AND created_at < ?
                  ORDER BY created_at DESC";
    $stmtOrders = $pdo->prepare($sqlOrders);
    $stmtOrders->execute([$from, $to]);
    $orders = $stmtOrders->fetchAll();

    echo json_encode([
        'status' => 'success',
        'total_revenue' => $totalRevenue,
        'total_profit' => $totalProfit,
        'orders' => $orders
    ]);
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
?>
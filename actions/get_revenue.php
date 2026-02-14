<?php
require_once '../config/db.php';
header('Content-Type: application/json');

$from_date = $_GET['from_date'] ?? date('Y-m-01'); // Mặc định từ đầu tháng
$to_date = $_GET['to_date'] ?? date('Y-m-d');     // Mặc định đến hôm nay

try {
    // 1. Tính tổng doanh thu trong khoảng thời gian
    $sqlTotal = "SELECT SUM(final_amount) as total FROM orders 
                 WHERE DATE(created_at) BETWEEN ? AND ?";
    $stmtTotal = $pdo->prepare($sqlTotal);
    $stmtTotal->execute([$from_date, $to_date]);
    $totalRevenue = $stmtTotal->fetch()['total'] ?? 0;
    // 2. Tính tổng lợi nhuận
    $sqlProfit = "SELECT SUM(profit_amount) as total FROM orders 
                 WHERE DATE(created_at) BETWEEN ? AND ?";
    $stmtProfit = $pdo->prepare($sqlProfit);
    $stmtProfit->execute([$from_date, $to_date]);
    $totalProfit = $stmtProfit->fetch()['total'] ?? 0;

    // 2. Lấy danh sách các hóa đơn trong khoảng thời gian đó để hiển thị bên dưới
    $sqlOrders = "SELECT invoice_no, created_at, final_amount, profit_amount
                  FROM orders 
                  WHERE DATE(created_at) BETWEEN ? AND ? 
                  ORDER BY created_at DESC";
    $stmtOrders = $pdo->prepare($sqlOrders);
    $stmtOrders->execute([$from_date, $to_date]);
    $orders = $stmtOrders->fetchAll(PDO::FETCH_ASSOC);

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
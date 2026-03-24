<?php
require_once 'config/db.php';
date_default_timezone_set('Asia/Ho_Chi_Minh');

// 1. Lấy invoice_no hóa đơn từ URL
$invoice_no = $_GET["invoice_no"]?? 0;

$orderStmt = $pdo->prepare("SELECT id FROM `orders` WHERE invoice_no = ?");
$orderStmt->execute([$invoice_no]);
$orderId = $orderStmt->fetch();

if ($orderId) {
    $order_id = $orderId["id"];
}else{
    $order_id="";
}
// 2. Truy vấn thông tin chung của hóa đơn
$stmt = $pdo->prepare("SELECT o.*, c.id as customer_id, c.name as customer_name, c.phone as customer_phone 
                       FROM orders o 
                       LEFT JOIN customers c ON o.customer_id = c.id 
                       WHERE o.id = ?");
$stmt->execute([$order_id]);
$order = $stmt->fetch();

if (!$order) {
    die("Không tìm thấy hóa đơn!");
}

// 3. Truy vấn chi tiết các mặt hàng trong hóa đơn
$itemStmt = $pdo->prepare("SELECT oi.*, p.sku
                           FROM order_items oi 
                           JOIN products p ON oi.product_id = p.id 
                           WHERE oi.order_id = ?");
$itemStmt->execute([$order_id]);
$items = $itemStmt->fetchAll();

// 4. Truy vấn tổng số tiền hóa đơn trước
$debtAmountStmt = $pdo->prepare("SELECT SUM(debt_amount) as total_debt 
                                FROM orders 
                                WHERE customer_id = ? AND created_at < ?;");
$debtAmountStmt->execute([$order["customer_id"], $order["created_at"]]);
$totalDebt = $debtAmountStmt->fetch();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>In Hóa Đơn - <?= $order['invoice_no'] ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/print.css">
</head>
<body onload="window.print()"> <div class="invoice-wrapper container my-5">
    <div class="text-end no-print mb-4">
        <button onclick="window.print()" class="btn btn-primary">In lại</button>
        <a href="sales.php" class="btn btn-secondary">Tạo hóa đơn</a>
        <a href="order.php?invoice_no=<?= $order['invoice_no']?>" class="btn btn-success">Sửa hóa đơn</a>
    </div>

    <div style="font-size: smaller;" class="invoice-box p-4 border shadow-sm bg-white">
        <div class="row">
            <h3 class="fw-bold text-uppercase text-center mb-0">CÔNG TY TNHH MTV VÀ DV KHÁNH HỒNG</h3>
            <h4 class="fw-bold text-center d-flex justify-content-center">
                <p class="fw-bold fst-italic mb-0">Chuyên:</p>
                <p class="mb-0">Cung cấp thiết bị điện nước</p>
            </h4>
        </div>
        <div class="row">
            <div class="col-7 fw-bold">
                <p style="font-size: smaller !important" class="mb-0 fs-6">Địa chỉ: 323 Nguyễn Trãi - TP Quảng Ngãi</p>
                <p style="font-size: smaller !important" class="mb-0 fs-6">ĐT: 0915 254 385(Hường) - 0989 048 997(Vương) </p>
            </div>
            <div class="col-5 fw-bold text-end">
                <p style="font-size: smaller !important" class="mb-0 fs-6">Số TK: 0271000214568, VCB,</p>
                <p style="font-size: smaller !important" class="mb-0 fs-6">Huỳnh Thị Mỹ Hường</p>
            </div>
        </div>
        <hr class="mt-1 mb-1">
        <div class="row mb-0">
            <h3 class="fw-bold text-uppercase text-center mb-0">PHIẾU BÁN HÀNG</h3>
        </div>
        <hr class="mt-1 mb-1">
        <div class="row mb-1 d-flex justify-content-center text-center">
            <p class="mb-1 col-6 text-end">Khách hàng: <strong><?= $order['customer_name'] ?? 'Khách lẻ' ?></strong></p>
            <p class="mb-1 col-6 text-start">ĐC: <?= $order['address']?></p>
        </div>

        <table class="table table-bordered">
            <thead class="">
                <tr class="text-center">
                    <th class="p-0">STT</th>
                    <th class="p-0">Tên hàng</th>
                    <th class="p-0">ĐVT</th>
                    <th class="p-0">SL</th>
                    <th class="p-0">Đơn giá</th>
                    <th class="p-0">Thành tiền</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $stt = 0;
                foreach($items as $item): 
                    $stt++;
                ?>
                <tr>
                    <td class="text-center p-0"><?=$stt?></td>
                    <td class="p-0"><?= $item['name'] ?><br></td>
                    <td class="text-center p-0"><?= $item['unit'] ?></td>
                    <td class="text-center p-0"><?= $item['quantity'] ?></td>
                    <td class="text-end p-0"><?= number_format($item['selling_price'], 0, ',', '.') ?></td>
                    <td class="text-end p-0"><?= number_format($item['subtotal'], 0, ',', '.') ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
            <div class="only-last-page">
                <tr>
                    <td colspan="5" class="p-0 text-end fw-bold h10">Tổng toa hàng:</td>
                    <td class="p-0 text-end fw-bold h10"><?= number_format($order['final_amount'], 0, ',', '.') ?></td>
                </tr>
                <tr class="no-print">
                    <td colspan="5" class="p-0 text-end fw-bold h10">
                        <button style="font-size: smaller !important;" onclick="showProfit()" class="btn">Hiện lợi nhuận</button>
                        Lợi nhuận:
                    </td>
                    <td id="profit-hide" class="p-0 text-end fw-bold h10">#</td>
                    <td id="profit-show" style="display: none;" class="p-0 text-end fw-bold h10"><?= number_format($order['profit_amount'], 0, ',', '.') ?></td>
                </tr>
                <tr style="display: none; text-align: right;">
                    <td ondblclick="changeMessage()" colspan="6"  id="message-show" class="p-0 text-end fw-bold h10"></td>
                </tr>
                <tr>
                    <td colspan="6" class="border-0 p-0 text-end fw-bold h10">
                        <?php

                            $timestamp = strtotime($order['created_at']);
                            echo strftime("Ngày %d tháng %m năm %Y",$timestamp );
                        ?>
                    </td>
                </tr>
            </div>
        </table>

        <div class="row mt-5 text-center">
            <div class="col-6">
                <p class="fw-bold">Người mua hàng</p>
            </div>
            <div class="col-6">
                <p class="fw-bold">Người lập hóa đơn</p>
            </div>
        </div>
    </div>
</div>
<script>
    let messageInput = "Tổng nợ cũ: <?= number_format($totalDebt['total_debt'], 0, ',', '.') ?>"
    const messageDiv = document.getElementById("message-show")
    const showProfit = () => {
        profit_hide = document.getElementById("profit-hide");
        profit_show = document.getElementById("profit-show");
        if (profit_hide.style.display == "none") {
            profit_hide.style.display = "";
            profit_show.style.display = "none";
        }else{
            profit_show.style.display = "";
            profit_hide.style.display = "none";
        }
    }
    document.addEventListener("keypress",(e)=>{
        // console.log(e.key)
        if (e.key=="Enter") {
            messageDiv.parentNode.style.display = ""
        }else if (e.key=="x"){
            messageDiv.parentNode.style.display = "none"
        }
    })
    const addMessage = () => {
        messageDiv.innerText = messageInput;
    }
    addMessage()
    
    const changeMessage = () => {
        messageDiv.innerText = ""
        const input = document.createElement("input");
        input.className = "form-control";
        input.value = messageInput
        messageDiv.appendChild(input);
        input.focus();
        input.addEventListener("focusout",()=>{
            messageInput = input.value
            messageDiv.innerHTML = messageInput
        })
    }
</script>
</body>
</html>
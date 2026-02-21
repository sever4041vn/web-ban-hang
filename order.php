<?php
    $invoice_no = $_GET["invoice_no"]?? 0;

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sửa hóa đơn</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
</head>
<body onload="updateTable()">
    
    <?php require("header.html"); ?>
    <div class="container mt-4">
        <div class="row">
            <div class="col-md-12">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h5>🛒 Sửa Hóa Đơn <?= $invoice_no?></h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Khách hàng</label>
                                <input id="customerName" type="text" name="customerName" class="form-control mt-10" required>
                                <input id="address" type="text" name="address" class="form-control mt-10" placeholder="Nhập địa chỉ" required>
                            </div>
                        </div>
                    </div>
                    
                    <table class="table table-bordered align-middle">
                        <thead class="table-light">
                            <tr>
                                <th class="mw-50">Sản phẩm</th>
                                <th width="100">Đơn vị tính</th>
                                <th width="100">Số lượng</th>
                                <th width="200">Đơn giá</th>
                                <th width="200">Thành tiền</th>
                                <th width="50"></th>
                            </tr>
                        </thead>
                        <tbody id="invoiceItems">
                        </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="3" class="text-end fw-bold">Tổng cộng:</td>
                                    <td colspan="2" class="text-danger fw-bold h5" id="totalDisplay">0 ₫</td>
                                </tr>
                            </tfoot>
                        </table>
                        
                        <div class="text-end mt-3">
                            <div id="response-message"></div>
                            <button onclick="submitInvoice()" class="btn btn-success btn-lg px-5">
                                <i class="bi bi-printer"></i> Lưu & In hóa đơn
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
<script src="../assets/js/update_order.js"></script>
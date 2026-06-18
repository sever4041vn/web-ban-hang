<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Đơn Hàng</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<?php require("header.html"); ?>
<div class="container mt-4">
    <div class="card shadow">
        <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="bi bi-receipt"></i> Lịch Sử Hóa Đơn</h5>
            <button class="btn btn-sm btn-outline-light" onclick="loadOrders()">
                <i class="bi bi-arrow-clockwise"></i> Làm mới
            </button>
        </div>
        <div>
            <input id="search" oninput="searchOrder()" type="text" class="form-control mt-2 mb-2" placeholder="Nhập tên khách hàng">
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Mã Hóa Đơn</th>
                            <th>Ngày Tạo</th>
                            <th>Khách Hàng</th>
                            <th>Địa Chỉ</th>
                            <th>Tổng Tiền Toa Hàng</th>
                            <th>Tổng Tiền Nợ trước</th>
                            <th>Đã Thanh Toán</th>
                            <th>Cần Thanh Toán Còn Lại</th>
                            <th class="text-center">Thao Tác</th>
                        </tr>
                    </thead>
                    <tbody id="orderTableBody">
                        </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<script src="/assets/js/get_order.js"></script>

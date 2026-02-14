<?php //include 'db.php'; ?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Khách Hàng</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <?php require("header.html"); ?>
<div class="container mt-5">
    <h2 class="mb-4">Thêm Khách Hàng</h2>
    <div class="card card-body mb-4 shadow-sm">
        <form method="POST" id="customer-form" class="row g-3">
            <div class="col-3">
                <input id="name" type="text" name="name" class="form-control" placeholder="Nhập tên khách hàng" required>
            </div>
            <div class="col-3">
                <input id="phone" type="number" name="phone" class="form-control" placeholder="Nhập số điện thoại khách hàng">
            </div>
            <div class="col-4">
                <input id="address" type="text" name="address" class="form-control" placeholder="Nhập địa chỉ khách hàng">
            </div>
            <div class="col-2">
                <button type="submit" class="btn btn-success">Thêm</button>
            </div>
            <div id="response-message-add"></div>
        </form>
    </div>
    <h2 class="mb-4">Khách Hàng</h2>
    <div class="card card-body mb-4 shadow-sm">
        <div class="row g-3">
            <div>
                <input id="search" type="text" name="id" class="form-control" placeholder="Nhập tên hoặc mã khách hàng để tìm kiếm" required>
            </div>
            <div id="response-message"></div>
        </div>
    </div>
    <table class="table table-hover bg-white shadow-sm">
        <thead class="table-dark">
            <tr>
                <th>id</th>
                <th>Tên Khách Hàng</th>
                <th>Số Điện Thoại</th>
                <th>Địa Chỉ</th>
            </tr>
        </thead>
        <tbody id="customers">
           
        </tbody>
    </table>
</div>
</body>
<script src="/assets/js/customers.js"></script>
<script src="/assets/js/add_customer.js"></script>


</html>
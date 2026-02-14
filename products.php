<?php //include 'db.php'; ?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Sản phẩm</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <?php require("header.html"); ?>
<div class="container mt-5">
    <h2 class="mb-4">📦 Sản Phẩm</h2>
    <div class="card card-body mb-4 shadow-sm">
        <div class="row g-3">
            <div>
                <input id="search" type="text" name="sku" class="form-control" placeholder="Nhập tên hoặc mã sản phẩm để tìm kiếm" required>
            </div>
            <div id="response-message"></div>
        </div>
    </div>
    <table class="table table-hover bg-white shadow-sm">
        <thead class="table-dark">
            <tr>
                <th>SKU</th>
                <th>Tên Sản Phẩm</th>
                <th>Số Lượng Tồn Kho</th>
                <th>Đơn Vị Tính</th>
                <th>Giá Nhập</th>
                <th>Giá Bán</th>
                <th>Lợi Nhuận</th>
                <th>Thao tác</th>
            </tr>
        </thead>
        <tbody id="products">
           
        </tbody>
    </table>
</div>
</body>
<script src="/assets/js/products.js"></script>
<script src="/assets/js/delete_product.js"></script>

</html>
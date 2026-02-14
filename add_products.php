<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thêm Sản Phẩm</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<?php require("header.html"); ?>
<div class="container mt-4">
    <div class="card shadow">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">📦 Thêm Sản Phẩm Mới Bằng File csv</h5>
        </div>
        <div class="card-body">
            <form id="add-product-file" method="POST">
                <input id="file" type="file" class="custom-file-input" required accept=".csv">
                <button class="btn btn-success" type="submit">Thêm sản phẩm</button>
            </form>
            <div id="response-message-file"></div>
        </div>
    </div>
<div class="card shadow">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">📦 Thêm Sản Phẩm Mới</h5>
        </div>
        <div class="card-body">
            <form id="add-product" action="actions/add_product.php" method="POST">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label fw-bold">Mã sản phẩm</label>
                        <input type="text" name="sku" class="form-control" placeholder="Nhập mã sản phẩm" required>
                    </div>
                    <div class="col-md-8 mb-3">
                        <label class="form-label fw-bold">Tên sản phẩm</label>
                        <input type="text" name="name" class="form-control" placeholder="Nhập tên sản phẩm" required>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label fw-bold">Danh mục</label>
                        <select id="category" name="category" class="form-select">
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label fw-bold">Giá vốn (VNĐ)</label>
                        <input id="cost_price" type="number" name="cost_price" class="form-control" value="0">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label fw-bold">Giá bán (VNĐ)</label>
                        <input id="selling_price" type="number" name="selling_price" class="form-control" value="0">
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label fw-bold">Đơn vị</label>
                        <select id="unit" name="unit" class="form-select">
                        </select>
                    </div>
                    <div id="profit" class="col-md-4 mb-3">
                        
                    </div>
                </div>

                <hr>
                <div class="d-flex justify-content-end">
                    <div id="response-message"></div>
                    <button type="reset" class="btn btn-secondary me-2">Nhập lại</button>
                    <button type="submit" class="btn btn-primary px-4">Lưu sản phẩm</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script src="./assets/js/add_product.js"></script>
<script src="./assets/js/add_product_file.js"></script>
<script src="./assets/js/get_unit.js"></script>
<script src="./assets/js/get_category.js"></script>


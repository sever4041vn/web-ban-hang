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
            <h5 class="mb-0">📦 Thêm Đơn vị tính</h5>
        </div>
        <div class="card-body">
            <form id="add-product" action="actions/add_product.php" method="POST">
                <div class="row">
                    <div class="col-md-8 mb-3">
                        <label class="form-label fw-bold">Tên đơn vị tính</label>
                        <input type="text" name="unit" class="form-control" placeholder="Nhập tên đơn vị tính" required>
                    </div>
                </div>

                <hr>
                <div class="d-flex justify-content-end">
                    <div id="response-message"></div>
                    <button type="reset" class="btn btn-secondary me-2">Nhập lại</button>
                    <button type="submit" class="btn btn-primary px-4">Thêm</button>
                </div>
            </form>
        </div>
        <table class="table table-hover bg-white shadow-sm">
            <thead class="table-dark">
                <tr>
                    <th>SKU</th>
                    <th>Tên Đơn vị tính</th>
                    <th>Nội dung</th>
                </tr>
            </thead>
            <tbody id="units">
            
            </tbody>
        </table>
    </div>
</div>
<script src="./assets/js/unit.js"></script>


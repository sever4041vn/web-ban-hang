<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trang chủ</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
</head>
<?php require("header.html"); ?>
<div class="container mt-4">
    <div class="row">
        <div class="col-md-4">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-success text-white"><h5><i class="bi bi-download"></i> Phiếu Nhập Kho</h5></div>
                <div class="card-body">
                    <form id="importForm">
                        <div class="mb-3">
                            <label class="form-label">Chọn sản phẩm</label>
                            <div class="mb-3 position-relative">
                                <input oninput="searchProducts(event)" class="name form-control" type="text">
                                <input style="display: none;" name="product_id" oninput="searchProducts(event)" class="id form-control" type="text">
                                <div style="display: none;" class="z-3 l-0 w-100 search-box bg-white position-absolute row"></div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Số lượng nhập</label>
                            <input name="quantity" class="form-control" min="1" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Ghi chú</label>
                            <textarea name="note" class="form-control" rows="2"></textarea>
                        </div>
                        <button type="submit" class="btn btn-success w-100">Xác nhận nhập kho</button>
                    </form>
                    <div id="response-message"></div>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-dark text-white d-flex justify-content-between">
                    <h5 class="mb-0">Lịch sử nhập hàng mới nhất</h5>
                    <button class="btn btn-sm btn-light" onclick="loadHistory()"><i class="bi bi-arrow-clockwise"></i></button>
                </div>
                <div class="card-body">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Thời gian</th>
                                <th>Sản phẩm</th>
                                <th>SL</th>
                                <th>Ghi chú</th>
                            </tr>
                        </thead>
                        <tbody id="historyTableBody">
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="assets/js/import_stock.js"></script>
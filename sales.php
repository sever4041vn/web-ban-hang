<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tạo hóa đơn</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
</head>
<body onload="addToTable()">
    
    <?php require("header.html"); ?>
    <div class="container mt-4">
        <div class="row">
            <div class="col-md-12">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h5>🛒 Tạo Hóa Đơn Mới</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Tìm khách hàng</label>
                                <select id="customerSelect" class="form-select select2">
                                    <option value="">-- Chọn khách hàng --</option>
                                </select>
                                <input id="address" type="text" name="address" class="form-control mt-10" placeholder="Nhập địa chỉ" required>
                            </div>
                        </div>
                        <!-- <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Tìm sản phẩm</label>
                                <button class="btn btn-success" onclick="productSelect()">Tải lại</button>
                                <select id="productSelect" class="form-select select2">
                                    <option value="">-- Chọn sản phẩm --</option>
                                </select>
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="button" onclick="addToTable()" class="btn btn-dark w-100">
                                <i class="bi bi-plus-lg"></i> Thêm hàng
                            </button>
                        </div> -->
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
<script>
    $(document).ready(function() {
        $('#productSelect').select2({
            placeholder: "Tìm tên hoặc mã SKU...",
            allowClear: true
        });
        $('#customerSelect').select2({
            placeholder: "Tìm tên hoặc id khách hàng...",
            allowClear: true
        });
    });
        
</script>
<script src="../assets/js/sales.js"></script>
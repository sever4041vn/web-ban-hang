<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Khách Hàng</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <?php require("header.html"); ?>
<div class="container mt-4">
    <div class="row">
        <div class="col-md-12">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="bi bi-graph-up"></i> Báo Cáo Doanh Thu Theo Phiếu</h5>
                </div>
                <div class="card-body">
                    <form id="filterForm" class="row g-3 align-items-end">
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Từ ngày</label>
                            <input type="date" id="from_date" class="form-control" value="<?= date('Y-m-01') ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Đến ngày</label>
                            <input type="date" id="to_date" class="form-control" value="<?= date('Y-m-d') ?>">
                        </div>
                        <div class="col-md-4">
                            <button type="button" onclick="loadRevenue()" class="btn btn-primary w-100">
                                <i class="bi bi-filter"></i> Xem báo cáo
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card bg-success text-white shadow-sm mb-4">
                <div class="card-body text-center">
                    <h6 class="text-uppercase small">Tổng doanh thu</h6>
                    <h2 class="display-6 fw-bold" id="totalDisplay">0 ₫</h2>
                    <p class="mb-0 dateRangeText"></p>
                </div>
            </div>
            <div class="card bg-success text-white shadow-sm mb-4">
                <div class="card-body text-center">
                    <h6 class="text-uppercase small">Tổng lợi nhuận</h6>
                    <h2 class="display-6 fw-bold" id="profitDisplay">0 ₫</h2>
                    <p class="mb-0 dateRangeText"></p>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-body">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Mã Phiếu</th>
                                <th>Thời Gian</th>
                                <th class="text-end">Lợi Nhuận Trên Phiếu</th>
                                <th class="text-end">Tiền Trên Phiếu</th>
                            </tr>
                        </thead>
                        <tbody id="revenueTableBody">
                            </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="assets/js/report.js"></script>

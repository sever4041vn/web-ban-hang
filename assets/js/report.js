async function loadRevenue() {
    const fromDate = document.getElementById('from_date').value;
    const toDate = document.getElementById('to_date').value;

    const response = await fetch(`actions/get_revenue.php?from_date=${fromDate}&to_date=${toDate}`);
    const data = await response.json();

    if (data.status === 'success') {
        // Cập nhật tổng tiền
        document.getElementById('totalDisplay').innerText = Number(data.total_revenue).toLocaleString() + " ₫";
        // Cập nhật lợi nhuận
        document.getElementById('profitDisplay').innerText = Number(data.total_profit).toLocaleString() + " ₫";
        
        document.querySelectorAll('.dateRangeText').forEach(elem=>{
            elem.innerText = `Từ ${fromDate} đến ${toDate}`;
        })
        // Cập nhật bảng
        const tbody = document.getElementById('revenueTableBody');
        tbody.innerHTML = data.orders.map(o => `
            <tr>
                <td class="fw-bold">${o.invoice_no}</td>
                <td>${new Date(o.created_at).toLocaleString('vi-VN')}</td>
                <td class="text-end fw-bold">${Number(o.profit_amount).toLocaleString()} ₫</td>
                <td class="text-end fw-bold">${Number(o.final_amount).toLocaleString()} ₫</td>
            </tr>
        `).join('');
    }
}

// Chạy lần đầu khi load trang
document.addEventListener('DOMContentLoaded', loadRevenue);
async function loadOrders(customer_name) {
    try {
        const response = await fetch(`actions/get_order.php?customer_name=${customer_name}`);
        const orders = await response.json();
        const tbody = document.getElementById('orderTableBody');
        tbody.innerHTML = '';
        JSON.parse(orders.message).forEach(o => {
            // Định dạng mã hóa đơn có số 0 phía trước nếu cần (ví dụ ID thành 4 số)
            const formattedId = o.id.toString().padStart(4, '0');
            
            tbody.innerHTML += `
                <tr>
                    <td class="fw-bold text-primary">${o.invoice_no}</td>
                    <td><small>${new Date(o.created_at).toLocaleString('vi-VN')}</small></td>
                    <td>${o.customer_name!=""?o.customer_name:o.customer_name_real}</td>
                    <td>${o.address}</td>
                    <td class="fw-bold text-success">${Number(o.final_amount).toLocaleString()} ₫</td>
                    <td class="fw-bold text-success">${Number(o.debt_amount).toLocaleString()} ₫</td>
                    <td class="text-center">
                        <a href="print_invoice.php?invoice_no=${o.invoice_no}" class="btn btn-sm btn-outline-secondary" title="Xem & In">
                            <i class="bi bi-printer"></i> Xem lại
                        </a>
                        <a href="order.php?invoice_no=${o.invoice_no}" class="btn btn-sm btn-success" title="Xem & In">
                            <i class="bi bi-printer"></i> Sửa
                        </a>
                    </td>
                </tr>
            `;
        });
    } catch (err) {
        console.error("Lỗi:", err);
    }
}

document.addEventListener('DOMContentLoaded', ()=>loadOrders(""));

const searchOrder = () => {
    loadOrders(document.getElementById("search").value)
}
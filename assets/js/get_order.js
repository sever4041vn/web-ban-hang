let currentOrderPage = 1;
let currentOrderSearch = "";

async function loadOrders(customer_name, page = 1, append = false) {
    try {
        currentOrderSearch = customer_name;
        currentOrderPage = page;
        // Trước đây API này trả về TOÀN BỘ bảng orders mỗi lần mở trang / tìm kiếm.
        // Giờ có phân trang (30 đơn/lần) để trang tải nhanh, có nút "Tải thêm" bên dưới
        // cho các đơn hàng cũ hơn khi cần.
        const response = await abortableFetch("orders-list", `actions/get_order.php?customer_name=${encodeURIComponent(customer_name)}&page=${page}`);
        const orders = await response.json();
        const tbody = document.getElementById('orderTableBody');
        if (!append) {
            tbody.innerHTML = '';
        }
        const list = JSON.parse(orders.message);
        list.forEach(o => {
            // Định dạng mã hóa đơn có số 0 phía trước nếu cần (ví dụ ID thành 4 số)
            const formattedId = o.id.toString().padStart(4, '0');
            
            tbody.innerHTML += `
                <tr>
                    <td class="fw-bold text-primary">${o.invoice_no}</td>
                    <td><small>${new Date(o.created_at).toLocaleString('vi-VN')}</small></td>
                    <td>${o.customer_name!=""?o.customer_name:o.customer_name_real}</td>
                    <td>${o.address}</td>
                    <td class="fw-bold text-success">${Number(o.total_amount).toLocaleString()} ₫</td>
                    <td class="fw-bold text-info">${Number(o.extra_value_1).toLocaleString()} ₫</td>
                    <td class="fw-bold">${Number(o.paid_amount).toLocaleString()} ₫</td>
                    <td class="fw-bold ${o.debt_amount==0?"text-success":"text-danger"}">${Number(o.debt_amount).toLocaleString()} ₫</td>
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

        // Hiện/ẩn nút "Tải thêm" tùy còn dữ liệu hay không (nếu trả về đủ 30 dòng,
        // khả năng còn trang tiếp theo)
        const loadMoreBtn = document.getElementById('loadMoreOrdersBtn');
        if (loadMoreBtn) {
            loadMoreBtn.style.display = (list.length < 30) ? 'none' : 'inline-block';
        }
    } catch (err) {
        if (err.name !== 'AbortError') {
            console.error("Lỗi:", err);
        }
    }
}

function loadMoreOrders() {
    loadOrders(currentOrderSearch, currentOrderPage + 1, true);
}

document.addEventListener('DOMContentLoaded', ()=>loadOrders(""));

// debounce 300ms: tránh gọi API mỗi lần gõ 1 ký tự khi tìm hóa đơn
const debouncedSearchOrder = debounce(() => loadOrders(document.getElementById("search").value, 1, false), 300)
const searchOrder = () => {
    debouncedSearchOrder()
}
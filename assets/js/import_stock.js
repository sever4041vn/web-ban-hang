const productIdSelect = document.getElementById("productSelect");
const importForm = document.getElementById("importForm");
const responseMessage = document.getElementById("response-message");
const historyTableBody = document.getElementById("historyTableBody")


const productSelect = async () => {
    let products = []
    try {
        const response = await fetch(`actions/get_product.php`);
        const result = await response.json(); // Đợi phản hồi JSON từ PHP
        if (result.status === 'success') {
            products= JSON.parse(result.message);
        } else {
            products = [];
        }
    } catch (error) {
        products = [];
    }
    for (let i = 0; i < products.length; i++) {
        productIdSelect.innerHTML = productIdSelect.innerHTML + `<option value="${products[i]["id"]}">${products[i]["sku"]} - ${products[i]["name"]}</option>`
    }
}

productSelect()

importForm.addEventListener("submit", async (e)=>{
    e.preventDefault();
    const formData = new FormData(importForm);

    try {
        const response = await fetch('actions/import_stock.php', {
            method: 'POST',
            body: formData
        });

        const result = await response.json(); // Đợi phản hồi JSON từ PHP

        if (result.status === 'success') {
            responseMessage.innerHTML = `<div class="alert alert-success">${result.message}</div>`;
        } else {
            responseMessage.innerHTML = `<div class="alert alert-danger">${result.message}</div>`;
        }
        loadHistory();
        importForm.reset();
    } catch (error) {
        console.log(error)
        responseMessage.innerHTML = `<div class="alert alert-danger">Lỗi kết nối hệ thống!</div>`;
    }
})

const loadHistory = async () => {
    try {
        response = await fetch(`actions/get_stock_history.php`);

        const result = await response.json(); // Đợi phản hồi JSON từ PHP
        if (result.status === 'success') {
            historyTableBody.innerHTML = `<div class="alert alert-success">Lấy thành công</div>`;
            let logs = ""
            JSON.parse(result.message).forEach(log => {
                logs = logs + `<tr><td><small>${log.created_at}</small></td><td class="fw-bold">${log.product_name}</td><td><span class="text-success">+${log.quantity}</span></td><td>${log.note}</td></tr>`;
            });
            historyTableBody.innerHTML=logs;
        } else {
            historyTableBody.innerHTML = `<div class="alert alert-danger">Lỗi kết nối hệ thống!</div>`;
        }
    } catch (error) {
        console.log(error)
        historyTableBody.innerHTML = `<div class="alert alert-danger">Lỗi kết nối hệ thống!</div>`;
    }
}

loadHistory()
const productIdSelect = document.getElementById("productSelect");
const importForm = document.getElementById("importForm");
const responseMessage = document.getElementById("response-message");
const historyTableBody = document.getElementById("historyTableBody")

document.querySelector(".name").addEventListener("focusin", (e)=>toggleShowSearchBox(e.target,"show"))
document.querySelector(".name").addEventListener("focusout", (e)=>toggleShowSearchBox(e.target,"hide"))

const searchProducts = async (e) => {
    let searchBox = e.target.parentNode.querySelector(".search-box");
    const search = e.target.value;
    let products = []
    let productOption = '';
    try {
        const response = await fetch(`actions/get_product.php?search=${search}`);
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
        productOption = productOption + `
        <button onclick='setId(event, [${products[i]["id"]},"${products[i]["name"]}"])' class="btn border btn-primary">${products[i]["sku"]} - ${products[i]["name"]}</button>
        `
    }
    if (productOption=="") {
        searchBox.innerHTML = `<p>Không tìm thấy sản phẩm</p>`;
    }else{
        searchBox.innerHTML = productOption;
    }
}

const setId = (e, product) => {
    e.target.parentNode.parentNode.querySelector(".id").value = product[0]
    e.target.parentNode.parentNode.querySelector(".name").value = product[1]
}

const toggleShowSearchBox = (target,action) => {
    let searchBox =target.parentNode.querySelector(".search-box");
    if (action=="show") {
        searchBox.style.display = "block"
    }else if(action == "hide"){
        setTimeout(() => {
            searchBox.style.display = "none"
        }, 150);
    }
}

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
const productIdSelect = document.getElementById("productSelect");
const responseMessage = document.getElementById("response-message");

let selectProductCache = [];

document.addEventListener("keypress",(e)=>{
    if (e.key=="Enter") {
        addToTable();
    }
})

document.querySelector(".name").addEventListener("focusin", (e)=>toggleShowSearchBox(e.target,"show"))
document.querySelector(".name").addEventListener("focusout", (e)=>toggleShowSearchBox(e.target,"hide"))

const searchCustomers = async (e) => {
    let searchBox = e.target.parentNode.querySelector(".search-box");
    e.target.parentNode.parentNode.querySelector(".id").value = 1
    const search = e.target.value;
    let customers = []
    let customerOption = '';
    try {
        const response = await fetch(`actions/get_customer.php?search=${search}`);
        const result = await response.json(); // Đợi phản hồi JSON từ PHP
        if (result.status === 'success') {
            customers= JSON.parse(result.message);
        } else {
            customers = [];
        }
    } catch (error) {
        customers = [];
    }
    for (let i = 0; i < customers.length; i++) {
        customerOption = customerOption + `
        <button onclick='setId(event, [${customers[i]["id"]},"${customers[i]["name"]}"])' class="btn border btn-primary">${customers[i]["id"]} - ${customers[i]["name"]}</button>
        `
    }
    if (customerOption=="") {
        searchBox.innerHTML = `<p>Không tìm thấy khách hàng</p>`;
    }else{
        searchBox.innerHTML = customerOption;
    }
}

const setId = (e, customer) => {
    e.target.parentNode.parentNode.querySelector(".id").value = customer[0]
    e.target.parentNode.parentNode.querySelector(".name").value = customer[1]
}

const addToTable = () => {
    cost_price = 0;
    selling_price = 0;

    const tr = document.createElement("tr");
    tr.innerHTML = `
                            <td class="d-none">
                                <input class="id form-control" type="number">
                            </td>
                            <td class="position-relative">
                                <input oninput="searchProducts(event)" class="name form-control" type="text">
                                <div style="display: none;" class="z-3 l-0 w-100 search-box bg-white position-absolute row"></div>
                            </td>
                            <td>
                                <input class="unit form-control" type="text">
                            </td>
                            <td>
                                <input type="number" class="form-control qty-input" value="1" min="1" onchange="calculateRow(this)">
                            </td>
                            <td>
                                <input type="text" class="form-control" value="${Number(selling_price).toLocaleString('vi-VN')}" oninput="formatCurrency(this);calculateRow(this.parentNode.querySelector('.selling-price-input'))">
                                <input type="hidden" class="form-control selling-price-input" value="${selling_price}">
                                <input style="display:none;" type="number" class="form-control cost-price-input" value="${cost_price}" onchange="calculateRow(this)">
                            </td>
                                <td class="subtotal fw-bold">${Number(selling_price).toLocaleString('vi-VN')} ₫</td>
                            <td>                 
                                <button class="btn btn-sm btn-outline-danger" onclick="removeRow(this)">Xóa</button>
                            </td>
                    `;
    document.getElementById('invoiceItems').appendChild(tr);
    tr.querySelector(".name").addEventListener("focusin", (e)=>toggleShowSearchBox(e.target,"show"))
    tr.querySelector(".name").addEventListener("focusout", (e)=>{
        toggleShowSearchBox(e.target,"hide");
    })
    tr.querySelector(".name").focus();
}

const toggleShowSearchBox = (target,action) => {
    let searchBox =target.parentNode.querySelector(".search-box");
    if (action=="show") {
        searchBox.style.display = "block"
    }else if(action == "hide"){
        if (selectProductCache.length != 0) {
            addToRow(selectProductCache[0],selectProductCache[1])
        }
        setTimeout(() => {
            searchBox.style.display = "none"
        }, 100);
    }
}

const searchProducts = async (e) => {
    let searchBox = e.target.parentNode.querySelector(".search-box");
    const search = e.target.value;
    const id = e.target.closest("tr").querySelector(".id");
    const cost_price = e.target.closest("tr").querySelector(".cost-price-input");
    id.value = 1;
    cost_price.value =0;
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
        <button onmouseover='selectProductCache=[event,[${products[i]["id"]},"${products[i]["name"].replace(/\n/g, "")}","${products[i]["unit"]}",${products[i]["cost_price"]},${products[i]["selling_price"]}]]' class="btn border btn-primary">${products[i]["sku"]} - ${products[i]["name"]}</button>
        `
    }
    if (productOption=="") {
        searchBox.innerHTML = `<p>Không tìm thấy sản phẩm</p>`;
    }else{
        searchBox.innerHTML = productOption;
    }
}

const addToRow = (e, data) => {
    selectProductCache = [];
    const row = e.target.closest('tr');
    const id = row.querySelector(".id");
    const name = row.querySelector(".name");
    const unit = row.querySelector(".unit");
    const cost_price = row.querySelector(".cost-price-input")
    const selling_price = row.querySelector(".selling-price-input");
    id.value = data[0];
    // ẩn cửa sổ search
    name.value = "";
    toggleShowSearchBox(name,"hide")
    name.value = data[1];
    unit.value = data[2];
    cost_price.value = data[3];
    selling_price.parentNode.querySelectorAll("input")[0].value = Number(data[4]).toLocaleString('vi-VN')
    selling_price.value = data[4];
    // console.log(data)
    calculateRow(e.target);
    addToTable();
} 

function removeRow(btn) {
    btn.closest('tr').remove();
    updateTotal();
}

function calculateRow(input) {
    const row = input.closest('tr');
    const qty = row.querySelector('.qty-input').value;
    const selling_price = row.querySelector('.selling-price-input').value;
    const subtotal = qty * selling_price;
    row.querySelector('.subtotal').innerText = subtotal.toLocaleString('vi-VN') + " ₫";
    updateTotal();
}

function updateTotal() {
    let total = 0;
    let totalDisplay = document.getElementById('totalDisplay')
    let amount1 = document.getElementById('amount_1')
    let amount2 = document.getElementById('amount_2')
    document.querySelectorAll('#invoiceItems tr').forEach(row => {
        const qty = row.querySelector('.qty-input').value;
        const selling_price = row.querySelector('.selling-price-input').value;
        total += qty * selling_price;
    });
    totalDisplay.innerText = (total).toLocaleString('vi-VN') + " ₫";
    amount2.parentNode.querySelector(".amount").value = total + (amount1.value)*1
    formatCurrency(amount2.parentNode.querySelector(".amount"))
}

async function submitInvoice() {
    if(document.getElementById("id").value == ""){
        responseMessage.innerHTML = `<div class="alert alert-danger">Vui lòng nhập tên khách hàng</div>`;
        return;
    }
    let fail = false;
    const customer = {
        customer_id: document.getElementById("id").value,
        customer_name: document.getElementById("customer_name").value,
        address: document.getElementById("address").value,
    };
    const items = [];
    document.querySelectorAll('#invoiceItems tr').forEach(row => {
        if (row.querySelector('.name').value==""||row.querySelector('.unit').value==""||row.querySelector('.qty-input').value==""||row.querySelector('.selling-price-input').value=="") {
            // console.log(row.querySelector('.name').value)
            fail = true;
            return;
        }
        items.push({
            product_id: row.querySelector('.id').value,
            name: row.querySelector('.name').value,
            unit: row.querySelector('.unit').value,
            quantity: row.querySelector('.qty-input').value,
            cost_price: row.querySelector('.cost-price-input').value,
            selling_price: row.querySelector('.selling-price-input').value
        });
    });

    if (fail) {
        responseMessage.innerHTML = `<div class="alert alert-danger">Vui lòng nhập hết thông tin các dòng hoặc xóa dòng đó</div>`;
        return;
    }
    const order = {
        // paid: document.getElementById("paidDisplay").querySelector(".paid-input").value,
        extra_label_1: document.getElementById("label_1").value,
        extra_value_1: document.getElementById("amount_1").value,
        extra_label_2: document.getElementById("label_2").value,
        debt_amount: document.getElementById("amount_2").value
    }
    const formData = new FormData;
    formData.append("customer",JSON.stringify(customer))
    formData.append("items",JSON.stringify(items))
    formData.append("order",JSON.stringify(order))
    try {
        const response = await fetch('actions/create_order.php', {
            method: 'POST',
            body: formData
        });
    
        const result = await response.json();
        if (result.status === 'success') {
            responseMessage.innerHTML = `<div class="alert alert-success">Tạo hóa đơn thành công!</div>`;
            window.location.href = `print_invoice.php?invoice_no=${result.invoice_no}`;

        } else {
            responseMessage.innerHTML = `<div class="alert alert-danger">${result.message}</div>`;
        }
        
    } catch (error) {
        responseMessage.innerHTML = `<div class="alert alert-danger">${error}</div>`;
    }
}

function formatCurrency(input) {
    
    let value = input.value;

    // 1. Kiểm tra xem có dấu trừ ở đầu không
    const isNegative = value.startsWith('-');

    // 2. Lấy giá trị số (loại bỏ tất cả ký tự không phải số)
    let digits = value.replace(/\D/g, "");

    // 3. Định dạng phần số với dấu chấm hàng nghìn
    let formatted = digits !== "" ? Number(digits).toLocaleString('vi-VN') : 0;

    // 4. Ghép dấu trừ lại nếu có
    input.value = (isNegative && (digits !== "" || value === "-")) ? "-" + formatted : formatted;

    // 5. Lưu giá trị thực (số nguyên) vào ô ẩn để gửi lên PHP
    input.parentNode.querySelectorAll("input")[1].value = isNegative ? "-" + digits : digits;
}
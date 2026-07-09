const productIdSelect = document.getElementById("productSelect");
const customerIdSelect = document.getElementById("customerSelect");
const responseMessage = document.getElementById("response-message");
const parsedUrl = new URL(window.location.href);
const params = new URLSearchParams(parsedUrl.search);
const invoice_no = params.get("invoice_no");
let selectProductCache = [];
document.addEventListener("keypress",(e)=>{
    if (e.key=="Enter") {
        addToTable();
    }
})


document.querySelector(".name").addEventListener("focusin", (e)=>toggleShowSearchBox(e.target,"show"))
document.querySelector(".name").addEventListener("focusout", (e)=>toggleShowSearchBox(e.target,"hide"))

const searchCustomersImmediate = async (e) => {
    let searchBox = e.target.parentNode.querySelector(".search-box");
    e.target.parentNode.parentNode.querySelector(".id").value = 1
    const search = e.target.value;
    let customers = []
    let customerOption = '';
    try {
        const response = await abortableFetch("update-order-customers", `actions/get_customer.php?search=${encodeURIComponent(search)}`);
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

// debounce: chỉ gọi API sau khi ngừng gõ ~250ms
const searchCustomers = debounce(searchCustomersImmediate, 250)

const setId = (e, customer) => {
    e.target.parentNode.parentNode.querySelector(".id").value = customer[0]
    e.target.parentNode.parentNode.querySelector(".name").value = customer[1]
}

const addToTable = (data) => {
    const cost_price = 0;
    const selling_price = 0;

    if (data===undefined){
        const tr = document.createElement("tr");
        tr.innerHTML = `
                                <td class="d-none">
                                    <input class="id form-control" type="number">
                                    <input class="isChanged" type="checkbox">
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
                                <td class="selling-price-cotainer">
                                    <input type="text" class="form-control selling-price-input" value="${Number(selling_price).toLocaleString('vi-VN')}" oninput="formatCurrency(this);calculateRow(this.parentNode.querySelector('.selling-price-input'))">
                                    <input type="hidden" class="form-control selling-price" value="${selling_price}">
                                </td>
                                <td class="selling-price-cotainer" class="position-relative">
                                    <input type="text" class="form-control selling-price-input" value="${Number(selling_price).toLocaleString('vi-VN')}" oninput="formatCurrency(this);calculateRow(this.parentNode.querySelector('.selling-price-input'))">
                                    <input type="hidden" class="form-control selling-price" value="${selling_price}">
                                </td>
                                <td class="subtotal fw-bold">
                                    <div class="form-check m-0">
                                        <input style="cursor: pointer;" class="form-check-input price-radio" type="radio" id="price_1" value="${0}" onchange="updateSubTotal(event);" checked>
                                        <label class="form-check-label text-primary" >
                                            <p class="subtotal_choice">${Number(0).toLocaleString('vi-VN')} ₫</p>
                                        </label>
                                    </div>
                                    <div class="form-check m-0">
                                        <input style="cursor: pointer;" class="form-check-input price-radio" id="price_2" value="${0}" type="radio" onchange="updateSubTotal(event);">
                                        <label class="form-check-label text-success">
                                            <p class="subtotal_choice">${Number(0).toLocaleString('vi-VN')} ₫</p>
                                        </label>
                                    </div>
                                </td>
                                <td>                 
                                    <p class="mb-0 form-control cost-price-input-p" data-price="0" data-show="true">* ₫</p>
                                    <input type="hidden" class="form-control cost-price" value="${cost_price}" onchange="calculateRow(this)">
                                    <button class="btn btn-sm btn-outline-warning" onclick="showCostPrice(this)">Hiện giá nhập</button>    
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-outline-danger" onclick="removeRow(this)">Xóa</button>
                                </td>
                        `;
        document.getElementById('invoiceItems').appendChild(tr);
        tr.querySelector(".name").addEventListener("focusin", (e)=>toggleShowSearchBox(e.target,"show"))
        tr.querySelector(".name").addEventListener("focusout", (e)=>toggleShowSearchBox(e.target,"hide"))
        tr.querySelector(".name").focus();
    }else{

        data.forEach(item=>{
            // console.log(item)
            const tr = document.createElement("tr");
            tr.innerHTML = `
                                    <td class="d-none">
                                        <input class="id form-control" type="number" value="${item["product_id"]}">
                                        <input class="isChanged" type="checkbox" checked>
                                    </td>
                                    <td class="position-relative">
                                        <input oninput="searchProducts(event)" class="name form-control" type="text" value="${item["name"]}">
                                        <div style="display: none;" class="z-3 l-0 w-100 search-box bg-white position-absolute row"></div>
                                    </td>
                                    <td>
                                        <input class="unit form-control" type="text" value="${item["unit"]}">
                                    </td>
                                    <td>
                                        <input type="number" value="${Number(item["quantity"])}" class="form-control qty-input" onchange="calculateRow(this)">
                                    </td>
                                    <td class="selling-price-cotainer">
                                        <input type="text" class="form-control selling-price-input" value="${Number(item["selling_price_1"]).toLocaleString('vi-VN')}" oninput="formatCurrency(this);calculateRow(this.parentNode.querySelector('.selling-price-input'))">
                                        <input type="hidden" class="form-control selling-price" value="${item["selling_price_1"]}">
                                    </td>
                                    <td class="selling-price-cotainer" class="position-relative">
                                        <input type="text" class="form-control selling-price-input" value="${Number(item["selling_price_2"]).toLocaleString('vi-VN')}" oninput="formatCurrency(this);calculateRow(this.parentNode.querySelector('.selling-price-input'))">
                                        <input type="hidden" class="form-control selling-price" value="${item["selling_price_2"]}">
                                    </td>
                                    <td class="subtotal fw-bold">
                                        <div class="form-check m-0">
                                            <input style="cursor: pointer;" class="form-check-input price-radio" type="radio" id="price_1" value="${item["selling_price_1"]*item["quantity"]}" onchange="updateSubTotal(event);" ${item["choice"]==1?"checked":""}>
                                            <label class="form-check-label text-primary" >
                                                <p class="subtotal_choice">${Number(item["selling_price_1"]*item["quantity"]).toLocaleString('vi-VN')} ₫</p>
                                            </label>
                                        </div>
                                        <div class="form-check m-0">
                                            <input style="cursor: pointer;" class="form-check-input price-radio" type="radio" id="price_2" value="${item["selling_price_2"]*item["quantity"]}" onchange="updateSubTotal(event);"  ${item["choice"]==2?"checked":""}>
                                            <label class="form-check-label text-success">
                                                <p class="subtotal_choice">${Number(item["selling_price_2"]*item["quantity"]).toLocaleString('vi-VN')} ₫</p>
                                            </label>
                                        </div>
                                    </td>
                                    <td>                 
                                        <p class="mb-0 form-control cost-price-input-p" data-price="${item["cost_price"]}" data-show="true">* ₫</p>
                                        <input type="hidden" class="form-control cost-price" value="${item["cost_price"]}" onchange="calculateRow(this)">
                                        <button class="btn btn-sm btn-outline-warning" onclick="showCostPrice(this)">Hiện giá nhập</button>    
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-danger" onclick="removeRow(this)">Xóa</button>
                                    </td>
                            `;  

                            document.getElementById('invoiceItems').appendChild(tr);
                            tr.querySelector(".name").addEventListener("focusin", (e)=>toggleShowSearchBox(e.target,"show"))
                            tr.querySelector(".name").addEventListener("focusout", (e)=>toggleShowSearchBox(e.target,"hide"))
                            tr.querySelector(".name").focus();
                            calculateRow(tr.querySelector(".selling-price-input"));
                        })
    }
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

const searchProductsImmediate = async (e) => {
    let searchBox = e.target.parentNode.querySelector(".search-box");
    const search = e.target.value;
    const id = e.target.closest("tr").querySelector(".id");
    const cost_price_p = e.target.closest("tr").querySelector(".cost-price-input-p");
    const cost_price = e.target.closest("tr").querySelector(".cost-price");
    const selling_price_cotainers = e.target.closest("tr").querySelectorAll(".selling-price-cotainer");
    id.value = 1;
    cost_price_p.innerHTML = "* ₫";
    cost_price_p.setAttribute("data-show", "true")
    cost_price_p.setAttribute("data-price", 0)
    cost_price.value =0;
    for (let i = 1; i < selling_price_cotainers.length; i++) {
        selling_price_cotainers[i].innerHTML = `
            <input type="text" class="form-control selling-price-input" value="0" oninput="formatCurrency(this);calculateRow(this.parentNode.querySelector('.selling-price-input'))">
            <input type="hidden" class="form-control selling-price" value="0">
        `
    }
    let products = []
    let productOption = '';
    try {
        const response = await abortableFetch("update-order-products", `actions/get_product.php?search=${encodeURIComponent(search)}`);
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
            <button onmouseover='selectProductCache=[event,[${products[i]["id"]},"${products[i]["name"].replace(/\n/g, "")}","${products[i]["unit"]}",${products[i]["cost_price"]},${products[i]["selling_price_1"]},${products[i]["selling_price_2"]}]]' class="btn border btn-primary">${products[i]["sku"]} - ${products[i]["name"]}</button>
        `
    }
    if (productOption=="") {
        searchBox.innerHTML = `<p>Không tìm thấy sản phẩm</p>`;
    }else{
        searchBox.innerHTML = productOption;
    }
}
// debounce: chỉ gọi API sau khi ngừng gõ ~250ms
const searchProducts = debounce(searchProductsImmediate, 250)

const addToRow = (e, data) => {
    selectProductCache = [];
    const row = e.target.closest('tr');
    const id = row.querySelector(".id");
    const isChanged = row.querySelector(".isChanged");
    const name = row.querySelector(".name");
    const unit = row.querySelector(".unit");
    const cost_price_p = row.querySelector(".cost-price-input-p");
    const cost_price = row.querySelector(".cost-price");
    const selling_price = row.querySelector(".selling-price");
    const selling_price_cotainers = row.querySelectorAll(".selling-price-cotainer");
    id.value = data[0];
    // ẩn cửa sổ search
    name.value = "";
    toggleShowSearchBox(name,"hide")
    name.value = data[1];
    unit.value = data[2];
    cost_price_p.innerText = "* ₫";
    cost_price_p.setAttribute("data-show", "true");
    cost_price_p.setAttribute("data-price", data[3]);
    cost_price.value = data[3];
    for (let i = 0; i < selling_price_cotainers.length; i++) {
        selling_price_cotainers[i].innerHTML = `
            <input type="text" class="form-control selling-price-input" value="${Number(data[i+4]).toLocaleString('vi-VN')}" oninput="formatCurrency(this);calculateRow(this.parentNode.querySelector('.selling-price-input'))">
            <input type="hidden" class="form-control selling-price" value="${data[i+4]}">
        `
    }
    // console.log(data)
    calculateRow(e.target);
    if (!isChanged.checked) {
        addToTable();
        isChanged.checked = true;
    }
    row.querySelector(".qty-input").focus();
    row.querySelector(".qty-input").select();
} 

const updateSubTotal = (e) => {
    const selling_price_cotainer = e.target.closest("td")
    if(e.target.id=="price_1"){
        selling_price_cotainer.querySelector("#price_2").checked = false
    }else if(e.target.id=="price_2"){
        selling_price_cotainer.querySelector("#price_1").checked = false
    }

    calculateRow(e.target);
}

function removeRow(btn) {
    btn.closest('tr').remove();
    updateTotal();
}

function showCostPrice(btn) {
    const cost_price_input_p = btn.parentNode.parentNode.querySelector(".cost-price-input-p");
    if (cost_price_input_p.getAttribute("data-show")=="true") {
        cost_price_input_p.innerText = `${Intl.NumberFormat('vi-VN').format(cost_price_input_p.getAttribute("data-price"))} ₫`;
        cost_price_input_p.setAttribute("data-show", "false")
    }else{
        cost_price_input_p.innerText = `* ₫`;
        cost_price_input_p.setAttribute("data-show", "true")
    }
}

function calculateRow(input) {
    const row = input.closest('tr');
    const qty = row.querySelector('.qty-input').value;
    const selling_prices = row.querySelectorAll('.selling-price');
    const subtotal_choices = row.querySelector('.subtotal').querySelectorAll("div");
    for (let i = 0; i < subtotal_choices.length; i++) {
        const subtotal = selling_prices[i].value * qty
        subtotal_choices[i].querySelector("input").value = subtotal;
        subtotal_choices[i].querySelector("p").innerText = subtotal.toLocaleString('vi-VN') + " ₫";
    }
    updateTotal();
}

function updateTotal() {
    let total = 0;
    let totalDisplay = document.getElementById('totalDisplay')
    let amount1 = document.getElementById('amount_1')
    let amount2 = document.getElementById('amount_2')
    let paid = document.getElementById('paid')
    document.querySelectorAll('#invoiceItems tr').forEach(row => {
        const qty = row.querySelector('.qty-input').value;
        const selling_prices = row.querySelectorAll('.selling-price');
        const price_radio_checked = row.querySelector('.price-radio:checked');
        if (price_radio_checked.id == "price_1") {
            total += qty * selling_prices[0].value;
        }else{
            total += qty * selling_prices[1].value;
        }
    });
    totalDisplay.innerText = (total).toLocaleString('vi-VN') + " ₫";
    amount2.parentNode.querySelector(".amount").value = total + (amount1.value)*1 - (paid.value)*1
    formatCurrency(amount2.parentNode.querySelector(".amount"))
}

const updateTable = async () => {
    const parsedUrl = new URL(window.location.href);
    const params = new URLSearchParams(parsedUrl.search);
    const invoice_no = params.get("invoice_no");
    try {
        response = await fetch(`actions/get_order.php?invoice_no=${invoice_no}`);

        const result = await response.json(); // Đợi phản hồi JSON từ PHP
        if (result.status === 'success') {
            responseMessage.innerHTML = `<div class="alert alert-success">Lấy thành công</div>`;
            let products = ""
            const orderJson= JSON.parse(result.message);
            const itemsJson= JSON.parse(result.items);

            if (orderJson.id) {
                responseMessage.innerHTML = ``;
                addToTable(itemsJson);
                let customerName = document.getElementById("customer_name")
                customerName.value = orderJson["customer_name"]
                let customerId = document.getElementById("id")
                customerId.value = orderJson["customer_id"]
                let customerAdrress = document.getElementById("address")
                customerAdrress.value = orderJson["address"]
                let label1 = document.getElementById('label_1')
                label1.value = orderJson["extra_label_1"]
                let label2 = document.getElementById('label_2')
                label2.value = orderJson["extra_label_2"]
                let label3 = document.getElementById('label_3')
                label3.value = orderJson["extra_label_3"]
                let amount1 = document.getElementById('amount_1')
                amount1.parentNode.querySelector(".amount").value = Math.trunc(orderJson["extra_value_1"])
                let paid = document.getElementById('paid')
                paid.parentNode.querySelector(".amount").value = Math.trunc(orderJson["paid_amount"])
                let amount2 = document.getElementById('amount_2')
                amount2.parentNode.querySelector(".amount").value = Math.trunc(orderJson["debt_amount"])
                formatCurrency(amount1.parentNode.querySelector(".amount"))
                formatCurrency(paid.parentNode.querySelector(".amount"))
                formatCurrency(amount2.parentNode.querySelector(".amount"))
                updateTotal()
            }else{
                responseMessage.innerHTML = `<div class="alert alert-danger">Không tìm thấy đơn hàng!</div>`;
            }
        } else {
            responseMessage.innerHTML = `<div class="alert alert-danger">Lỗi kết nối hệ thống!</div>`;
        }
    } catch (error) {
        console.log(error)
        responseMessage.innerHTML = `<div class="alert alert-danger">Lỗi kết nối hệ thống!</div>`;
    }
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
        if (row.querySelector('.name').value==""||row.querySelector('.unit').value==""||row.querySelector('.qty-input').value==""||row.querySelector('.selling-price').value=="") {
            // console.log(row.querySelector('.name').value)
            fail = true;
            return;
        }
        items.push({
            product_id: row.querySelector('.id').value,
            name: row.querySelector('.name').value,
            unit: row.querySelector('.unit').value,
            quantity: row.querySelector('.qty-input').value,
            cost_price: row.querySelector('.cost-price').value,
            selling_prices: [
                row.querySelectorAll('.selling-price')[0].value,
                row.querySelectorAll('.selling-price')[1].value
            ],
            choice: row.querySelector('.price-radio:checked')?.id.split('_').pop() || "",
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
        debt_amount: document.getElementById("amount_2").value,
        extra_label_3: document.getElementById("label_3").value,
        paid_amount: document.getElementById("paid").value
    }
    const formData = new FormData;
    formData.append("invoice_no",invoice_no)
    formData.append("customer", JSON.stringify(customer))
    formData.append("items",JSON.stringify(items))
    formData.append("order", JSON.stringify(order))
    try {
        const response = await fetch('actions/update_order.php', {
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
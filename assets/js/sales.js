const productIdSelect = document.getElementById("productSelect");
const customerIdSelect = document.getElementById("customerSelect");
const responseMessage = document.getElementById("response-message");

document.addEventListener("keypress",(e)=>{
    if (e.key=="Enter") {
        addToTable();
    }
})
// const productSelect = async () => {
//     let products = []
//     let productOption = '<option data-name="" data-cost-price="" data-selling-price="" data-stock="" data-unit="" value=""></option>';
//     try {
//         const response = await fetch(`actions/get_product.php`);
//         const result = await response.json(); // Đợi phản hồi JSON từ PHP
//         if (result.status === 'success') {
//             products= JSON.parse(result.message);
//         } else {
//             products = [];
//         }
//     } catch (error) {
//         products = [];
//     }
//     for (let i = 0; i < products.length; i++) {
//         productOption = productOption + `
//         <option data-name="${products[i]["name"]}" data-cost-price="${products[i]["cost_price"]}" data-selling-price="${products[i]["selling_price"]}" data-stock="${products[i]["stock_quanity"]}" data-unit="${products[i]["unit"]}" value="${products[i]["id"]}">${products[i]["sku"]} - ${products[i]["name"]}</option>
//         `
//     }
//     productIdSelect.innerHTML = productOption;
// }
// productIdSelect.addEventListener("click",productSelect);
// productSelect()

const customerSelect =  async () => {
    let customers = []
    let customerOption = '<option data-id="1" data-phone="" value="1">1 - A/C</option>';
    try {
        const response = await fetch(`actions/get_customer.php`);
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
        <option data-id="${customers[i]["id"]}" data-phone="${customers[i]["phone"]}" value="${customers[i]["id"]}">${customers[i]["id"]} - ${customers[i]["name"]}</option>
        `
    }
    customerIdSelect.innerHTML = customerOption;
}
customerSelect()
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
                                <input style="display:none;" type="number" class="form-control cost-price-input" value="${cost_price}" onchange="calculateRow(this)">
                                <input type="number" class="form-control selling-price-input" value="${selling_price}" onchange="calculateRow(this)">
                            </td>
                                <td class="subtotal fw-bold">${Number(selling_price).toLocaleString()} ₫</td>
                            <td>                 
                                <button class="btn btn-sm btn-outline-danger" onclick="removeRow(this)">Xóa</button>
                            </td>
                    `;
    document.getElementById('invoiceItems').appendChild(tr);
    tr.querySelector(".name").addEventListener("focusin", (e)=>toggleShowSearchBox(e.target))
    tr.querySelector(".name").addEventListener("focusout", (e)=>toggleShowSearchBox(e.target))
    tr.querySelector(".name").focus();
}

const toggleShowSearchBox = (target) => {
    let searchBox =target.parentNode.querySelector(".search-box");
    if(searchBox.style.display == "none"){
        searchBox.style.display = "block"
    }else{
        setTimeout(() => {
            searchBox.style.display = "none"
        }, 100);
    };
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
        <button onclick='addToRow(event, [${products[i]["id"]},"${products[i]["name"].replace(/\n/g, "")}","${products[i]["unit"]}",${products[i]["cost_price"]},${products[i]["selling_price"]}])' class="btn border btn-primary">${products[i]["sku"]} - ${products[i]["name"]}</button>
        `
    }
    if (productOption=="") {
        searchBox.innerHTML = `<p>Không tìm thấy sản phẩm</p>`;
    }else{
        searchBox.innerHTML = productOption;
    }
}

const addToRow = (e, data) => {
    const row = e.target.closest('tr');
    const id = row.querySelector(".id");
    const name = row.querySelector(".name");
    const unit = row.querySelector(".unit");
    const cost_price = row.querySelector(".cost-price-input")
    const selling_price = row.querySelector(".selling-price-input");
    id.value = data[0];
    name.value = data[1];
    unit.value = data[2];
    cost_price.value = data[3];
    selling_price.value = data[4];
    console.log(data)
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
    row.querySelector('.subtotal').innerText = subtotal.toLocaleString() + " ₫";
    updateTotal();
}

function updateTotal() {
    let total = 0;
    document.querySelectorAll('#invoiceItems tr').forEach(row => {
        const qty = row.querySelector('.qty-input').value;
        const selling_price = row.querySelector('.selling-price-input').value;
        total += qty * selling_price;
    });
    document.getElementById('totalDisplay').innerText = total.toLocaleString() + " ₫";
}

async function submitInvoice() {
    let fail = false;
    const customer = {
        customer_id: customerIdSelect.value,
        address: document.getElementById("address").value
    };
    const items = [];
    document.querySelectorAll('#invoiceItems tr').forEach(row => {
        if (row.querySelector('.name').value==""||row.querySelector('.unit').value==""||row.querySelector('.qty-input').value==""||row.querySelector('.selling-price-input').value=="") {
            console.log(row.querySelector('.name').value)
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
    const formData = new FormData;
    formData.append("customer",JSON.stringify(customer))
    formData.append("items",JSON.stringify(items))
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
        responseMessage.innerHTML = `<div class="alert alert-danger">${result.message}</div>`;
    }
}
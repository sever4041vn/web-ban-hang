const productsDiv = document.getElementById("products");
const search = document.getElementById("search");
let searchValue = search.value
let page = 1
search.addEventListener("input", ()=>{
    searchValue = search.value
    getProducts(searchValue,page)
})


//Lấy sản phẩm
const getProducts = async (search, page) => {
    try {
        let response;
        if (search!="") {
            response = await fetch(`actions/get_product.php?search=${search}`);
        }else{
            response = await fetch(`actions/get_product.php?page=${page}`);
        }

        const result = await response.json(); // Đợi phản hồi JSON từ PHP
        if (result.status === 'success') {
            productsDiv.innerHTML = `<div class="alert alert-success">Lấy thành công</div>`;
            let products = ""
            const productsJson= JSON.parse(result.message);
            if (productsJson.length==0) {
                productsDiv.innerHTML = `Không tìm thấy sản phẩm`;
            }else{

            productsJson.map(product=>{
                const profitTotal = product["selling_price"]-product["cost_price"];
                const profitTotalSpan = profitTotal>0?`<span data-price="${profitTotal}" class="profit badge bg-success">*</span>`:`<span data-price="${profitTotal}" class="profit badge bg-danger">*</span>`;
                products = products + `<tr id=${product["id"]} data-id=${product["id"]}>
                <td>${product["sku"]}</td>
                <td ondblclick="changeNameFocus(this)" class="product_name" data-name="${product["name"]}">${product["name"]}</td>
                <td ondblclick="changeStockFocus(this)" class="stock_quantity" data-stock="${parseFloat(product["stock_quantity"])}">${parseFloat(product["stock_quantity"])}</td>
                <td>${product["unit"]}</td>
                <td ondblclick="changePriceFocus(this)" class="cost_price" data-price="${product["cost_price"]}" data-show="true">
                    * ₫
                </td>
                <td ondblclick="changePriceFocus(this)" class="selling_price_1" data-price="${product["selling_price_1"]}">
                    ${Intl.NumberFormat('vi-VN').format(product["selling_price_1"])} ₫
                </td>
                <td ondblclick="changePriceFocus(this)" class="selling_price_2" data-price="${product["selling_price_2"]}">
                    ${Intl.NumberFormat('vi-VN').format(product["selling_price_2"])} ₫
                </td>
                <td>
                    <button onclick="showHideProfit(${product["id"]})" class="btn btn-sm btn-outline-secondary">Xem giá nhập</button>
                    <button onclick="deleteProduct('${product["sku"]}','${product["name"]}','${product["id"]}')" class="btn btn-sm btn-danger">Xóa</button>
                </td>
                </tr>`;
            })
            productsDiv.innerHTML = products;

            }
        } else {
            productsDiv.innerHTML = `<div class="alert alert-danger">Lỗi kết nối hệ thống!</div>`;
        }
    } catch (error) {
        console.log(error)
        productsDiv.innerHTML = `<div class="alert alert-danger">Lỗi kết nối hệ thống!</div>`;
    }
}

const toPage = (action) => {
    if (action == "add") {
        page -= 1
    }else if (action == "minus"){
        page += 1
    }else if (action == "go"){
        page = document.getElementById("gotopage").value
    }
    document.getElementById("gotopage").value = page
    getProducts (searchValue,page)
}
toPage("action")
//Xem giá nhập
const showHideProfit = (id) => {
    const div =document.getElementById(id).getElementsByClassName("cost_price")[0];

    if (div.getAttribute("data-show")=="true") {
        div.innerHTML = `${Intl.NumberFormat('vi-VN').format(div.getAttribute("data-price"))} ₫`;
        div.setAttribute("data-show", "false")
    }else{
        div.innerHTML = `* ₫`;
        div.setAttribute("data-show", "true")
    }
}
//CHỉnh sửa tồn kho
const changeStockFocus = (target) => {
    const product = target.parentElement
    const product_id = product.getAttribute("data-id")
    const stock_quantity = product.getElementsByClassName("stock_quantity")[0].getAttribute("data-stock")
    target.innerHTML = "";
    const input = document.createElement("input");
    input.className = "form-control";
    input.style.width = "110px"
    input.value = target.getAttribute("data-stock");
    target.appendChild(input);
    input.focus();
    input.addEventListener("focusout",()=>changeStock(product_id,input.value))
}
//CHỉnh sửa tên hàng
const changeNameFocus = (target) => {
    const product = target.parentElement
    const product_id = product.getAttribute("data-id")
    const product_name = product.getElementsByClassName("product_name")[0].getAttribute("data-name")
    target.innerHTML = "";
    const input = document.createElement("input");
    input.className = "form-control";
    input.style.width = "110px"
    input.value = target.getAttribute("data-name");
    target.appendChild(input);
    input.focus();
    input.addEventListener("focusout",()=>changeName(product_id,input.value))
}
//CHỉnh sửa giá
const changePriceFocus = (target) => {
    const product = target.parentElement
    const product_id = product.getAttribute("data-id")
    const cost_price = product.getElementsByClassName("cost_price")[0].getAttribute("data-price")
    const selling_price_1 = product.getElementsByClassName("selling_price_1")[0].getAttribute("data-price")
    const selling_price_2 = product.getElementsByClassName("selling_price_2")[0].getAttribute("data-price")
    target.innerHTML = "";
    const input = document.createElement("input");
    input.className = "form-control";
    input.style.width = "110px"
    input.value = Number(target.getAttribute("data-price")).toLocaleString('vi-VN');
    input.addEventListener("input",()=>formatCurrency(input))
    target.appendChild(input);
    input.focus();

    const input2 = document.createElement("input");
    input2.className = "form-control";
    input2.style.width = "110px"
    input2.value = target.getAttribute("data-price");
    input2.hidden = true;
    input2.type = "number"
    target.appendChild(input2);
    if (target.className == "cost_price" ) {
        input.addEventListener("focusout",()=>changePrice(product_id,input2.value,selling_price_1,selling_price_2))
    }else if (target.className == "selling_price_1" ){
        input.addEventListener("focusout",()=>changePrice(product_id,cost_price,input2.value,selling_price_2))
    }else{
        input.addEventListener("focusout",()=>changePrice(product_id,cost_price,selling_price_1,input2.value))
    }
}
const changePrice = async (product_id, cost_price, selling_price_1, selling_price_2) => {
    // console.log(product_id, cost_price, selling_price)
    try {
        const formData = new FormData;
        formData.append("product_id",product_id);
        formData.append("cost_price",cost_price);
        formData.append("selling_price_1",selling_price_1);
        formData.append("selling_price_2",selling_price_2);
        response = await fetch(`actions/change_price.php`,{
            method:"POST",
            body: formData
        });

        const result = await response.json(); // Đợi phản hồi JSON từ PHP
        if (result.status === 'success') {
            const div = document.createElement("div")
            div.className = "alert alert-success";
            div.innerHTML = result.message;
            responseMessage.appendChild(div);
            setTimeout(() => {
                div.remove();
            }, 3000);
        } else {
            const div = document.createElement("div")
            div.className = "alert alert-danger";
            div.innerHTML = result.message;
            responseMessage.appendChild(div);
            setTimeout(() => {
                div.remove();
            }, 3000);
        }
    } catch (error) {
        console.log(error)
        const div = document.createElement("div")
        div.className = "alert alert-success";
        div.innerHTML = "Lỗi kết nối máy chủ";
        responseMessage.appendChild(div);
        setTimeout(() => {
            div.remove();
        }, 3000);
    }
    getProducts(searchValue,page)
}

//Thay đổi tồn kho
const changeStock = async (product_id, stock_quantity) => {
    // console.log(product_id, cost_price, selling_price)
    try {
        const formData = new FormData;
        formData.append("product_id",product_id);
        formData.append("quantity",stock_quantity);

        response = await fetch(`actions/post_stock.php`,{
            method:"POST",
            body: formData
        });

        const result = await response.json(); // Đợi phản hồi JSON từ PHP
        if (result.status === 'success') {
            const div = document.createElement("div")
            div.className = "alert alert-success";
            div.innerHTML = result.message;
            responseMessage.appendChild(div);
            setTimeout(() => {
                div.remove();
            }, 3000);
        } else {
            const div = document.createElement("div")
            div.className = "alert alert-danger";
            div.innerHTML = result.message;
            responseMessage.appendChild(div);
            setTimeout(() => {
                div.remove();
            }, 3000);
        }
    } catch (error) {
        console.log(error)
        const div = document.createElement("div")
        div.className = "alert alert-success";
        div.innerHTML = "Lỗi kết nối máy chủ";
        responseMessage.appendChild(div);
        setTimeout(() => {
            div.remove();
        }, 3000);
    }
    getProducts(searchValue,page)
}
//Thay đổi tên
const changeName = async (product_id, product_name) => {
    // console.log(product_id, product_name)
    try {
        const formData = new FormData;
        formData.append("product_id",product_id);
        formData.append("product_name",product_name);

        response = await fetch(`actions/update_name.php`,{
            method:"POST",
            body: formData
        });

        const result = await response.json(); // Đợi phản hồi JSON từ PHP
        if (result.status === 'success') {
            const div = document.createElement("div")
            div.className = "alert alert-success";
            div.innerHTML = result.message;
            responseMessage.appendChild(div);
            setTimeout(() => {
                div.remove();
            }, 3000);
        } else {
            const div = document.createElement("div")
            div.className = "alert alert-danger";
            div.innerHTML = result.message;
            responseMessage.appendChild(div);
            setTimeout(() => {
                div.remove();
            }, 3000);
        }
    } catch (error) {
        console.log(error)
        const div = document.createElement("div")
        div.className = "alert alert-success";
        div.innerHTML = "Lỗi kết nối máy chủ";
        responseMessage.appendChild(div);
        setTimeout(() => {
            div.remove();
        }, 3000);
    }
    getProducts(searchValue,page)
}
function formatCurrency(input) {
    // console.log(input)
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
getProducts("")
const productsDiv = document.getElementById("products");
const search = document.getElementById("search");
let searchValue = search.value
let page = 1
search.addEventListener("input", ()=>{
    searchValue = search.value
    getProducts(searchValue)
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
                <td>${product["name"]}</td>
                <td>${product["stock_quantity"]}</td>
                <td>${product["unit"]}</td>
                <td ondblclick="changePriceFocus(this)" class="cost_price" data-price="${product["cost_price"]}" data-show="true">* ₫</td>
                <td ondblclick="changePriceFocus(this)" class="selling_price" data-price="${product["selling_price"]}">${Intl.NumberFormat('vi-VN').format(product["selling_price"])} ₫</td>
                <td>
                    ${profitTotalSpan}
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
    const profit =document.getElementById(id).getElementsByClassName("profit")[0];

    if (div.getAttribute("data-show")=="true") {
        div.innerHTML = `${Intl.NumberFormat('vi-VN').format(div.getAttribute("data-price"))} ₫`;
        profit.innerHTML = `${Intl.NumberFormat('vi-VN').format(profit.getAttribute("data-price"))} ₫`;
        div.setAttribute("data-show", "false")
    }else{
        div.innerHTML = `* ₫`;
        profit.innerHTML = `* ₫`;
        div.setAttribute("data-show", "true")
    }
}
//CHỉnh sửa giá
const changePriceFocus = (target) => {
    const product = target.parentElement
    const product_id = product.getAttribute("data-id")
    const cost_price = product.getElementsByClassName("cost_price")[0].getAttribute("data-price")
    const selling_price = product.getElementsByClassName("selling_price")[0].getAttribute("data-price")
    target.innerHTML = "";
    const input = document.createElement("input");
    input.className = "form-control";
    input.style.width = "110px"
    input.value = target.getAttribute("data-price");
    target.appendChild(input);
    input.focus();
    if (target.className == "cost_price" ) {
        input.addEventListener("focusout",()=>changePrice(product_id,input.value,selling_price))

    }else{
        input.addEventListener("focusout",()=>changePrice(product_id,cost_price,input.value))
    }
}
const changePrice = async (product_id, cost_price, selling_price) => {
    // console.log(product_id, cost_price, selling_price)
    try {
        const formData = new FormData;
        formData.append("product_id",product_id);
        formData.append("cost_price",cost_price);
        formData.append("selling_price",selling_price);
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
    getProducts(searchValue)
}
getProducts("")
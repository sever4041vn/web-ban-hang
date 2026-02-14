const addProduct = document.getElementById("add-product");
const responseMessage = document.getElementById("response-message");

const costPrice = document.getElementById("cost_price");
const sellingPrice = document.getElementById("selling_price"); 
const profit = document.getElementById("profit");


//Tính lợi nhuận
const profitCount = () => {
    const profitTotal = sellingPrice.value - costPrice.value;
    if (profitTotal>0) {
        profit.innerHTML = `<div class="alert alert-success">${profitTotal}</div>`
    }else{
        profit.innerHTML = `<div class="alert alert-danger">${profitTotal}</div>`
    }
}
costPrice.addEventListener("input",profitCount);
sellingPrice.addEventListener("input",profitCount);

//Thêm sản phẩm
addProduct.addEventListener("submit", async (e)=>{
    e.preventDefault();
    const formData = new FormData(addProduct);

    try {
        const response = await fetch('actions/add_product.php', {
            method: 'POST',
            body: formData
        });

        const result = await response.json(); // Đợi phản hồi JSON từ PHP

        if (result.status === 'success') {
            responseMessage.innerHTML = `<div class="alert alert-success">${result.message}</div>`;
        } else {
            responseMessage.innerHTML = `<div class="alert alert-danger">${result.message}</div>`;
        }
    } catch (error) {
        console.log(error)
        responseMessage.innerHTML = `<div class="alert alert-danger">Lỗi kết nối hệ thống!</div>`;
    }
})
//Thêm sản phẩm bằng file csv
addProduct.addEventListener("submit", async (e)=>{
    e.preventDefault();
    const formData = new FormData(addProduct);

    try {
        const response = await fetch('actions/add_product.php', {
            method: 'POST',
            body: formData
        });

        const result = await response.json(); // Đợi phản hồi JSON từ PHP

        if (result.status === 'success') {
            responseMessage.innerHTML = `<div class="alert alert-success">${result.message}</div>`;
        } else {
            responseMessage.innerHTML = `<div class="alert alert-danger">${result.message}</div>`;
        }
    } catch (error) {
        console.log(error)
        responseMessage.innerHTML = `<div class="alert alert-danger">Lỗi kết nối hệ thống!</div>`;
    }
})
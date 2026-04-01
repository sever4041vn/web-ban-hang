const addProduct = document.getElementById("add-product");

const responseMessage = document.getElementById("response-message");

const costPrice = document.getElementById("cost_price");
const sellingPrice = document.getElementById("selling_price"); 
const profit = document.getElementById("profit");


//Tính lợi nhuận
const profitCount = () => {
    const profitTotal = sellingPrice.value - costPrice.value;
    if (profitTotal>0) {
        profit.innerHTML = `<div class="alert alert-success">${Number(profitTotal).toLocaleString('vi-VN')}</div>`
    }else{
        profit.innerHTML = `<div class="alert alert-danger">${Number(profitTotal).toLocaleString('vi-VN')}</div>`
    }
}

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
        profitCount()
    } catch (error) {
        console.log(error)
        responseMessage.innerHTML = `<div class="alert alert-danger">Lỗi kết nối hệ thống!</div>`;
    }
})

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
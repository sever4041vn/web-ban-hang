const addProductFile = document.getElementById("add-product-file");
const file = document.getElementById("file")
const responseMessageFile = document.getElementById("response-message-file")

addProductFile.addEventListener("submit", async (e)=>{
    e.preventDefault()
    const warning = document.createElement("div")
    warning.className = "alert alert-warning";
    warning.innerHTML = "Đang xử lý...";
    responseMessageFile.appendChild(warning);
    const formData = new FormData();
    formData.append("file", file.files[0]);
    try {
        const response = await fetch('actions/add_product_file.php', {
            method: 'POST',
            body: formData
        });

        const result = await response.json(); // Đợi phản hồi JSON từ PHP
        warning.remove()
        if (result.status === 'success') {
            const div = document.createElement("div")
            div.className = "alert alert-success";
            div.innerHTML = result.message;
            responseMessageFile.appendChild(div);
            setTimeout(() => {
                div.remove();
            }, 10000);
        } else {
            const div = document.createElement("div")
            div.className = "alert alert-danger";
            div.innerHTML = result.message;
            responseMessageFile.appendChild(div);
            setTimeout(() => {
                div.remove();
            }, 3000);
        }
    } catch (error) {
        console.log(error)
        const div = document.createElement("div")
        div.className = "alert alert-success";
        div.innerHTML = "Lỗi kết nối máy chủ";
        responseMessageFile.appendChild(div);
        setTimeout(() => {
            div.remove();
        }, 3000);
    }
})
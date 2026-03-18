const responseMessage = document.getElementById("response-message");

const deleteProduct = async (sku,name,id) => {
    const sure = confirm(`Xóa sản phẩm có sku và tên là :${sku},${name}`);
    if (!sure) {
        return;
    }
    try {
        const formData = new FormData;
        formData.append("sku",sku);
        formData.append("name",name);
        formData.append("id",id);
        const response = await fetch('actions/delete_product.php', {
            method: 'POST',
            body: formData
        });

        const result = await response.json(); // Đợi phản hồi JSON từ PHP
        if (result.status === 'success') {
            responseMessage.innerHTML = `<div class="alert alert-success">Xóa thành công</div>`;
        } else {
            responseMessage.innerHTML = `<div class="alert alert-danger">Lỗi kết nối hệ thống!</div>`;
        }
        getProducts("")
    } catch (error) {
        console.log(error)
        responseMessage.innerHTML = `<div class="alert alert-danger">Lỗi kết nối hệ thống!</div>`;
    }
    getProducts("")
}
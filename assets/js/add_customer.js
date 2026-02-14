const customerForm = document.getElementById("customer-form");
const responseMessageCustomer = document.getElementById("response-message-add");

customerForm.addEventListener("submit", async (e)=>{
    e.preventDefault()
    const formData = new FormData(customerForm);
    try {
        response = await fetch(`actions/add_customer.php`,{
            method:"POST",
            body: formData
        });

        const result = await response.json(); // Đợi phản hồi JSON từ PHP
        if (result.status === 'success') {
            const div = document.createElement("div")
            div.className = "alert alert-success";
            div.innerHTML = result.message;
            responseMessageCustomer.appendChild(div);
            customerForm.reset()
            setTimeout(() => {
                div.remove();
            }, 3000);

        } else {
            const div = document.createElement("div")
            div.className = "alert alert-danger";
            div.innerHTML = result.message;
            responseMessageCustomer.appendChild(div);
            setTimeout(() => {
                div.remove();
            }, 3000);
        }
    } catch (error) {
        console.log(error)
        const div = document.createElement("div")
        div.className = "alert alert-success";
        div.innerHTML = "Lỗi kết nối máy chủ";
        responseMessageCustomer.appendChild(div);
        setTimeout(() => {
            div.remove();
        }, 3000);
    }
    getCustomers("")
    
})
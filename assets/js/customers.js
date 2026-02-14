const customersDiv = document.getElementById("customers");
const search = document.getElementById("search");
let searchValue = search.value
search.addEventListener("input", ()=>{
    searchValue = search.value
    getCustomers(searchValue)
})
//Lấy khách hàng
const getCustomers = async (search) => {
    try {
        let response;
        if (search!="") {
            response = await fetch(`actions/get_customer.php?search=${search}`);
        }else{
            response = await fetch(`actions/get_customer.php`);
        }

        const result = await response.json(); // Đợi phản hồi JSON từ PHP
        if (result.status === 'success') {
            customersDiv.innerHTML = `<div class="alert alert-success">Lấy thành công</div>`;
            let customers = ""
            const customersJson= JSON.parse(result.message);
            if (customersJson.length==0) {
                customersDiv.innerHTML = `Không tìm thấy khách hàng`;
            }else{

            customersJson.map(customer=>{customers= customers + `
                <td>${customer["id"]}</td>
                <td>${customer["name"]}</td>
                <td>${customer["phone"]?customer["phone"]:"Không có"}</td>
                <td>${customer["address"]?customer["address"]:"Không có"}</td>
                </tr>`;
            })
            customersDiv.innerHTML = customers;

            }
        } else {
            customersDiv.innerHTML = `<div class="alert alert-danger">Lỗi kết nối hệ thống!</div>`;
        }
    } catch (error) {
        console.log(error)
        customersDiv.innerHTML = `<div class="alert alert-danger">Lỗi kết nối hệ thống!</div>`;
    }
}
getCustomers("")
const addUnit = document.getElementById("add-product");
const responseMessage = document.getElementById("response-message");

//Thêm đơn vị tính
addUnit.addEventListener("submit", async (e)=>{
    e.preventDefault();
    const formData = new FormData(addUnit);

    try {
        const response = await fetch('actions/add_unit.php', {
            method: 'POST',
            body: formData
        });

        const result = await response.json(); // Đợi phản hồi JSON từ PHP

        if (result.status === 'success') {
            responseMessage.innerHTML = `<div class="alert alert-success">${result.message}</div>`;
        } else {
            responseMessage.innerHTML = `<div class="alert alert-danger">${result.message}</div>`;
        }
        getUnit()
    } catch (error) {
        console.log(error)
        responseMessage.innerHTML = `<div class="alert alert-danger">Lỗi kết nối hệ thống!</div>`;
    }
})

const unitsDiv = document.getElementById("units")

const getUnit = async () => {
    try {
        const response = await fetch('actions/get_unit.php');

        const result = await response.json(); // Đợi phản hồi JSON từ PHP

        if (result.status === 'success') {
            unitsDiv.innerHTML = `<div class="alert alert-success">Lấy thành công</div>`;
            let units = ""
            const unitsJson= JSON.parse(result.message);
            if (unitsJson.length==0) {
                unitsDiv.innerHTML = `Không tìm thấy đơn vị tính`;
            }else{

            unitsJson.map(unit=>{
                units = units + `<tr id=${unit["id"]} data-id=${unit["id"]}>
                <td>${unit["id"]}</td>
                <td>${unit["name"]}</td>
                <td>${unit["description"]}</td>
                </tr>`;
            })
            unitsDiv.innerHTML = units;

            }
        } else {
            unitsDiv.innerHTML = `<div class="alert alert-danger">Lỗi kết nối hệ thống!</div>`;
        }
    } catch (error) {
        console.log(error)
        responseMessage.innerHTML = `<div class="alert alert-danger">Lỗi kết nối hệ thống!</div>`;
    }
}

getUnit()
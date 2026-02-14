const unitSelect = document.getElementById("unit");

const getUnits = async () => {
    try {

        response = await fetch(`actions/get_unit.php`);

        const result = await response.json(); // Đợi phản hồi JSON từ PHP
        if (result.status === 'success') {
            unitSelect.innerHTML = `<div class="alert alert-success">Lấy thành công</div>`;
            let units = ""
            JSON.parse(result.message).forEach(unit => {
                units = units + `<option value="${unit["name"]}">${unit["name"]}</option>`;
            });
            unitSelect.innerHTML=units;
        } else {
            unitSelect.innerHTML = `<div class="alert alert-danger">Lỗi kết nối hệ thống!</div>`;
        }
    } catch (error) {
        console.log(error)
        unitSelect.innerHTML = `<div class="alert alert-danger">Lỗi kết nối hệ thống!</div>`;
    }
}

getUnits("")
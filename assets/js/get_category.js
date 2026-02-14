const categorySelect = document.getElementById("category");

const getCategories = async () => {
    try {

        response = await fetch(`actions/get_category.php`);

        const result = await response.json(); // Đợi phản hồi JSON từ PHP
        if (result.status === 'success') {
            categorySelect.innerHTML = `<div class="alert alert-success">Lấy thành công</div>`;
            let units = ""
            JSON.parse(result.message).forEach(unit => {
                units = units + `<option value="${unit["id"]}">${unit["name"]}</option>`;
            });
            categorySelect.innerHTML=units;
        } else {
            categorySelect.innerHTML = `<div class="alert alert-danger">Lỗi kết nối hệ thống!</div>`;
        }
    } catch (error) {
        console.log(error)
        categorySelect.innerHTML = `<div class="alert alert-danger">Lỗi kết nối hệ thống!</div>`;
    }
}

getCategories("")
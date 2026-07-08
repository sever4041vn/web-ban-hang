/**
 * utils.js - Các hàm dùng chung để tối ưu việc gọi API từ giao diện.
 *
 * Vấn đề trước đây: các ô tìm kiếm (sản phẩm, khách hàng, đơn hàng) gắn sự kiện
 * "input" và gọi fetch() ngay lập tức mỗi khi người dùng gõ 1 ký tự. Khi gõ nhanh,
 * hàng loạt request được bắn đi gần như cùng lúc, và vì mạng/DB có độ trễ khác nhau,
 * response của request CŨ có thể về SAU response của request MỚI, khiến kết quả
 * hiển thị sai + giao diện giật do render đi render lại liên tục.
 *
 * debounce(): chỉ thực sự gọi API sau khi người dùng ngừng gõ một khoảng ngắn.
 * abortableFetch(): tự hủy request trước đó (nếu còn đang chạy) khi có request mới,
 * tránh lãng phí băng thông/CPU và tránh hiện tượng dữ liệu bị ghi đè sai thứ tự.
 */

/**
 * Trả về 1 hàm mới, hàm này sẽ chỉ chạy `fn` sau khi không còn được gọi lại
 * trong vòng `delay` ms. Dùng cho các ô input tìm kiếm.
 */
function debounce(fn, delay = 300) {
    let timer = null;
    return function (...args) {
        clearTimeout(timer);
        timer = setTimeout(() => fn.apply(this, args), delay);
    };
}

/**
 * Quản lý 1 "khe" request theo key: mỗi khi gọi lại với cùng key,
 * request trước đó (nếu chưa xong) sẽ bị abort trước khi bắn request mới.
 * Giúp tránh trường hợp gõ nhanh -> nhiều request chạy song song -> UI giật/nhấp nháy.
 */
const __pendingRequests = {};
async function abortableFetch(key, url) {
    if (__pendingRequests[key]) {
        __pendingRequests[key].abort();
    }
    const controller = new AbortController();
    __pendingRequests[key] = controller;
    try {
        const response = await fetch(url, { signal: controller.signal });
        return response;
    } finally {
        if (__pendingRequests[key] === controller) {
            delete __pendingRequests[key];
        }
    }
}

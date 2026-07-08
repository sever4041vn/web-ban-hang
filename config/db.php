<?php
$host = 'localhost';
$db   = 'inventory_manager';
$user = 'root';
$pass = ''; // Mặc định của XAMPP là trống

try {
    // charset utf8mb4 khớp với charset thật của DB -> tránh convert ngầm
    // ATTR_PERSISTENT: tái sử dụng kết nối giữa các request -> giảm độ trễ mở kết nối mỗi lần load trang
    // ATTR_EMULATE_PREPARES = false: dùng prepared statement thật của MySQL (nhanh & an toàn hơn)
    $pdo = new PDO(
        "mysql:host=$host;dbname=$db;charset=utf8mb4",
        $user,
        $pass,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_PERSISTENT => true,
            PDO::ATTR_EMULATE_PREPARES => false,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]
    );
} catch (PDOException $e) {
    http_response_code(500);
    die("Lỗi kết nối: " . $e->getMessage());
}
?>
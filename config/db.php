<?php
$host = 'localhost';
$db   = 'inventory_manager';
$user = 'root';
$pass = ''; // Mặc định của XAMPP là trống

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Lỗi kết nối: " . $e->getMessage());
}
?>
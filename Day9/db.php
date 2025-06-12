<?php
// Cấu hình kết nối cơ sở dữ liệu
$host = 'localhost';
$dbname = 'tech_factory';
$username = 'root';
$password = '';

try {
    // Tạo kết nối PDO
    $pdo = new PDO("mysql:host=$host", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Tạo cơ sở dữ liệu nếu chưa tồn tại
    $pdo->exec("CREATE DATABASE IF NOT EXISTS $dbname CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    
    // Kết nối đến cơ sở dữ liệu vừa tạo
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    
    echo "Kết nối cơ sở dữ liệu thành công!<br>";
    
} catch(PDOException $e) {
    die("Lỗi kết nối: " . $e->getMessage());
}
?>
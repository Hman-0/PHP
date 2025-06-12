<?php
echo "=== HỆ THỐNG QUẢN LÝ SẢN XUẤT TECHFACTORY ===<br><br>";

echo "Bước 1: Tạo cơ sở dữ liệu và bảng...<br>";
include 'create_tables.php';

echo "<br>" . str_repeat("=", 50) . "<br><br>";

echo "Bước 2: Thực hiện các thao tác dữ liệu...<br>";
include 'data_operations.php';

echo "<br>" . str_repeat("=", 50) . "<br><br>";

echo "Bước 3: Tạo báo cáo nâng cao...<br>";
include 'reports.php';

echo "<br>" . str_repeat("=", 50) . "<br>";
echo "HỆ THỐNG ĐÃ HOÀN THÀNH!<br>";
?>
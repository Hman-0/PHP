<?php

// Import các file class
require_once 'AffiliatePartner.php';
require_once 'PremiumAffiliatePartner.php';
require_once 'AffiliateManager.php';

// Xử lý form
// Tạo đối tượng quản lý
$manager = new AffiliateManager();

// Tạo cộng tác viên thường
$ctv1 = new AffiliatePartner("Nguyễn Văn A", "a@gmail.com", 5); // 5%
$ctv2 = new AffiliatePartner("Trần Thị B", "b@gmail.com", 7); // 7%

// Tạo cộng tác viên cao cấp
$ctv3 = new PremiumAffiliatePartner("Lê Văn C", "c@gmail.com", 10, 50000); // 10% + 50,000 VNĐ

// Thêm vào hệ thống
$manager->addPartner($ctv1);
$manager->addPartner($ctv2);
$manager->addPartner($ctv3);

// Hiển thị thông tin
$manager->listPartners();

// Tính hoa hồng trên mỗi đơn hàng trị giá 2.000.000 VNĐ
$orderValue = 2000000;
echo "Tính hoa hồng cho đơn hàng trị giá " . number_format($orderValue, 0, ',', '.') . " VNĐ:<br>";
$total = $manager->totalCommission($orderValue);
echo "Tổng hoa hồng hệ thống cần chi trả: " . number_format($total, 0, ',', '.') . " VNĐ<br>";

<?php

require_once 'AffiliatePartner.php';

// Lớp AffiliateManager
class AffiliateManager {
    private array $partners = [];

    // Thêm đối tượng cộng tác viên vào hệ thống
    public function addPartner(AffiliatePartner $affiliate): void {
        $this->partners[] = $affiliate;
    }

    // In thông tin tất cả cộng tác viên
    public function listPartners(): void {
        foreach ($this->partners as $partner) {
            echo "----------------------<br>";
            echo $partner->getSummary();
            
            // Check if partner is premium to display bonus and premium status
            if ($partner instanceof PremiumAffiliatePartner) {
                echo "CTV Cao Cấp<br>";
                echo "Tiền thưởng : " . number_format($partner->getBonus(), 0, ',', '.') . " VNĐ<br>";
            }
        }
        echo "----------------------<br>";
    }
    // Tính tổng hoa hồng nếu mỗi người có 1 đơn hàng trị giá $orderValue
    public function totalCommission(float $orderValue): float {
        $total = 0;
        foreach ($this->partners as $partner) {
            if ($partner->isActive()) {
                $commission = $partner->calculateCommission($orderValue);
                echo "Hoa hồng cho {$partner->getName()}: " . number_format($commission, 0, ',', '.') . " VNĐ<br>";
                $total += $commission;
            }
        }
        echo "========================<br>";
        return $total;
    }
}

?>
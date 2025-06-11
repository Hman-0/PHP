<?php

require_once 'AffiliatePartner.php';

// Lớp PremiumAffiliatePartner
class PremiumAffiliatePartner extends AffiliatePartner {
    private float $bonusPerOrder;

    public function __construct(string $name, string $email, float $commissionRate, float $bonusPerOrder, bool $isActive = true) {
        parent::__construct($name, $email, $commissionRate, $isActive);
        $this->bonusPerOrder = $bonusPerOrder;
    }

    // Ghi đè phương thức tính hoa hồng
    public function calculateCommission(float $orderValue): float {
        return parent::calculateCommission($orderValue) + $this->bonusPerOrder;
    }
    
    // Getter cho bonusPerOrder
    public function getBonus(): float {
        return $this->bonusPerOrder;
    }
    
    // Lấy hoa hồng cơ bản (không bao gồm bonus)
    public function getBaseCommission(float $orderValue): float {
        return parent::calculateCommission($orderValue);
    }
}

?>
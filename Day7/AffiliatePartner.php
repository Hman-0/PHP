<?php

class AffiliatePartner {
    // Hằng số
    const PLATFORM_NAME = "VietLink Affiliate";

    // Thuộc tính
    private string $name;
    private string $email;
    private float $commissionRate;
    private bool $isActive;

    // Constructor
    public function __construct(string $name, string $email, float $commissionRate, bool $isActive = true) {
        $this->name = $name;
        $this->email = $email;
        $this->commissionRate = $commissionRate;
        $this->isActive = $isActive;
    }

    // Destructor
    public function __destruct() {
        echo "[LOG] CTV {$this->name} đã được giải phóng khỏi bộ nhớ.<br>";
    }

    // Tính hoa hồng = % x giá trị đơn hàng
    public function calculateCommission(float $orderValue): float {
        return ($this->commissionRate / 100) * $orderValue;
    }

    // Lấy thông tin tổng quan
    public function getSummary(): string {
        return "Nền tảng: " . self::PLATFORM_NAME . "<br>" .
               "Tên: {$this->name}<br>" .
               "Email: {$this->email}<br>" .
               "Tỷ lệ hoa hồng: {$this->commissionRate}%<br>" .
               "Trạng thái: " . ($this->isActive ? "Đang hoạt động" : "Ngừng hoạt động") . "<br>";
    }

    // Getter cho name
    public function getName(): string {
        return $this->name;
    }

    public function isActive(): bool {
        return $this->isActive;
    }
}
?>
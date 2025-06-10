<?php

/**
 * Lớp cơ bản AffiliatePartner - Đại diện cho cộng tác viên thường
 * Chứa thông tin cơ bản và logic tính hoa hồng cho cộng tác viên
 */
class AffiliatePartner 
{
    // Hằng số nền tảng
    const PLATFORM_NAME = "VietLink Affiliate";
    
    // Thuộc tính protected để cho phép lớp con truy cập
    protected $name;
    protected $email;
    protected $commissionRate; // Tỷ lệ hoa hồng (%)
    protected $isActive;
    
    /**
     * Constructor - Khởi tạo đối tượng cộng tác viên
     * 
     * @param string $name Họ tên cộng tác viên
     * @param string $email Email liên hệ
     * @param float $commissionRate Tỷ lệ hoa hồng (%)
     * @param bool $isActive Trạng thái hoạt động
     */
    public function __construct($name, $email, $commissionRate, $isActive = true) 
    {
        $this->name = $name;
        $this->email = $email;
        $this->commissionRate = $commissionRate;
        $this->isActive = $isActive;
        
        echo "✅ Đã tạo cộng tác viên: {$this->name}\n";
    }
    
    /**
     * Destructor - Thông báo khi đối tượng bị hủy
     */
    public function __destruct() 
    {
        echo "🗑️ Đã giải phóng cộng tác viên: {$this->name} khỏi bộ nhớ\n";
    }
    
    /**
     * Tính hoa hồng dựa trên giá trị đơn hàng
     * 
     * @param float $orderValue Giá trị đơn hàng
     * @return float Số tiền hoa hồng
     */
    public function calculateCommission($orderValue) 
    {
        if (!$this->isActive) {
            return 0;
        }
        
        return ($orderValue * $this->commissionRate) / 100;
    }
    
    /**
     * Lấy thông tin tổng quan của cộng tác viên
     * 
     * @return string Thông tin chi tiết
     */
    public function getSummary() 
    {
        $status = $this->isActive ? "Hoạt động" : "Tạm dừng";
        $type = "Thường";
        
        return sprintf(
            "📋 [%s] %s\n" .
            "   ├─ Email: %s\n" .
            "   ├─ Loại: %s\n" .
            "   ├─ Hoa hồng: %.1f%%\n" .
            "   └─ Trạng thái: %s",
            self::PLATFORM_NAME,
            $this->name,
            $this->email,
            $type,
            $this->commissionRate,
            $status
        );
    }
    
    // Getter methods
    public function getName() 
    {
        return $this->name;
    }
    
    public function getEmail() 
    {
        return $this->email;
    }
    
    public function isActive() 
    {
        return $this->isActive;
    }
    
    public function getCommissionRate() 
    {
        return $this->commissionRate;
    }
}

/**
 * Lớp PremiumAffiliatePartner - Kế thừa từ AffiliatePartner
 * Đại diện cho cộng tác viên cao cấp với bonus cố định
 */
class PremiumAffiliatePartner extends AffiliatePartner 
{
    // Thuộc tính bổ sung cho cộng tác viên cao cấp
    private $bonusPerOrder;
    
    /**
     * Constructor cho cộng tác viên cao cấp
     * 
     * @param string $name Họ tên
     * @param string $email Email
     * @param float $commissionRate Tỷ lệ hoa hồng (%)
     * @param float $bonusPerOrder Bonus cố định mỗi đơn hàng
     * @param bool $isActive Trạng thái hoạt động
     */
    public function __construct($name, $email, $commissionRate, $bonusPerOrder, $isActive = true) 
    {
        parent::__construct($name, $email, $commissionRate, $isActive);
        $this->bonusPerOrder = $bonusPerOrder;
        
        echo "⭐ Cộng tác viên cao cấp với bonus: " . number_format($bonusPerOrder) . " VNĐ/đơn\n";
    }
    
    /**
     * Override phương thức tính hoa hồng
     * Bao gồm cả hoa hồng phần trăm và bonus cố định
     * 
     * @param float $orderValue Giá trị đơn hàng
     * @return float Tổng hoa hồng (phần trăm + bonus)
     */
    public function calculateCommission($orderValue) 
    {
        if (!$this->isActive) {
            return 0;
        }
        
        $percentageCommission = parent::calculateCommission($orderValue);
        return $percentageCommission + $this->bonusPerOrder;
    }
    
    /**
     * Override phương thức getSummary để hiển thị thông tin bonus
     * 
     * @return string Thông tin chi tiết bao gồm bonus
     */
    public function getSummary() 
    {
        $status = $this->isActive ? "Hoạt động" : "Tạm dừng";
        
        return sprintf(
            "📋 [%s] %s ⭐\n" .
            "   ├─ Email: %s\n" .
            "   ├─ Loại: Premium\n" .
            "   ├─ Hoa hồng: %.1f%%\n" .
            "   ├─ Bonus: %s VNĐ/đơn\n" .
            "   └─ Trạng thái: %s",
            self::PLATFORM_NAME,
            $this->name,
            $this->email,
            $this->commissionRate,
            number_format($this->bonusPerOrder),
            $status
        );
    }
    
    public function getBonusPerOrder() 
    {
        return $this->bonusPerOrder;
    }
}

/**
 * Lớp AffiliateManager - Quản lý danh sách cộng tác viên
 * Chịu trách nhiệm thêm, liệt kê và tính toán hoa hồng tổng thể
 */
class AffiliateManager 
{
    // Danh sách cộng tác viên
    private $partners = [];
    
    /**
     * Thêm cộng tác viên vào hệ thống
     * 
     * @param AffiliatePartner $affiliate Đối tượng cộng tác viên
     */
    public function addPartner(AffiliatePartner $affiliate) 
    {
        $this->partners[] = $affiliate;
        echo "➕ Đã thêm {$affiliate->getName()} vào hệ thống quản lý\n\n";
    }
    
    /**
     * Liệt kê tất cả cộng tác viên trong hệ thống
     */
    public function listPartners() 
    {
        echo "📊 DANH SÁCH CỘNG TÁC VIÊN\n";
        echo str_repeat("=", 50) . "\n\n";
        
        if (empty($this->partners)) {
            echo "❌ Chưa có cộng tác viên nào trong hệ thống.\n";
            return;
        }
        
        foreach ($this->partners as $index => $partner) {
            echo ($index + 1) . ". " . $partner->getSummary() . "\n\n";
        }
    }
    
    /**
     * Tính tổng hoa hồng nếu mỗi CTV thực hiện một đơn hàng
     * 
     * @param float $orderValue Giá trị đơn hàng
     * @return float Tổng hoa hồng cần chi trả
     */
    public function totalCommission($orderValue) 
    {
        $totalCommission = 0;
        $activePartners = 0;
        
        echo "💰 TÍNH TOÁN HOA HỒNG CHO ĐơN HÀNG: " . number_format($orderValue) . " VNĐ\n";
        echo str_repeat("=", 60) . "\n\n";
        
        foreach ($this->partners as $partner) {
            if ($partner->isActive()) {
                $commission = $partner->calculateCommission($orderValue);
                $totalCommission += $commission;
                $activePartners++;
                
                echo sprintf(
                    "👤 %-20s │ %s VNĐ\n",
                    $partner->getName(),
                    number_format($commission)
                );
            } else {
                echo sprintf(
                    "👤 %-20s │ Tạm dừng hoạt động\n",
                    $partner->getName()
                );
            }
        }
        
        echo str_repeat("-", 60) . "\n";
        echo sprintf(
            "📈 TỔNG KẾT:\n" .
            "   ├─ Số CTV hoạt động: %d/%d\n" .
            "   ├─ Giá trị đơn hàng: %s VNĐ\n" .
            "   └─ Tổng hoa hồng: %s VNĐ\n\n",
            $activePartners,
            count($this->partners),
            number_format($orderValue),
            number_format($totalCommission)
        );
        
        return $totalCommission;
    }
    
    /**
     * Lấy số lượng cộng tác viên trong hệ thống
     * 
     * @return int Tổng số cộng tác viên
     */
    public function getPartnerCount() 
    {
        return count($this->partners);
    }
    
    /**
     * Lấy danh sách cộng tác viên đang hoạt động
     * 
     * @return array Mảng các cộng tác viên đang hoạt động
     */
    public function getActivePartners() 
    {
        return array_filter($this->partners, function($partner) {
            return $partner->isActive();
        });
    }
}

// ============================================================================
// CHƯƠNG TRÌNH CHÍNH - DEMO HỆ THỐNG
// ============================================================================

echo "🚀 KHỞI TẠO HỆ THỐNG QUẢN LÝ CỘNG TÁC VIÊN\n";
echo str_repeat("=", 60) . "\n\n";

// Tạo đối tượng quản lý
$manager = new AffiliateManager();

echo "📝 ĐANG TẠO CÁC CỘNG TÁC VIÊN...\n";
echo str_repeat("-", 40) . "\n";

// Tạo 2 cộng tác viên thường với tỷ lệ hoa hồng khác nhau
$affiliate1 = new AffiliatePartner(
    "Nguyễn Văn Anh", 
    "nguyenvananh@email.com", 
    5.0  // 5% hoa hồng
);

$affiliate2 = new AffiliatePartner(
    "Trần Thị Bình", 
    "tranthibinh@email.com", 
    7.5  // 7.5% hoa hồng
);

// Tạo 1 cộng tác viên cao cấp
$premiumAffiliate = new PremiumAffiliatePartner(
    "Lê Hoàng Cường", 
    "lehoangcuong@email.com", 
    10.0,   // 10% hoa hồng
    50000   // 50,000 VNĐ bonus mỗi đơn
);

echo "\n";

// Thêm các cộng tác viên vào hệ thống quản lý
$manager->addPartner($affiliate1);
$manager->addPartner($affiliate2);
$manager->addPartner($premiumAffiliate);

// Hiển thị danh sách cộng tác viên
$manager->listPartners();

// Giả sử mỗi CTV thực hiện thành công một đơn hàng 2,000,000 VNĐ
$orderValue = 2000000; // 2 triệu VNĐ
$totalCommission = $manager->totalCommission($orderValue);

echo "🎯 KẾT LUẬN:\n";
echo "Với đơn hàng " . number_format($orderValue) . " VNĐ, hệ thống cần chi trả tổng cộng " . 
     number_format($totalCommission) . " VNĐ tiền hoa hồng.\n\n";

echo "🔚 CHƯƠNG TRÌNH KẾT THÚC - ĐANG DỌN DẸP BỘ NHỚ...\n";
echo str_repeat("=", 50) . "\n";

// Các đối tượng sẽ tự động được hủy khi script kết thúc
// Phương thức __destruct() sẽ được gọi tự động

?>
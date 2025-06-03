<?php

final class SystemLogger {
    private static array $logs = [];

    public static function write(string $message, string $type = 'INFO'): void {
        self::$logs[] = sprintf("[%s] [%s] %s", date('Y-m-d H:i:s'), $type, $message);
    }

    public static function getLogs(): array {
        return self::$logs;
    }
}

// Theo dõi số lượng hoa hồng 
final class CommissionCounter {
    private static int $count = 0;

    public static function increment(): void {
        self::$count++;
    }

    public static function getCount(): int {
        return self::$count;
    }

    public static function reset(): void {
        self::$count = 0;
    }
}


final class DataStore {
    public const USERS = [
        1 => ['name' => 'Alice', 'referrer_id' => null],
        2 => ['name' => 'Bob', 'referrer_id' => 1],
        3 => ['name' => 'Charlie', 'referrer_id' => 2],
        4 => ['name' => 'David', 'referrer_id' => 3],
        5 => ['name' => 'Eva', 'referrer_id' => 1],
    ];

    public const ORDERS = [
        ['order_id' => 101, 'user_id' => 4, 'amount' => 200.0],
        ['order_id' => 102, 'user_id' => 3, 'amount' => 150.0],
        ['order_id' => 103, 'user_id' => 5, 'amount' => 300.0],
    ];

    public const COMMISSION_RATES = [
        1 => 0.10,
        2 => 0.05,
        3 => 0.02,
    ];
}


final class CommissionHelper {
    public static function sanitizeUserName(string $name): string {
        return ucfirst(trim(strtolower($name)));
    }

    public static function formatCurrency(float $amount): string {
        return number_format($amount, 2) . ' VND';
    }

    public static function findUserById(int $userId, array $users): ?array {
        return $users[$userId] ?? null;
    }

    public static function validateOrder(array $order): bool {
        return isset($order['order_id'], $order['user_id'], $order['amount'])
            && is_numeric($order['amount'])
            && $order['amount'] > 0;
    }

    public static function calculateCommission(float $orderAmount, int $level, array $rates): float {
        return $orderAmount * ($rates[$level] ?? 0.0);
    }

    public static function getCurrentTimestamp(): string {
        return date('Y-m-d H:i:s');
    }
}


final class CommissionCalculator {
    private array $users;
    private array $commissionRates;
    private const MAX_LEVEL = 3;

    public function __construct(array $users, array $commissionRates) {
        $this->users = $users;
        $this->commissionRates = $commissionRates;
    }

    private function findReferralChain(int $userId, int $currentLevel = 1): array {
        if ($currentLevel > self::MAX_LEVEL) {
            return [];
        }

        $user = CommissionHelper::findUserById($userId, $this->users);
        if (!$user || !$user['referrer_id']) {
            return [];
        }

        SystemLogger::write("Tìm thấy người giới thiệu cấp $currentLevel: User ID {$user['referrer_id']} cho User ID $userId");

        $chain = [[
            'user_id' => $user['referrer_id'],
            'level' => $currentLevel,
            'name' => $this->users[$user['referrer_id']]['name'] ?? 'Unknown'
        ]];

        return array_merge($chain, $this->findReferralChain($user['referrer_id'], $currentLevel + 1));
    }

    public function calculateSingleOrder(array $order): array {
        CommissionCounter::increment();

        if (!CommissionHelper::validateOrder($order)) {
            SystemLogger::write("Đơn hàng không hợp lệ: " . json_encode($order), 'ERROR');
            return [];
        }

        $buyerUser = CommissionHelper::findUserById($order['user_id'], $this->users);
        if (!$buyerUser) {
            SystemLogger::write("Không tìm thấy người mua với ID: {$order['user_id']}", 'ERROR');
            return [];
        }

        SystemLogger::write("Bắt đầu tính hoa hồng cho đơn hàng #{$order['order_id']} - Người mua: {$buyerUser['name']} - Số tiền: " . CommissionHelper::formatCurrency($order['amount']));

        $referralChain = $this->findReferralChain($order['user_id']);
        $commissions = [];

        foreach ($referralChain as $referrer) {
            $commissionAmount = CommissionHelper::calculateCommission(
                $order['amount'],
                $referrer['level'],
                $this->commissionRates
            );

            if ($commissionAmount > 0) {
                $commissions[] = [
                    'order_id' => $order['order_id'],
                    'buyer_name' => $buyerUser['name'],
                    'buyer_id' => $order['user_id'],
                    'referrer_id' => $referrer['user_id'],
                    'referrer_name' => $referrer['name'],
                    'level' => $referrer['level'],
                    'order_amount' => $order['amount'],
                    'commission_rate' => $this->commissionRates[$referrer['level']] ?? 0.0,
                    'commission_amount' => $commissionAmount
                ];

                SystemLogger::write("Hoa hồng cấp {$referrer['level']}: {$referrer['name']} nhận " . CommissionHelper::formatCurrency($commissionAmount));
            }
        }

        return $commissions;
    }

    public function calculateTotals(array $orders): array {
        SystemLogger::write("=== BẮT ĐẦU TÍNH TOÁN HỆ THỐNG HOA HỒNG ===");
        CommissionCounter::reset();

        $allCommissions = array_merge(...array_map(fn($order) => $this->calculateSingleOrder($order), $orders));

        $userTotals = [];
        foreach ($allCommissions as $commission) {
            $userId = $commission['referrer_id'];
            if (!isset($userTotals[$userId])) {
                $userTotals[$userId] = [
                    'name' => $commission['referrer_name'],
                    'total_commission' => 0,
                    'commission_details' => []
                ];
            }

            $userTotals[$userId]['total_commission'] += $commission['commission_amount'];
            $userTotals[$userId]['commission_details'][] = $commission;
        }

        uasort($userTotals, fn($a, $b) => $b['total_commission'] <=> $a['total_commission']);

        return [
            'user_totals' => $userTotals,
            'all_commissions' => $allCommissions,
            'summary' => [
                'total_orders' => count($orders),
                'total_commissions' => array_sum(array_column($allCommissions, 'commission_amount')),
                'calculation_count' => CommissionCounter::getCount(),
                'processed_at' => CommissionHelper::getCurrentTimestamp()
            ]
        ];
    }
}


$calculator = new CommissionCalculator(DataStore::USERS, DataStore::COMMISSION_RATES);
$results = $calculator->calculateTotals(DataStore::ORDERS);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hệ thống tính hoa hồng Affiliate</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 font-sans">
    <div class="container mx-auto p-6 max-w-7xl">
        <header class="bg-blue-600 text-white p-6 rounded-lg shadow-lg mb-8">
            <h1 class="text-3xl font-bold">Hệ thống tính hoa hồng Affiliate</h1>
            <p class="mt-2">Báo cáo chi tiết về hoa hồng và thông tin giao dịch</p>
        </header>

  
        <section class="bg-white p-6 rounded-lg shadow-md mb-8">
            <h2 class="text-2xl font-semibold mb-4">📊 Tổng quan</h2>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="bg-gray-50 p-4 rounded-lg">
                    <p class="text-gray-600">Tổng đơn hàng</p>
                    <p class="text-xl font-bold"><?php echo $results['summary']['total_orders']; ?></p>
                </div>
                <div class="bg-gray-50 p-4 rounded-lg">
                    <p class="text-gray-600">Tổng hoa hồng</p>
                    <p class="text-xl font-bold"><?php echo CommissionHelper::formatCurrency($results['summary']['total_commissions']); ?></p>
                </div>
                <div class="bg-gray-50 p-4 rounded-lg">
                    <p class="text-gray-600">Số lần tính toán</p>
                    <p class="text-xl font-bold"><?php echo $results['summary']['calculation_count']; ?></p>
                </div>
                <div class="bg-gray-50 p-4 rounded-lg">
                    <p class="text-gray-600">Thời gian xử lý</p>
                    <p class="text-xl font-bold"><?php echo $results['summary']['processed_at']; ?></p>
                </div>
            </div>
        </section>

      
        <section class="bg-white p-6 rounded-lg shadow-md mb-8">
            <h2 class="text-2xl font-semibold mb-4">👥 Tổng hợp theo người dùng</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <?php foreach ($results['user_totals'] as $userId => $data): ?>
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <p class="font-semibold"><?php echo htmlspecialchars($data['name']); ?> (ID: <?php echo $userId; ?>)</p>
                        <p>Tổng hoa hồng: <span class="font-bold"><?php echo CommissionHelper::formatCurrency($data['total_commission']); ?></span></p>
                        <p>Số giao dịch: <?php echo count($data['commission_details']); ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>

        <section class="bg-white p-6 rounded-lg shadow-md mb-8">
            <h2 class="text-2xl font-semibold mb-4">📋 Chi tiết hoa hồng</h2>
            <div class="overflow-x-auto">
                <table class="w-full table-auto">
                    <thead>
                        <tr class="bg-gray-200">
                            <th class="p-3 text-left">Đơn hàng</th>
                            <th class="p-3 text-left">Người mua</th>
                            <th class="p-3 text-left">Số tiền</th>
                            <th class="p-3 text-left">Người nhận</th>
                            <th class="p-3 text-left">Hoa hồng</th>
                            <th class="p-3 text-left">Cấp</th>
                            <th class="p-3 text-left">Tỷ lệ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($results['all_commissions'] as $commission): ?>
                            <tr class="border-b">
                                <td class="p-3">#<?php echo $commission['order_id']; ?></td>
                                <td class="p-3"><?php echo htmlspecialchars($commission['buyer_name']); ?></td>
                                <td class="p-3"><?php echo CommissionHelper::formatCurrency($commission['order_amount']); ?></td>
                                <td class="p-3"><?php echo htmlspecialchars($commission['referrer_name']); ?></td>
                                <td class="p-3"><?php echo CommissionHelper::formatCurrency($commission['commission_amount']); ?></td>
                                <td class="p-3"><?php echo $commission['level']; ?></td>
                                <td class="p-3"><?php echo ($commission['commission_rate'] * 100); ?>%</td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </section>

        <section class="bg-white p-6 rounded-lg shadow-md">
            <h2 class="text-2xl font-semibold mb-4">📜 Log hệ thống</h2>
            <div class="bg-gray-50 p-4 rounded-lg max-h-96 overflow-y-auto">
                <?php foreach (SystemLogger::getLogs() as $log): ?>
                    <p class="text-sm text-gray-600"><?php echo htmlspecialchars($log); ?></p>
                <?php endforeach; ?>
            </div>
        </section>
    </div>
</body>
</html>
<?php

namespace XYZBank\Accounts;

/**
 * Abstract base class for all bank accounts
 */
abstract class BankAccount {
    protected string $accountNumber;
    protected string $ownerName;
    protected float $balance;

    public function __construct(string $accountNumber, string $ownerName, float $balance) {
        $this->accountNumber = $accountNumber;
        $this->ownerName = $ownerName;
        $this->balance = $balance;
        Bank::incrementTotalAccounts();
    }

    public function getBalance(): float {
        return $this->balance;
    }

    public function getOwnerName(): string {
        return $this->ownerName;
    }

    public function getAccountNumber(): string {
        return $this->accountNumber;
    }

    abstract public function deposit(float $amount): void;
    abstract public function withdraw(float $amount): void;
    abstract public function getAccountType(): string;
}

/**
 * Interface for accounts that bear interest
 */
interface InterestBearing {
    public function calculateAnnualInterest(): float;
}

/**
 * Trait for logging transactions
 */
trait TransactionLogger {
    public function logTransaction(string $type, float $amount, float $newBalance): void {
        $timestamp = date('Y-m-d H:i:s');
        $formattedAmount = number_format($amount, 0, ',', '.') . ' VNĐ';
        $formattedBalance = number_format($newBalance, 0, ',', '.') . ' VNĐ';
        echo "[$timestamp] Giao dịch: $type $formattedAmount | Số dư mới: $formattedBalance\n";
    }
}

/**
 * Savings Account implementation
 */
class SavingsAccount extends BankAccount implements InterestBearing {
    use TransactionLogger;

    private const INTEREST_RATE = 0.05;
    private const MINIMUM_BALANCE = 1000000;

    public function deposit(float $amount): void {
        if ($amount <= 0) {
            throw new \InvalidArgumentException("Số tiền gửi phải lớn hơn 0");
        }
        $this->balance += $amount;
        $this->logTransaction("Gửi tiền", $amount, $this->balance);
    }

    public function withdraw(float $amount): void {
        if ($amount <= 0) {
            throw new \InvalidArgumentException("Số tiền rút phải lớn hơn 0");
        }
        if ($this->balance - $amount < self::MINIMUM_BALANCE) {
            throw new \RuntimeException("Số dư sau rút không được dưới " . self::MINIMUM_BALANCE . " VNĐ");
        }
        $this->balance -= $amount;
        $this->logTransaction("Rút tiền", $amount, $this->balance);
    }

    public function getAccountType(): string {
        return "Tiết kiệm";
    }

    public function calculateAnnualInterest(): float {
        return $this->balance * self::INTEREST_RATE;
    }
}

/**
 * Checking Account implementation
 */
class CheckingAccount extends BankAccount {
    use TransactionLogger;

    public function deposit(float $amount): void {
        if ($amount <= 0) {
            throw new \InvalidArgumentException("Số tiền gửi phải lớn hơn 0");
        }
        $this->balance += $amount;
        $this->logTransaction("Gửi tiền", $amount, $this->balance);
    }

    public function withdraw(float $amount): void {
        if ($amount <= 0) {
            throw new \InvalidArgumentException("Số tiền rút phải lớn hơn 0");
        }
        if ($amount > $this->balance) {
            throw new \RuntimeException("Số dư không đủ");
        }
        $this->balance -= $amount;
        $this->logTransaction("Rút tiền", $amount, $this->balance);
    }

    public function getAccountType(): string {
        return "Thanh toán";
    }
}

/**
 * Bank utility class
 */
class Bank {
    private static int $totalAccounts = 0;

    public static function getBankName(): string {
        return "Ngân hàng XYZ";
    }

    public static function incrementTotalAccounts(): void {
        self::$totalAccounts++;
    }

    public static function getTotalAccounts(): int {
        return self::$totalAccounts;
    }
}

/**
 * Collection class for managing accounts
 */
class AccountCollection implements \IteratorAggregate {
    private array $accounts = [];

    public function addAccount(BankAccount $account): void {
        $this->accounts[] = $account;
    }

    public function getIterator(): \Traversable {
        return new \ArrayIterator($this->accounts);
    }

    public function getHighBalanceAccounts(float $threshold = 10000000): array {
        return array_filter($this->accounts, fn($account) => $account->getBalance() >= $threshold);
    }
}

/**
 * Test implementation
 */
function runBankingTest(): void {
    $collection = new AccountCollection();

    // Create savings account
    $savings = new SavingsAccount("10201122", "Nguyễn Thị A", 20000000);
    $collection->addAccount($savings);

    // Create checking accounts
    $checking1 = new CheckingAccount("20301123", "Lê Văn B", 8000000);
    $checking2 = new CheckingAccount("20401124", "Trần Minh C", 12000000);
    $collection->addAccount($checking1);
    $collection->addAccount($checking2);

    // Perform transactions
    try {
        $checking1->deposit(5000000);
        $checking2->withdraw(2000000);
    } catch (\Exception $e) {
        echo "Lỗi giao dịch: " . $e->getMessage() . "\n";
    }

    // Display all accounts
    foreach ($collection as $account) {
        echo sprintf(
            "Tài khoản: %s | %s | Loại: %s | Số dư: %s VNĐ\n",
            $account->getAccountNumber(),
            $account->getOwnerName(),
            $account->getAccountType(),
            number_format($account->getBalance(), 0, ',', '.')
        );
    }

    // Display annual interest for savings account
    $interest = $savings->calculateAnnualInterest();
    echo sprintf(
        "Lãi suất hàng năm cho %s: %s VNĐ\n",
        $savings->getOwnerName(),
        number_format($interest, 0, ',', '.')
    );

    // Display bank statistics
    echo "Tổng số tài khoản đã tạo: " . Bank::getTotalAccounts() . "\n";
    echo "Tên ngân hàng: " . Bank::getBankName() . "\n";

    // Display high balance accounts
    echo "\nTài khoản có số dư ≥ 10.000.000 VNĐ:\n";
    foreach ($collection->getHighBalanceAccounts() as $account) {
        echo sprintf(
            "Tài khoản: %s | %s | Số dư: %s VNĐ\n",
            $account->getAccountNumber(),
            $account->getOwnerName(),
            number_format($account->getBalance(), 0, ',', '.')
        );
    }
}

// Run the test
runBankingTest();

?>
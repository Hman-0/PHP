<?php

require_once 'InterestBearing.php';
require_once 'TransactionLogger.php';
require_once 'Bank.php';
require_once 'BankAccount.php';
require_once 'SavingsAccount.php';
require_once 'CheckingAccount.php';
require_once 'AccountCollection.php';

use XYZBank\Accounts\SavingsAccount;
use XYZBank\Accounts\CheckingAccount;
use XYZBank\Accounts\AccountCollection;
use XYZBank\Accounts\Bank;

echo "=== HỆ THỐNG QUẢN LÝ TÀI KHOẢN NGÂN HÀNG XYZ ===<br>";

// Tạo collection để quản lý tài khoản
$accountCollection = new AccountCollection();

// 1. Tạo 1 tài khoản tiết kiệm
echo "1. Tạo tài khoản tiết kiệm cho Nguyễn Thị A<br>";
$savingsAccount = new SavingsAccount("10201122", "Nguyễn Thị A", 20000000);
$accountCollection->addAccount($savingsAccount);
echo "<br>";

// 2. Tạo 2 tài khoản thanh toán
echo "2. Tạo tài khoản thanh toán cho Lê Văn B<br>";
$checkingAccount1 = new CheckingAccount("20301123", "Lê Văn B", 8000000);
$accountCollection->addAccount($checkingAccount1);
echo "<br>";

echo "3. Tạo tài khoản thanh toán cho Trần Minh C<br>";
$checkingAccount2 = new CheckingAccount("20401124", "Trần Minh C", 12000000);
$accountCollection->addAccount($checkingAccount2);
echo "<br>";

// 3. Gửi thêm 5.000.000 vào tài khoản của Lê Văn B
echo "4. Gửi thêm 5.000.000 VNĐ vào tài khoản của Lê Văn B<br>";
$checkingAccount1->deposit(5000000);
echo "<br>";

// 4. Rút 2.000.000 từ tài khoản của Trần Minh C
echo "5. Rút 2.000.000 VNĐ từ tài khoản của Trần Minh C<br>";
$checkingAccount2->withdraw(2000000);
echo "<br>";

// 5. Tính và hiển thị lãi suất hàng năm của tài khoản tiết kiệm
echo "6. Tính lãi suất hàng năm<br>";
$annualInterest = $savingsAccount->calculateAnnualInterest();
echo "Lãi suất hàng năm cho {$savingsAccount->getOwnerName()}: " . number_format($annualInterest, 0, ',', '.') . " VNĐ<br>";

// 6. Duyệt tất cả tài khoản và in thông tin
echo "7. Danh sách tất cả tài khoản:<br>";
foreach ($accountCollection as $account) {
    $formattedBalance = number_format($account->getBalance(), 0, ',', '.');
    echo "Tài khoản: {$account->getAccountNumber()} | {$account->getOwnerName()} | Loại: {$account->getAccountType()} | Số dư: {$formattedBalance} VNĐ<br>";
}
echo "<br>";

// 7. In tổng số tài khoản đã khởi tạo
echo "8. Thống kê hệ thống:<br>";
echo "Tổng số tài khoản đã tạo: " . Bank::getTotalAccounts() . "<br>";
echo "Tên ngân hàng: " . Bank::getBankName() . "<br>";

// 8. Lọc các tài khoản có số dư >= 10.000.000 VNĐ
echo "9. Tài khoản có số dư >= 10.000.000 VNĐ:<br>";
$highBalanceAccounts = $accountCollection->getHighBalanceAccounts();
foreach ($highBalanceAccounts as $account) {
    $formattedBalance = number_format($account->getBalance(), 0, ',', '.');
    echo "Tài khoản: {$account->getAccountNumber()} | {$account->getOwnerName()} | Số dư: {$formattedBalance} VNĐ<br>";
}

echo "<br>=== KẾT THÚC DEMO ===<br>";
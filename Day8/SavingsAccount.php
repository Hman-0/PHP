<?php

namespace XYZBank\Accounts;


class SavingsAccount extends BankAccount implements InterestBearing
{
    private const INTEREST_RATE = 0.05; // 5% lãi suất hàng năm
    private const MINIMUM_BALANCE = 1000000; // Số dư tối thiểu 1.000.000 VNĐ
    
   
    public function deposit(float $amount): void
    {
        if ($amount <= 0) {
            throw new \InvalidArgumentException("Số tiền gửi phải lớn hơn 0");
        }
        
        $this->balance += $amount;
        $this->logTransaction("Gửi tiền", $amount, $this->balance);
    }
    
 
    public function withdraw(float $amount): void
    {
        if ($amount <= 0) {
            throw new \InvalidArgumentException("Số tiền rút phải lớn hơn 0");
        }
        
        if ($this->balance - $amount < self::MINIMUM_BALANCE) {
            throw new \InvalidArgumentException("Không thể rút tiền. Số dư sau giao dịch phải >= 1.000.000 VNĐ");
        }
        
        $this->balance -= $amount;
        $this->logTransaction("Rút tiền", $amount, $this->balance);
    }
    
 
    public function getAccountType(): string
    {
        return "Tiết kiệm";
    }
    
 
    public function calculateAnnualInterest(): float
    {
        return $this->balance * self::INTEREST_RATE;
    }
}
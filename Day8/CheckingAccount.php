<?php

namespace XYZBank\Accounts;

use InvalidArgumentException;

class CheckingAccount extends BankAccount
{
    
    public function deposit(float $amount): void
    {
        if ($amount <= 0) {
            throw new InvalidArgumentException("Số tiền gửi phải lớn hơn 0");
        }
        
        $this->balance += $amount;
        $this->logTransaction("Gửi tiền", $amount, $this->balance);
    }
    
 
    public function withdraw(float $amount): void
    {
        if ($amount <= 0) {
            throw new InvalidArgumentException("Số tiền rút phải lớn hơn 0");
        }
        
        if ($amount > $this->balance) {
            throw new InvalidArgumentException("Số dư không đủ để thực hiện giao dịch");
        }
        
        $this->balance -= $amount;
        $this->logTransaction("Rút tiền", $amount, $this->balance);
    }
    
   
    public function getAccountType(): string
    {
        return "Thanh toán";
    }
}
<?php

namespace XYZBank\Accounts;


trait TransactionLogger
{
   
    protected function logTransaction(string $type, float $amount, float $newBalance): void
    {
        $timestamp = date('Y-m-d H:i:s');
        $formattedAmount = number_format($amount, 0, ',', '.');
        $formattedBalance = number_format($newBalance, 0, ',', '.');
        
        echo "[{$timestamp}] Giao dịch: {$type} {$formattedAmount} VNĐ | Số dư mới: {$formattedBalance} VNĐ<br>";
    }
}
<?php

namespace XYZBank\Accounts;

use IteratorAggregate;
use ArrayIterator;
use Traversable;


class AccountCollection implements IteratorAggregate
{
    private array $accounts = [];
    
 
    public function addAccount(BankAccount $account): void
    {
        $this->accounts[] = $account;
    }
    
  
    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->accounts);
    }
    
   
    public function getHighBalanceAccounts(): array
    {
        return array_filter($this->accounts, function(BankAccount $account) {
            return $account->getBalance() >= 10000000;
        });
    }
    

    public function getAllAccounts(): array
    {
        return $this->accounts;
    }
}
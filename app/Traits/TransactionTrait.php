<?php


namespace App\Traits;

use App\Models\Transaction;

trait TransactionTrait {

    public function MakeTransaction($data)
    {
        $transaction = Transaction::create($data);
        
    }
}

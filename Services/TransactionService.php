<?php
namespace Services;

class TransactionService {
    /**
    * @soap
    * @param float $amount
    * @param integer $idUser
    * @return object  
    */
    public function createTransaction($amount, $idUser){


        return [];
    }

    /**
    * @soap
    * @param string $transaction_uuid
    * @return object  
    */
    public function getTransaction($transaction_uuid){


        return [];
    }
}
<?php
namespace Services;

class WalletService {
    /**
    * @soap
    * @param integer $userId
    * @return object  
    */
    public function getWallet($userId){


        return [];
    }

    /**
    * @soap
    * @param string $uuid
    * @return object  
    */
    public function getBalance($uuid){


        return [];
    }

    /**
    * @soap
    * @param string $uuid
    * @param float $amount
    * @return object  
    */
    public function chargeWallet($uuid,$amount){


        return [];
    }
}
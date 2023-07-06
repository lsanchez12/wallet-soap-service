<?php
namespace Services;
include 'database.php';
use Database;

class WalletService {
    //USER

    /**
    * @soap
    * @param string $email
    * @param string $password
    * @return object  
    */
    public function login($email,$password){
        $database = new Database\Database();
        $database->connect();

        $result = $database->query("SELECT id,document,first_name,last_name,phone_number,email,api_token,password FROM users  WHERE email = '{$email}' LIMIT 1");
        $user = [];
        while ($row = $result->fetch_assoc()) {
            $user = $row;
            break;
        }
        if(password_verify($password, $user["password"])){
            unset($user["password"]);
            return [
                "success" => true,
                "data" => $user
            ];
        }
        return [
            "success" => false,
            "message" => "Email or password invalid"
        ];
    }


    /**
    * @soap
    * @param string $token
    * @return boolean  
    */
    public function validateToken($token){
        $database = new Database\Database();
        $database->connect();

        $result = $database->query("SELECT token FROM users WHERE api_token = '{$token}' LIMIT 1");
        while ($row = $result->fetch_assoc()) {
            return true;
        }
        return false;
    }

    /**
     * @soap
    * @param integer $id
    * @return object  
    */
    public function getUser($id){
        $database = new Database\Database();
        $database->connect();

        $result = $database->query("SELECT id,document,first_name,last_name,phone_number,email FROM users  WHERE id = {$id} LIMIT 1");
        while ($row = $result->fetch_assoc()) {
            return [
                "success" => true,
                "data" => $row
            ];
        }
        return [
            "success" => false,
            "message" => "User not found"
        ];
    }

    /**
    * @soap
    * @param Models\User $user
    * @return object  
    */
    public function createUser($user){  
        $database = new Database\Database();
        $database->connect();
        $user["api_token"] = md5(uniqid().rand(1000000, 9999999));
        $user["password"] = password_hash($user["password"],PASSWORD_DEFAULT);
        if($result = $database->insert("users", $user)){
            return [
                "success" => true,
                "data" => $result
            ];
        }
        return [
            "success" => false,
            "message" => "Error in create new user"
        ];
    }

    //WALLET

     /**
    * @soap
    * @param integer $userId
    * @return object  
    */
    public function getWallet($userId){
        $database = new Database\Database();
        $database->connect();

        $result = $database->query("SELECT wallet_uuid FROM wallets WHERE id_user = {$id} LIMIT 1");
        while ($row = $result->fetch_assoc()) {
            return [
                "success" => true,
                "data" => $row
            ];
        }
        return [
            "success" => false,
            "message" => "Wallet not found"
        ];
    }

    /**
    * @soap
    * @param string $uuid
    * @return object  
    */
    public function getBalance($uuid){
        $database = new Database\Database();
        $database->connect();

        $result = $database->query("SELECT balance FROM wallets WHERE wallet_uuid = '{$uuid}' LIMIT 1");
        while ($row = $result->fetch_assoc()) {
            return [
                "success" => true,
                "data" => $row
            ];
        }
        return [
            "success" => false,
            "message" => "Wallet not found"
        ];
    }

    /**
    * @soap
    * @param string $uuid
    * @param float $amount
    * @return object  
    */
    public function chargeWallet($uuid,$amount){
        $database = new Database\Database();
        $database->connect();

        if ($database->query("UPDATE wallets SET balance=balance+{$amount} WHERE wallet_uuid = '{$uuid}'") === TRUE) {
            return [
                "success" => true,
                "message" => "Charge success"
            ];
        } 
        return [
            "success" => false,
            "message" => "Charge error"
        ];
    }

    //TRANSACTION
    /**
    * @soap
    * @param float $amount
    * @param integer $idUser
    * @return object  
    */
    public function createTransaction($amount, $idUser){
        $database = new Database\Database();
        $database->connect();
        $transaction_uuid = uniqid('transaction_');
        if($transaction_id = $database->insert("transactions", [
            "id_user" => $idUser,
            "transaction_uuid" => $transaction_uuid,
            "amount" => $amount,
            "token" => mt_rand(100000,999999),
        ])){
            return [
                "success" => true,
                "data" => $transaction_uuid 
            ];
        }

        return [
            "success" => false,
            "message" => "Error in create transaction",
        ];
    }

    /**
    * @soap
    * @param string $transaction_uuid
    * @return object  
    */
    public function getTransaction($transaction_uuid){
        $database = new Database\Database();
        $database->connect();

        $result = $database->query("SELECT * FROM transactions LEFT JOIN payments ON payments.id_transaction = transactions.id  WHERE transactions.transaction_uuid = '{$transaction_uuid}' LIMIT 1");
        while ($row = $result->fetch_assoc()) {
            return [
                "success" => true,
                "data" => $row
            ];
        }
        return [
            "success" => false,
            "message" => "Transaction not found"
        ];
    }
    //PAYMENT
    /**
    * @soap
    * @param string $transaction_uuid
    * @param string $token
    * @return object  
    */
    public function chargePayment($transaction_uuid, $token){
        $database = new Database\Database();
        $database->connect();
        $result = $database->query("SELECT id,amount,user_id FROM transactions WHERE transaction_uuid = '{$transaction_uuid}' and token='{$token}' LIMIT 1");
        $transaction = null;
        while ($row = $result->fetch_assoc()) {
            $transaction = $row;
            break;
        }
        if($transaction){
            $wallet = null;
            $result = $database->query("SELECT id,balance FROM wallets WHERE id_user = '{$transaction["user_id"]}' LIMIT 1");
            while ($row = $result->fetch_assoc()) {
                $wallet = $row;
                break;
            }
            if($wallet){
                if($wallet["balance"] >= $transaction["amount"]){
                    if($payment = $database->insert("payment", [
                        "id_transaction" => $transaction["id"],
                        "status" => "PAID",
                    ])){
                        $database->query("UPDATE wallets SET balance=balance-{$transaction["amount"]} WHERE id = '{$wallet["id"]}'");
                        return [
                            "success" => true,
                            "data" => $payment 
                        ];
                    }
                    else{
                        if($payment = $database->insert("payment", [
                            "id_transaction" => $transaction["id"],
                            "status" => "ERROR TRANSACTION",
                        ])){
                            return [
                                "success" => false,
                                "data" => $payment 
                            ];
                        }
                    }
                }else{
                    if($payment = $database->insert("payment", [
                        "id_transaction" => $transaction["id"],
                        "status" => "INSUFFICIENT_FUNDS",
                    ])){
                        return [
                            "success" => false,
                            "message" => "insufficient funds",
                            "data" => $payment 
                        ];
                    }
                }
            }
        }
        else{
            return [
                "success" => false,
                "message" => "Transaction or token invalid",
            ];
        }

        return [
            "success" => false,
            "message" => "Error in create payment",
        ];
    }
}
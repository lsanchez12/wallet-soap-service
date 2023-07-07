<?php
namespace Services;
include 'database.php';
use Database;

class WalletService {

    private $database;

    public function __construct() {
        $this->database = new Database\Database();
    }
    //USER

    /**
    * @soap
    * @param string $email
    * @param string $password
    * @return object  
    */
    public function login($email,$password){
        try{
            $this->database = new Database\Database();
            $this->database->connect();

            $result = $this->database->query("SELECT id,document,first_name,last_name,phone_number,email,api_token,password FROM users  WHERE email = '{$email}' LIMIT 1");
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
        } catch (\Throwable $e) {
            
            return [
                "success" => false,
                "message" => $e->getMessage()
            ];
        }
    }

    /**
     * @soap
    * @param integer $id
    * @return object  
    */
    public function getUser($id){
        try{
            $this->database = new Database\Database();
            $this->database->connect();

            $result = $this->database->query("SELECT id,document,first_name,last_name,phone_number,email FROM users  WHERE id = {$id} LIMIT 1");
            
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
        } catch (\Throwable $e) {
            
            return [
                "success" => false,
                "message" => $e->getMessage()
            ];
        }
    }

    /**
    * @soap
    * @param Models\User $user
    * @return object  
    */
    public function createUser($user){  
        try {
            $this->database = new Database\Database();
            $this->database->connect();
            $user["api_token"] = md5(uniqid().rand(1000000, 9999999));
            $user["password"] = password_hash($user["password"],PASSWORD_DEFAULT);
            
            if($result = $this->database->insert("users", $user)){
                $this->database->insert("wallets", [
                    "wallet_uuid" => uniqid('wallet_'),
                    "id_user" => $result,
                ]);
                return [
                    "success" => true,
                    "data" => ['id' => $result]
                ];
            }
        } catch (\Throwable $e) {
            
            return [
                "success" => false,
                "message" => $e->getMessage()
            ];
        }
    }

    //WALLET

     /**
    * @soap
    * @param integer $userId
    * @return object  
    */
    public function getWallet($userId){
        try {
            $this->database = new Database\Database();
            $this->database->connect();
    
            $result = $this->database->query("SELECT wallet_uuid FROM wallets WHERE id_user = {$userId} LIMIT 1");
            
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
        } catch (\Throwable $e) {
            
            return [
                "success" => false,
                "message" => $e->getMessage()
            ];
        }
    }

    /**
    * @soap
    * @param string $uuid
    * @return object  
    */
    public function getBalance($uuid){
        try {
            $this->database = new Database\Database();
            $this->database->connect();
    
            $result = $this->database->query("SELECT balance FROM wallets WHERE wallet_uuid = '{$uuid}' LIMIT 1");
            
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
        } catch (\Throwable $e) {
            
            return [
                "success" => false,
                "message" => $e->getMessage()
            ];
        }
    }

    /**
    * @soap
    * @param string $uuid
    * @param float $amount
    * @return object  
    */
    public function chargeWallet($uuid,$amount){
        try {
            $this->database = new Database\Database();
            $this->database->connect();
    
            if ($this->database->query("UPDATE wallets SET balance=balance+{$amount} WHERE wallet_uuid = '{$uuid}'") === TRUE) {
                
                return [
                    "success" => true,
                    "message" => "Charge success"
                ];
            } 
            return [
                "success" => false,
                "message" => "Charge error"
            ];
        } catch (\Throwable $e) {
            
            return [
                "success" => false,
                "message" => $e->getMessage()
            ];
        }
    }

    //TRANSACTION
    /**
    * @soap
    * @param float $amount
    * @param integer $idUser
    * @return object  
    */
    public function createTransaction($amount, $idUser){
        try {
            $this->database = new Database\Database();
            $this->database->connect();
            $transaction_uuid = uniqid('transaction_');
            if($transaction_id = $this->database->insert("transactions", [
                "id_user" => $idUser,
                "transaction_uuid" => $transaction_uuid,
                "amount" => $amount,
                "token" => mt_rand(100000,999999),
            ])){
                
                return [
                    "success" => true,
                    "data" => ["transaction_uuid" => $transaction_uuid]
                ];
            }
    
            return [
                "success" => false,
                "message" => "Error in create transaction",
            ];
        } catch (\Throwable $e) {
            
            return [
                "success" => false,
                "message" => $e->getMessage()
            ];
        }
    }

    /**
    * @soap
    * @param string $transaction_uuid
    * @return object  
    */
    public function getTransaction($transaction_uuid){
        try {
            $this->database = new Database\Database();
            $this->database->connect();

            $result = $this->database->query("SELECT * FROM transactions LEFT JOIN payments ON payments.id_transaction = transactions.id  WHERE transactions.transaction_uuid = '{$transaction_uuid}' LIMIT 1");
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
        } catch (\Throwable $e) {
            
            return [
                "success" => false,
                "message" => $e->getMessage()
            ];
        }
        
    }

    /**
    * @soap
    * @param string $transaction_uuid
    * @return object  
    */
    public function getTransactions($idUser){
        try {
            //code...
            $this->database = new Database\Database();
            $this->database->connect();
    
            $result = $this->database->query("SELECT transactions.id, transactions.transaction_uuid,transactions.amount,transactions.created_at as created_transaction, payments.status,payments.created_at as created_payment FROM transactions LEFT JOIN payments ON payments.id_transaction = transactions.id  WHERE transactions.id_user = {$idUser}");
            
            $data = $result->fetch_all(MYSQLI_ASSOC);
            $result->free_result();

            
    
            return [
                "success" => true,
                "data" => $data
            ];
            
            return [
                "success" => false,
                "message" => "Transaction not found"
            ];
        } catch (\Throwable $e) {
            
            return [
                "success" => false,
                "message" => $e->getMessage()
            ];
        }
    }
    //PAYMENT
    /**
    * @soap
    * @param string $transaction_uuid
    * @param string $token
    * @return object  
    */
    public function chargePayment($transaction_uuid, $token){
        try {
            $this->database = new Database\Database();
            $this->database->connect();
            $result = $this->database->query("SELECT id,amount,id_user FROM transactions WHERE transaction_uuid = '{$transaction_uuid}' and token='{$token}' LIMIT 1");
            $transaction = null;
            while ($row = $result->fetch_assoc()) {
                $transaction = $row;
                break;
            }
            if($transaction){
                $wallet = null;
                $result = $this->database->query("SELECT id,balance FROM wallets WHERE id_user = '{$transaction["id_user"]}' LIMIT 1");
                while ($row = $result->fetch_assoc()) {
                    $wallet = $row;
                    break;
                }
                if($wallet){
                    if($wallet["balance"] >= $transaction["amount"]){
                        if($payment = $this->database->insert("payments", [
                            "id_transaction" => $transaction["id"],
                            "status" => "PAID",
                        ])){
                            $this->database->query("UPDATE wallets SET balance=balance-{$transaction["amount"]}, updated_at = NOW() WHERE id = '{$wallet["id"]}'");
                            return [
                                "success" => true,
                                "data" => $payment 
                            ];
                        }
                        else{
                            if($payment = $this->database->insert("payments", [
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
                            return [
                                "success" => false,
                                "message" => "insufficient funds",
                                "data" => $payment 
                            ];
                    }
                }
            }
            else{
                return [
                    "success" => false,
                    "message" => "Token invalid",
                ];
            }
    
            return [
                "success" => false,
                "message" => "Error in create payment",
            ];
        } catch (\Throwable $e) {
            
            return [
                "success" => false,
                "message" => $e->getMessage()
            ];
        }
    }
}
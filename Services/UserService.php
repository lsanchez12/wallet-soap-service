<?php
namespace Services;
include 'database.php';
use Database;

class UserService {
 /**
  * @soap
  * @param integer $id
  * @return object  
  */
    public function getUser($id){
        $database = new Database\Database();
        $database->connect();

        $result = $database->query("SELECT * FROM users  WHERE id = {$id} LIMIT 1");
        while ($row = $result->fetch_assoc()) {
            return $row;
        }
        return [];
    }

    /**
    * @soap
    * @param Models\User $user
    * @return object  
    */
    public function createUser($user){  

        return "";
    }
}
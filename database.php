<?php
    namespace Database;
    require_once __DIR__ . "/vendor/autoload.php";
    use Dotenv;
    use mysqli;
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
    $dotenv->safeLoad();
    
    class Database {
        private $host;
        private $username;
        private $password;
        private $database;
        private $conn;
    
        public function __construct() {
            $this->host = $_ENV['SERVER_DB'];
            $this->username = $_ENV['USERNAME_DB'];
            $this->password = $_ENV['PASSWORD_DB'];
            $this->database = $_ENV['NAME_DB'];
        }
    
        public function connect() {
            $this->conn = new mysqli($this->host, $this->username, $this->password, $this->database);
    
            if ($this->conn->connect_error) {
                die("Error de conexión: " . $this->conn->connect_error);
            }
        }

        public function close() {
            $this->conn->close();
        }
    
        public function query($sql) {
            $result = $this->conn->query($sql);
    
            if (!$result) {
                die("Error de consulta: " . $this->conn->error);
            }
    
            return $result;
        }

        public function insert($table, $array) {
            $insert = [];
            $values = [];
            foreach ($array as $key => $value) {
                $insert[] = $key;
                $values[] = "'".$value."'";
            }
            $sql = "INSERT INTO {$table} (".implode(",",$insert).",created_at, updated_at) VALUES(".implode(",",$values).", NOW(), NOW())";

            if ($this->conn->query($sql) === TRUE) {
                return $this->conn->insert_id;
            } else {
                return false;
            }
        }
    
    }
?>
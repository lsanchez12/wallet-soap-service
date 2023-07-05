<?php
ini_set("soap.wsdl_cache_enabled", "0");
require_once __DIR__ . "/vendor/autoload.php";
include 'Services/BookService.php';
include 'Models/Book.php';

include 'Services/UserService.php';
include 'Models/User.php';
include 'Services/WalletService.php';
include 'Models/Wallet.php';
include 'Services/TransactionService.php';
include 'Models/Transaction.php';
include 'Services/PaymentService.php';
include 'Models/Payment.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();


$classes = [
    'book' => 'Services\BookService',
    'user' => 'Services\UserService',
    'wallet' => 'Services\UserService',
    'transaction' => 'Services\UserService',
    'payment' => 'Services\UserService',
];


foreach ($classes as $key => $class) {
    $serviceURI =  $_ENV['URL_SERVER'];
    $wsdlGenerator = new PHP2WSDL\PHPClass2WSDL($class, $serviceURI);
    $wsdlGenerator->generateWSDL(true);
    $wsdlXML = $wsdlGenerator->save('wsdl/'. $key .'.wsdl');
}
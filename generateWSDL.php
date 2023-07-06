<?php
ini_set("soap.wsdl_cache_enabled", "0");
require_once __DIR__ . "/vendor/autoload.php";
include 'Services/BookService.php';
include 'Models/Book.php';

include 'Services/WalletService.php';
include 'Models/User.php';
include 'Models/Wallet.php';
include 'Models/Transaction.php';
include 'Models/Payment.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();

$class = 'Services\WalletService';

$serviceURI =  $_ENV['URL_SERVER'];
$wsdlGenerator = new PHP2WSDL\PHPClass2WSDL('Services\WalletService', $serviceURI);
$wsdlGenerator->generateWSDL(true);
$wsdlXML = $wsdlGenerator->save('wsdl/wallet.wsdl');
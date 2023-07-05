<?php
ini_set("soap.wsdl_cache_enabled", "0");
require_once __DIR__ . "/vendor/autoload.php";
include 'Services/BookService.php';
include 'Models/Book.php';


$class = "Catalogs\BookService";

$serviceURI = "http://localhost:8091";
$wsdlGenerator = new PHP2WSDL\PHPClass2WSDL($class, $serviceURI);


$wsdlGenerator->generateWSDL(true);
// Dump as string
$wsdlXML = $wsdlGenerator->dump();
// Or save as file
$wsdlXML = $wsdlGenerator->save('book.wsdl');
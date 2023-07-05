<?php
    ini_set("soap.wsdl_cache_enabled", "0");
    require_once __DIR__ . "/vendor/autoload.php";
    include 'Services/BookService.php';

    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
    $dotenv->safeLoad();

    $class = "Services\BookService";
    $wsdl = "wsdl/book.wsdl";


    // initialize SOAP Server
    $server = new SoapServer($wsdl,[
        'uri'=> $_ENV['URL_SERVER']
    ]);

    $server->setClass($class);

    // start handling requests
    $server->handle();
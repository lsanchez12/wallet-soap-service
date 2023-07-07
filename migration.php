<?php
    require_once __DIR__ . "/vendor/autoload.php";
    include 'database.php';

    $database = new  Database\Database();
    $database->connect();

    $fileSQL = file_get_contents(__DIR__ .'/sql/migration.sql');

    $database->multiQuery($fileSQL);

    $database->close();

    echo "Migration Success";
    
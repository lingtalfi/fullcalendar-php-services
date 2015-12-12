<?php


use QuickPdo\QuickPdo;

require_once __DIR__ . "/functions/az.php";
require_once __DIR__ . "/functions/main.php";
require_once __DIR__ . '/classes/BeeAutoloader.php';
require_once __DIR__ . '/classes/ButineurAutoloader.php';


//------------------------------------------------------------------------------/
// PHP CONFIG
//------------------------------------------------------------------------------/
ini_set('error_reporting', -1);
mb_internal_encoding('UTF-8');
ini_set('display_errors', 1);


//------------------------------------------------------------------------------/
// AUTOLOAD
//------------------------------------------------------------------------------/
ButineurAutoLoader::getInst()
    ->addLocation(__DIR__ . "/modules")
    ->start();




//------------------------------------------------------------------------------/
// INIT DATABASE
//------------------------------------------------------------------------------/
QuickPdo::setConnection(
    "mysql:dbname=calendar;host=127.0.0.1",  // pdo dsn
    'root',  // user name
    'root',  // password
    array(
        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'UTF8'",
        PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
    )
);
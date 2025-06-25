<?php

require_once ('db-class-master/MysqliDb.php');
$isServer = true;

if($isServer == false) {
    /* Local Database */
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "cust_postback_data";
}
else {
    /* Server Database */
    $servername = "localhost";
    $username = "postbackuser";
    $password = "HB4cLc8uBvIISlwpwnSe";
    $dbname = "postback";
}


$dbConn = new MysqliDb ($servername, $username, $password, $dbname);

// $dbConn->autoReconnect = false;


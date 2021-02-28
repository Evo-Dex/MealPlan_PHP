<?php

/*
 * Error Reporting
 */

error_reporting(E_ALL ^ E_NOTICE);

/*
 * Datenbankverbindung herstellen
 * 
 */

# Verbindungsangaben
$dbuser = 'root';
$dbpassword = 'cimdata';
$dsn = 'mysql:dbname=speiseplan;host=localhost';

try{

    # PDO-Objekt bilden
    $pdo = new PDO($dsn, $dbuser, $dbpassword);

}
catch( PDOException $ausnahme ){
    
    echo $ausnahme->getMessage();
}
$pdo->query("SET GLOBAL sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''))");


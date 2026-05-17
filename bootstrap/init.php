<?php
require 'constance.php';
require 'config.php';

try {
    $pdo = new PDO("mysql:host=$database_config->host;dbname=$database_config->dbname;charset =$database_config->charset", $database_config->user, $database_config->password);
} catch (PDOException $e) {
    die('Connection failed: ' . $e->getMessage());
}



echo '<pre>';
var_dump($pdo);
echo '</pre>';
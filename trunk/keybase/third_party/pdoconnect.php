<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

$dsn = 'mysql:dbname=keybase;host=203.55.15.78';
$user = 'keybase';
$password = 'fb5c59c2c634';

try {
    $db = new PDO($dsn, $user, $password);
} catch (PDOException $e) {
    echo 'Connection failed: ' . $e->getMessage();
}

?>

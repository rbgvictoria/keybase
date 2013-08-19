<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

$dsn = 'mysql:dbname=keybase;host=localhost';
$user = 'root';
$password = 'rootpwd';

try {
    $db = new PDO($dsn, $user, $password);
} catch (PDOException $e) {
    echo 'Connection failed: ' . $e->getMessage();
}


?>

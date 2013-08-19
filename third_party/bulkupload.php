<?php

require_once('pdoconnect.php');
require_once('lpxktokeybase.php');

$load = new LpxkToKeyBase($db);

if (!isset($argv[1])) exit;
chdir('..');
$filename = getcwd() . '/temp_in/' . $argv[1] . '/keyids.csv';
$handle = fopen($filename, 'r');

while (!feof($handle)) {
    $line = fgetcsv($handle);
    if ($line[0]) {
        $userid = (isset($argv[2]) && is_numeric($argv[2])) ? $argv[2] : FALSE;
        $fname = getcwd() . '/temp_in/' . $argv[1] . '/key_' . $line[0] . '.csv';
        $load->LpxkToKeyBase($line[1], $fname, 'delimitedtext', FALSE, 'comma', $userid);
        echo $fname . "\n";
    }
}
echo "Done\n";

fclose($handle);



?>

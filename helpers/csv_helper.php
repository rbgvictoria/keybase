<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

if ( ! function_exists('csv_detect_delimiter')) {
    function csv_detect_delimiter($file, $delimiter=FALSE) {
        $infile = fopen('uploads/' . $file, 'r');
        $linearray = array();
        while (!feof($infile)) {
            $linearray[] = fgets($infile);
        }
        
        if (!$delimiter) {
            $n = count($linearray);
            $i = 0;
            $numcols = array();
            while ($i < 10 && $i < $n) {
                $row = str_getcsv($linearray[$i], "\t");
                $numcols[] = count($row);
                $i++;
            }
            $sum = array_sum($numcols);
            $count = count($numcols);
            $delimiter = ($sum/$count > 2) ? 'tab' : 'comma';
        }
        
        $separator = ($delimiter == 'tab') ? "\t" : ",";
        
        $input_key = array();
        foreach ($linearray as $line) {
            if ($line) {
                $input_key[] = str_getcsv($line, $separator);
            }
        }
        
        return (object) array(
            'delimiter' => $delimiter,
            'text_array' => $input_key
        );
    }

}
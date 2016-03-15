<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

if ( ! function_exists('autoVersion')) {
    function autoVersion($url){
        $path = pathinfo($url);
        $ver = filemtime(getcwd() . '/' .$url);
        echo $path['dirname']. '/'. $path['filename'] . '.' . $ver . '.' . $path['extension'];
    }    
}


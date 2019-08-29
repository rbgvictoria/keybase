<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

if ( ! function_exists('doCurl')) {
    function doCurl($url, $query=FALSE, $proxy=FALSE) {
        $ch = curl_init();
        if ($query) {
            curl_setopt($ch, CURLOPT_URL, $url . '?' . $query);
        }
        else {
            curl_setopt($ch, CURLOPT_URL, $url);
        }
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        
        /*if ($proxy) {
            curl_setopt($ch, CURLOPT_PROXY, "http://10.15.14.4:8080"); 
            curl_setopt($ch, CURLOPT_PROXYPORT, 8080); 
        }*/
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 0);
        curl_setopt($ch, CURLOPT_TIMEOUT, 300);
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }
}

if (!function_exists('curl_post')) {
    function curl_post($url, $data, $proxy=FALSE) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        /*if ($proxy) {
            curl_setopt($ch, CURLOPT_PROXY, "http://10.15.14.4:8080"); 
            curl_setopt($ch, CURLOPT_PROXYPORT, 8080); 
        }*/
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 0);
        curl_setopt($ch, CURLOPT_TIMEOUT, 300);
        $result = curl_exec($ch);
        //$info = curl_getinfo($ch);
        curl_close($ch);
        return $result;
    }
}

if (!function_exists('curl_delete')) {
    function curl_delete($url, $id, $data='', $proxy=FALSE) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url . '/' . $id);
        //curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
        curl_setopt($ch,CURLOPT_POST, TRUE);
        curl_setopt($ch,CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        /*if ($proxy) {
            curl_setopt($ch, CURLOPT_PROXY, "http://10.15.14.4:8080"); 
            curl_setopt($ch, CURLOPT_PROXYPORT, 8080); 
        }*/
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 0);
        curl_setopt($ch, CURLOPT_TIMEOUT, 300);
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }
}
<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

class KeyService {
    private $ci;
    
    public function __construct() {
        $this->ci =& get_instance();
        $this->ci->load->helper('curl');
    }
    
    public function getKeyMetadata($keyid) {
        $url = 'http://data.rbg.vic.gov.au/dev/keybase-ws/ws/key_meta/' . $keyid;
        $result = doCurl($url, FALSE, TRUE);
        return json_decode($result);
    }
    
    public function search($searchstring) {
        $url = 'http://data.rbg.vic.gov.au/dev/keybase-ws/ws/search_items/' . $searchstring;
        $result = doCurl($url, FALSE, TRUE);
        return json_decode($result);
    }
}
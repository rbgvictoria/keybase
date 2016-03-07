<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

class UserService {
    private $ci;
    
    public function __construct() {
        $this->ci =& get_instance();
        $this->ci->load->helper('curl');
    }
    
    public function getProjectUsers($project) {
        $url = 'http://data.rbg.vic.gov.au/dev/keybase-ws/ws/project_users/' . $project;
        $result = doCurl($url, FALSE, TRUE);
        return json_decode($result);
    }
}
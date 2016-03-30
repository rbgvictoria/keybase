<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

require_once 'Service.php';

class UserService extends Service {
    
    public function __construct() {
        parent::__construct();
    }
    
    public function getProjectUsers($project) {
        $url = 'http://data.rbg.vic.gov.au/dev/keybase-ws/ws/project_users/' . $project;
        $result = doCurl($url, FALSE, TRUE);
        return json_decode($result);
    }
}

/* End of file UserService.php */
/* Location: ./libraries/UserService.php */

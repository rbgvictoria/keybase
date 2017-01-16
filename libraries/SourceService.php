<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once 'Service.php';

class SourceService extends Service {
    
    public function __construct() {
        parent::__construct();
    }
    
    public function getSource($id) {
        $url = $this->ws_url() . 'ws/source_get/' . $id;
        $result = doCurl($url, FALSE, TRUE);
        return json_decode($result);
    }
    
}

/* End of file SourceService.php */
/* Location: ./libraries/SourceService.php */

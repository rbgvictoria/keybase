<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

class Service {
    protected $ci;
    
    public function __construct() {
        $this->ci =& get_instance();
        $this->ci->load->helper('curl');
    }
    
    public function ws_url() {
        return 'http://data.rbg.vic.gov.au/dev/keybase-ws/';
    }
}

/* End of file Service.php */
/* Location: ./libraries/Service.php */

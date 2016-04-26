<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

class Service {
    protected $ci;
    
    public function __construct() {
        $this->ci =& get_instance();
        $this->ci->load->helper('curl');
    }
    
    public function ws_url() {
        return $this->ci->config->item('ws_url');
    }
}

/* End of file Service.php */
/* Location: ./libraries/Service.php */

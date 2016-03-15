<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class KeyBase extends CI_Controller {
    var $data;

    function  __construct() {
        parent::__construct();
        $this->load->database();
        $this->load->library('session');
        $this->load->helper('url');
        $this->load->helper('form');
        $this->load->helper('versioning');
        
        // Allow for custom style sheets and javascript
        $this->data['css'] = array();
        $this->data['js'] = array();
        $this->data['iehack'] = FALSE;
        
    }

}

/* End of file keybase.php */
/* Location: ./controllers/keybase.php */
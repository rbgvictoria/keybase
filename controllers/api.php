<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class API extends CI_Controller {
    var $data;

    public function  __construct() {
        parent::__construct();
        $this->load->database();
        $this->load->library('session');
        $this->load->helper('url');
        $this->load->helper('form');
        $this->load->helper('json');
        $this->load->helper('versioning');
        $this->output->enable_profiler(false);
        $this->load->model('keymodel');
        $this->load->model('webservicesmodel', 'ws');
        $this->data = array();
    }
    
    public function index() {
        $this->load->view('api/index', $this->data);
    }
}
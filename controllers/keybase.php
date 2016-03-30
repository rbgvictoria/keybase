<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class KeyBase extends CI_Controller {
    var $data;

    function  __construct() {
        parent::__construct();
        $this->load->database();
        $this->load->library('session');
        $this->load->helper('url');
        $this->load->helper('form');
        $this->load->helper('curl');
        $this->load->helper('versioning');
        $this->load->library('ProjectService');
        $this->load->library('StaticContent');
        
        // Allow for custom style sheets and javascript
        $this->data['css'] = array();
        $this->data['js'] = array();
        $this->data['iehack'] = FALSE;
        
    }
    
    public function index() {
        $this->data['ProjectStats'] = $this->projectservice->getProjects();
        $keys = array();
        $taxa = array();
        foreach ($this->data['ProjectStats'] as $project) {
            $keys[] = $project->num_keys;
        }
        $this->data['NumKeys'] = array_sum($keys);
        $this->data['NumTaxa'] = $this->projectservice->getTotalNumberOfItems();
        
        $this->data['staticcontent'] = $this->staticcontent->getStaticContent('home');
        
        $this->load->view('static/home', $this->data);
    }

    public function ws_url() {
        return 'http://data.rbg.vic.gov.au/dev/keybase-ws/';
    }
    
    public function st() {
        $uri = str_replace('keybase/st/', '', $this->uri->uri_string());
        $cleanuri = str_replace('/_edit', '', $uri);
        
        if ($uri == 'keybase/st') {
            $this->data['pages'] = $this->staticcontent->getStaticPages();
        }
        
        $this->data['staticcontent'] = $this->staticcontent->getStaticContent($cleanuri);
        if (strpos($uri, '/_edit')) {
            if (isset($this->session->userdata['id']) && in_array($this->session->userdata('id'), array(1, 2))) {
                $this->data['js'][] = base_url() . 'js/jquery.keybase.loadimage.js';

                if ($this->input->post('submit')) {
                    $this->staticcontent->updateStaticContent($this->input->post());
                    redirect('/keybase/st/' . $cleanuri);
                }
                $this->load->view('static/edit', $this->data);
                return TRUE;
            }
        }
        if ($cleanuri == 'citation')
            $this->data['staticcontent']['PageContent'] = str_replace ('&lt;today&gt;', date('d-m-Y'), $this->data['staticcontent']['PageContent']);

        $this->load->view('static/show', $this->data);
    }
    
    public function createstaticpage() {
        if (!(isset($this->session->userdata['id']) && in_array($this->session->userdata('id'), array(1, 2)))) exit;
        if ($this->input->post('submit')) {
            $this->staticcontent->createNewStaticPage($this->input->post());
            redirect('key/st/' . $this->input->post('uri') . '/_edit');
        }
        
        $this->load->view('static/create', $this->data);
    }
    
    public function loadimage() {
        $this->output->enable_profiler(FALSE);
        if ($this->input->post('submit') && $_FILES['st_image']['tmp_name']) {
            move_uploaded_file($_FILES['st_image']['tmp_name'], getcwd() . '/images/st/' . $_FILES['st_image']['name']);
            if ($this->input->post('loadedimages'))
                $this->data['loadedimages'] = unserialize ($this->input->post('loadedimages'));
            else
                $this->data['loadedimages'] = array();
            $this->data['loadedimages'][] = array(
                'name' => $_FILES['st_image']['name'],
                'type' => $_FILES['st_image']['type'],
                'size' => $_FILES['st_image']['size'],
            );
        }
        $this->load->view('static/loadimage', $this->data);
    }
}

/* End of file keybase.php */
/* Location: ./controllers/keybase.php */
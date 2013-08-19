<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class AutoComplete extends CI_Controller {

    var $data;

    function  __construct() {
        parent::__construct();
        $this->load->database();
        $this->load->library('session');
        $this->load->helper('url');
        $this->output->enable_profiler(FALSE);
        $this->load->model('autocompletemodel');
    }
    
    public function findprojecttaxa($project) {
        if (empty($_GET['term'])) exit ;
        $q = strtolower($_GET["term"]);
        
        $filter = FALSE;
        if (isset($this->session->userdata['GlobalFilterOn']) && $this->session->userdata['GlobalFilterOn']) {
            $this->load->model('projectmodel');
            $filter = $this->projectmodel->getFilterKeys($project);
        }

        $items = $this->autocompletemodel->getProjectTaxa($project, $q, $filter);

        echo json_encode($items);
    }
    
    public function searchtaxon() {
        if (empty($_GET['term'])) exit;
        $q = strtolower($_GET['term']);
        $items = $this->autocompletemodel->getTaxa($q);
        echo json_encode($items);
    }
    
}

/* End of file autocomplete.php */
/* Location: ./controllers/autocomplete.php */
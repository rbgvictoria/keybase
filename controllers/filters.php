<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once 'keybase.php';

class Filters extends KeyBase {
    var $data;

    function  __construct() {
        parent::__construct();
        $this->load->model('keymodel');
        $this->load->model('filtermodel');
        $this->output->enable_profiler(true);
    }
    
    function index() {
        $this->show();
    }

    public function show($filterid=FALSE) {
        $this->data['js'][] = base_url() . 'js/jspath.min.js';
        $this->data['js'][] = base_url() . 'js/jquery.keybase.globalfilter.js?v=1.0';
        
        $this->data['filterid'] = $filterid;
        
        if ($this->input->post('update')) {
            $this->update_filter();
        }
        elseif ($this->input->post('export')) {
            redirect('webservices/getFilter?id=' . $this->input->post('filter'));
        }
        elseif ($this->input->post('delete')) {
            $this->filtermodel->deleteGlobalFilter($this->input->post('filter'));
            redirect('/key/filter');
        }
        
        $this->data['projects'] = $this->filtermodel->getProjects();
        $this->data['filters'] = $this->filtermodel->getFilters();
        if ($this->session->userdata('id')) {
            $this->data['projectFilters'] = $this->filtermodel->getProjectFilters(FALSE, $this->session->userdata('id'));
        }
        
        $this->load->view('filters/show', $this->data);
    }
    
    
    
}

/* End of file filters.php */
/* Location: ./controllers/filters.php */
<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once 'keybase.php';

class Filters extends KeyBase {
    var $data;

    function  __construct() {
        parent::__construct();
        $this->load->model('keymodel');
        $this->load->model('filtermodel');
    }
    
    function index() {
        $this->show();
    }

    public function show($filterid=FALSE) {
        $this->data['js'][] = base_url() . 'js/jspath.min.js';
        $this->data['js'][] = base_url() . 'js/jquery.keybase.globalfilter.js?v=1.0';
        
        $this->data['filterid'] = $filterid;
        
        if ($this->input->post('update')) {
            $this->edit();
        }
        elseif ($this->input->post('export')) {
            $this->export();
        }
        elseif ($this->input->post('delete')) {
            $this->delete();
        }
        
        $this->data['projects'] = $this->filtermodel->getProjects();
        $this->data['filters'] = $this->filtermodel->getFilters();
        if ($this->session->userdata('id')) {
            $this->data['projectFilters'] = $this->filtermodel->getProjectFilters(FALSE, $this->session->userdata('id'));
        }
        
        $this->load->view('filters/show', $this->data);
    }
    
    private function edit() {
        if (!$this->input->post('taxa')) return FALSE;
        
        $taxa = preg_split("/[\r|\n]+/", trim($this->input->post('taxa')));
        foreach ($taxa as $key=>$value) {
            $taxa[$key] = trim($value);
        }
        
        $projects = $this->input->post('projects');
        if (!$projects[0]) $projects = FALSE;

        $filterItems = $this->filtermodel->findInKeyBase($taxa, $projects);
        
        //$filterid = $this->filtermodel->getKeys($projects, $this->input->post('filter'), $this->input->post('filtername'));
        $filterid = $this->filtermodel->updateFilter($projects, $this->input->post('filterid'), $this->input->post('filtername'));
        
        $this->data['itemsfound'] = $this->filtermodel->itemsFound();
        $this->data['itemsnotfound'] = $this->filtermodel->itemsNotFound();
        
        redirect('filters/show/' . $filterid);
    }
    
    private function delete() {
        $this->filtermodel->deleteGlobalFilter($this->input->post('filterid'));
        redirect('filters');
    }
    
    private function export() {
        redirect('webservices/getFilter?id=' . $this->input->post('filterid'));
    }

    public function setProjectFilter() {
        $this->load->model('filtermodel');
        $lastQuery = $this->filtermodel->setProjectFilter();
        echo json_encode($lastQuery);
    }

    
}

/* End of file filters.php */
/* Location: ./controllers/filters.php */
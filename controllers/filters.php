<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once 'keybase.php';

class Filters extends KeyBase {
    var $data;

    function  __construct() {
        parent::__construct();
        $this->load->library('FilterService');
        $this->load->library('ProjectService');
        if (!$this->session->userdata('id')) {
            redirect(site_url());
        }
    }
    
    function index() {
        $this->show();
    }

    public function show($filterid=FALSE) {
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
        
        $this->data['projects'] = $this->projectservice->getProjects();
        if ($this->session->userdata('id')) {
            $this->data['filters'] = $this->filterservice->getFilters(FALSE, $this->session->userdata('id'));
            $this->data['projectFilters'] = $this->filterservice->getProjectFilters(FALSE, $this->session->userdata('id'));
        }
        else {
            $this->data['filters'] = $this->filterservice->getFilters(FALSE, FALSE, $this->session->userdata('session_id'));
        }
        
        $this->load->view('filters/show', $this->data);
    }
    
    private function edit() {
        if (!$this->input->post('taxa')) return FALSE;
        
        $found = preg_split("/[\r|\n]+/", trim($this->input->post('taxa')));
        $notfound = preg_split("/[\r|\n]+/", trim($this->input->post('items_not_found')));
        $taxa = array();
        foreach ($found as $key=>$value) {
            $taxa[] = trim($value);
        }
        foreach ($notfound as $key=>$value) {
            $taxa[] = trim($value);
        }
        
        $data = $this->input->post();
        $data['taxa'] = implode("\r\n", $taxa);
        $filter = $data['filterid'];
        if ($filter) {
            unset($data['filterid']);
            $filterid = $this->filterservice->updateFilter($filter, $data);
        }
        else {
            $filterid = $this->filterservice->createFilter($data);
            echo $filterid;
        }
        redirect('filters/show/' . $filterid);
    }
    
    private function delete() {
        $result = $this->filterservice->deleteFilter($this->input->post('filterid'));
        redirect('filters');
    }
    
}

/* End of file filters.php */
/* Location: ./controllers/filters.php */
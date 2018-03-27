<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once 'keybase.php';

class Projects extends KeyBase {
    var $data;

    function  __construct() {
        parent::__construct();
        $this->load->library('ProjectService');
        $this->load->library('FilterService');
        //$this->output->enable_profiler();
    }
    
    function index() {
        $this->data['projects'] = $this->projectservice->getProjects();
        $keys = array();
        foreach ($this->data['projects'] as $project) {
            $keys[] = $project->num_keys;
        }
        $this->data['totalKeys'] = array_sum($keys);
        $this->data['totalItems'] = $this->projectservice->getTotalNumberOfItems();
        $this->load->view('projects/index', $this->data);
    }
    
    function show($project) {
        $this->data['projectid'] = $project;
        $this->data['project'] = $this->projectservice->getProjectMetadata($project);
        $this->data['users'] = $this->projectservice->getProjectUsers($project);
        $this->data['myFilters'] = array();
        if ($this->session->userdata('id')) {
            $this->data['myFilters'] = $this->filterservice->getFilters($project, 
                    $this->session->userdata('id'));
        }
        $this->data['projectFilters'] = $this->filterservice->getProjectFilters($project);
        $this->data['manageFilters'] = $this->filterservice->getManageFilters($project);
        $this->load->view('projects/show', $this->data);
    }
    
    public function edit($project=false, $cbox=false) {
        $this->data['js'][] = 'http://www.rbg.vic.gov.au/dbpages/lib/ckeditor/ckeditor.js';
        $this->output->enable_profiler(false);
        if (!$project) {
            redirect('projects');
        }
        if (!isset($this->session->userdata['id'])) {
            redirect("projects/show/$project");
        }
        if ($this->input->post('submit')) {
            $result = $this->projectservice->editProjectMetadata($this->input->post());
            redirect("projects/show/$result");
        }
        
        if ($this->input->post('cancel')) {
            redirect("projects/show/" . $project);
        }
        
        $this->data['project'] = json_decode(doCurl($this->ws_url() . 'ws/project_meta_get/' . $project, FALSE, TRUE));
        $this->load->view('projects/edit', $this->data);
    }
    
    public function create() {
        $this->data['js'][] = 'http://www.rbg.vic.gov.au/dbpages/lib/ckeditor/ckeditor.js';
        $this->data['js'][] = base_url() . autoVersion('js/jquery.keybase.editproject.js');
        if (isset($this->session->userdata['id'])) {
            if ($this->input->post('submit')) {
                $projectid = $this->projectservice->createProject($this->input->post());
                redirect('projects/show/' . $projectid);
            }
            if ($this->input->post('cancel')) {
                redirect('projects');
            }

            $this->data['cbox'] = FALSE;
            $this->load->view('projects/edit', $this->data);
        }
        else {
            redirect('projects');
        }
    }
    
    public function delete($project) {
        $response = $this->projectservice->deleteProject($project);
        if ($response) {
            redirect('projects');
        }
    }
    
    public function load_items($project)
    {
        if ($this->input->post('submit')) {
            $temp_file = $_FILES['file_content']['tmp_name'];
            $handle = fopen($temp_file, 'r');
            $csv = array();
            while(!feof($handle)) {
                $csv[] = fgetcsv($handle);
            }
            fclose($handle);
            $data = array();
            $data['items'] = json_encode($csv);
            $this->projectservice->loaditems($project, $data);
            redirect('projects/show/' . $project);
        }
        $this->load->view('projects/load_items', $this->data);
    }
    
}

/* End of file projects.php */
/* Location: ./controllers/projects.php */
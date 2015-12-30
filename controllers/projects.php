<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once 'keybase.php';

class Projects extends KeyBase {
    var $data;

    function  __construct() {
        parent::__construct();
        $this->load->model('keymodel');
        $this->load->model('projectmodel');
    }
    
    function index() {
            $this->data['projects'] = $this->keymodel->getProjects();
            $this->load->view('projects/index', $this->data);
    }
    
    function show($project) {
        $this->data['js'][] = base_url() . 'js/jspath.min.js';
        $this->data['js'][] = base_url() . 'js/dynatree/jquery.dynatree.js';
        $this->data['js'][] = base_url() . 'js/jquery.keybase.project_new.js?v=1.0';
        $this->data['js'][] = base_url() . 'js/jquery.keybase.project.js?v=1.0';
        $this->data['css'][] = base_url() . 'css/dynatree/skin/ui.dynatree.css';
        $this->data['js'][] = 'http://www.rbg.vic.gov.au/dbpages/lib/ckeditor/ckeditor.js';
        $this->data['js'][] = base_url() . 'js/ckeditor_customconfig.js?v=1.0';
        $this->data['js'][] = base_url() . 'js/medialize-jQuery-contextMenu/jquery.contextMenu.js';
        $this->data['css'][] = base_url() . 'js/medialize-jQuery-contextMenu/jquery.contextMenu.css';
        $this->data['projectid'] = $project;
        $this->data['project'] = $this->keymodel->getProjectData($project);
        $this->data['users'] = $this->keymodel->getProjectUsers($project);
        $this->load->model('filtermodel');
        $this->data['myFilters'] = $this->filtermodel->getFilters($project);
        $this->data['projectFilters'] = $this->filtermodel->getProjectFilters($project);
        $this->data['manageFilters'] = $this->filtermodel->manageFilters($project);
        $this->load->view('projects/show', $this->data);
    }
    
    public function edit($project=false, $cbox=false) {
        $this->data['js'][] = 'http://www.rbg.vic.gov.au/dbpages/lib/ckeditor/ckeditor.js';
        $this->data['js'][] = base_url() . 'js/jquery.keybase.editproject.js?v=1.0';
        $this->output->enable_profiler(false);
        if (!$project) {
            redirect('projects');
        }
        if (!isset($this->session->userdata['id'])) {
            redirect("projects/show/$project");
        }
        if ($this->input->post('submit')) {
            $this->keymodel->editProject($this->input->post());
            redirect("projects/show/$project");
        }
        
        if ($this->input->post('cancel')) {
            redirect("projects/show/" . $project);
        }
        
        $this->data['cbox'] = $cbox;
        
        $this->data['project'] = $this->keymodel->getProjectData($project);
        $this->load->view('projects/edit', $this->data);
    }
    
    public function add() {
        $this->output->enable_profiler();
        $this->data['js'][] = 'http://www.rbg.vic.gov.au/dbpages/lib/ckeditor/ckeditor.js';
        $this->data['js'][] = base_url() . 'js/jquery.keybase.editproject.js?v=1.0';
        if (isset($this->session->userdata['id'])) {
            if ($this->input->post('submit')) {
                $projectid = $this->keymodel->addProject($this->input->post());
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
    
}

/* End of file projects.php */
/* Location: ./controllers/projects.php */
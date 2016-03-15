<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once 'keybase.php';

class Keys extends KeyBase {
    var $data;

    function  __construct() {
        parent::__construct();
        $this->load->model('keymodel');
        $this->load->library('KeyService');
        $this->load->library('UserService');
        $this->output->enable_profiler(false);
    }
    
    function index() {
        redirect(base_url());
    }
    
    function show($id) {
        if (!$id) {
            redirect(site_url());
        }
        $meta = $this->keyservice->getKeyMetadata($id);
        $this->data['key'] = $meta;
        $this->data['users'] = $this->userservice->getProjectUsers($meta->project->project_id);
        $this->load->view('keys/show', $this->data);
    }
    
    public function search() {
        if (!$this->input->post('searchbox')) {
            redirect($_SERVER['HTTP_REFERER']);
        }
        $searchstring = $this->input->post('searchbox');
        $this->data['search_string'] = $searchstring;
        $this->data['result'] = $this->keyservice->search($searchstring);
        
        $this->load->view('keys/search', $this->data);
    }

    public function edit($key=FALSE) {
        $this->data['js'][] = base_url() . 'js/jquery.keybase.editkey.js?v=1.0';
        $this->data['js'][] = base_url() . 'js/jquery.keybase.keymenu.js?v=1.0';
        
        if (!$key)
            $key = $this->input->post('key_id');
        if (!$key) 
            redirect('key');
        if (!isset($this->session->userdata['id']))
            redirect('keys/show/' . $key . '?tab=3');
        
        if ($this->input->post('submit')) {
            $this->edit_save($key);
        }
        
        if ($this->input->post('submit2')) {
            $this->edit_load_key($key);
        }
        
        if ($this->input->post('submit3')) {
            $this->edit_save_key($key);
        }
        
        if ($this->input->post('cancel')) {
            if ($this->input->post('tempfilename') && file_exists('uploads/' . $this->input->post('tempfilename')))
                unlink ('uploads/' . $this->input->post('tempfilename'));
            redirect($this->input->post('referer'));
        }
        
        $meta = $this->keyservice->getKeyMetadata($key);
        $this->data['key'] = $meta;
        $this->data['referer'] = ($this->input->post('referer')) ? $this->input->post('referer') : $_SERVER['HTTP_REFERER'];
        $this->load->view('keys/edit', $this->data);
    }
    
    private function edit_save($key=FALSE) {
        if ($_FILES['delimitedtext']['tmp_name']) {
            $this->data['key_metadata'] = $this->input->post();
            $filename = $_FILES['delimitedtext']['tmp_name'];
            if ($filename) {
                $this->load->model('lpxktokeybasemodel', 'lpxk');
                $delimiter = $this->input->post('delimiter');
                $tempfile = file_get_contents($_FILES['delimitedtext']['tmp_name']);
                $tempfilename = uniqid();
                file_put_contents('uploads/' . $tempfilename, $tempfile);
                
                $this->data['keyid'] = $key;
                $this->data['tempfilename'] = $tempfilename;
                
                $this->load->helper('csv');
                $csv = csv_detect_delimiter($tempfilename);
                $this->data['delimiter'] = $csv->delimiter;
                $this->data['input_key'] = $csv->text_array;
            }
        }
        else {
            $post['key_metadata'] = json_encode($this->input->post());
            $post['keybase_user_id'] = $this->session->userdata('id');
            $url = 'http://data.rbg.vic.gov.au/dev/keybase-ws/ws/key_meta_post/' . $key;
            $result = curl_post($url, $post, TRUE);
            redirect('keys/show/' . $key);
        }
    }
    
    private function edit_load_key($key=FALSE) {
        $this->data['key_metadata'] = $this->input->post('key_metadata');
        $errors = $this->_checkForErrors($this->input->post('keyid'), 
                $this->input->post('tempfilename'), $this->input->post('delimiter'));
        if (!$errors) {
            $this->edit_save_key($key);
        }
    }
    
    private function edit_save_key($key=FALSE) {
        $post = array();
        $post['key_metadata'] = json_encode($this->input->post('key_metadata'));
        $post['file_content'] = '@uploads/' . $this->input->post('tempfilename') . ';type=text/csv';
        $post['keybase_user_id'] = $this->session->userdata('id');
        
        if ($key) {
            $url = 'http://data.rbg.vic.gov.au/dev/keybase-ws/ws/key_post/' . $key;
        }
        else {
            $url = 'http://data.rbg.vic.gov.au/dev/keybase-ws/ws/key_post';
        }
        $result = curl_post($url, $post, TRUE);
        unlink('uploads/' . $this->input->post('tempfilename'));
        redirect('keys/show/' . $result);
    }
    
    public function create($projectid=FALSE) {
        $meta = new stdClass();
        $meta->project = new stdClass();
        $meta->project->project_id = ($projectid) ? $projectid : $this->input->post('project_id');
        
        $projectdata = $this->keymodel->getProjectData($meta->project->project_id);
        $meta->project->project_name = $projectdata['Name'];
        $this->data['key'] = $meta;
        $this->data['referer'] = ($this->input->post('referer')) ? $this->input->post('referer') : $_SERVER['HTTP_REFERER'];
        
        $this->data['js'][] = base_url() . 'js/jquery.keybase.editkey.js?v=1.0';
        if (!isset($this->session->userdata['id']))
            redirect(site_url());
        
        if ($this->input->post('submit')) {
            if (!($this->input->post('key_name')  && $this->input->post('taxonomic_scope') 
                    && $this->input->post('geographic_scope'))) {
                $this->data['message'][] = 'Please enter all required metadata (name, taxonomic scope, geographic scope).';
            }
            if (!($_FILES['loadfile']['tmp_name'] || $_FILES['delimitedtext']['tmp_name'] 
                    || $this->input->post('loadurl'))) {
                $this->data['message'][] = 'Please select a key file to upload.';
            }
            if (isset($this->data['message'])) {
                $this->load->view('keys/edit', $this->data);
                return;
            }
            $this->edit_save();
        }
        
        if ($this->input->post('submit2')) {
            $this->edit_load_key();
        }
        
        if ($this->input->post('submit3')) {
            $this->edit_save_key();
        }
        
        if ($this->input->post('cancel')) {
            if ($this->input->post('tempfilename') && file_exists('uploads/' . $this->input->post('tempfilename')))
                unlink ('uploads/' . $this->input->post('tempfilename'));
            if ($this->input->post('keyid'))
                $this->keymodel->deleteKey($this->input->post('keyid'), $this->session->userdata['id']);
            redirect($this->input->post('referer'));
        }

        $this->load->view('keys/edit', $this->data);
    }
    
    public function delete($key, $cbox=FALSE) {
        if (!$this->session->userdata('id')) exit;
        
        if ($this->input->post('ok')) {
            $projectID = $this->keymodel->getProjectID($key);
            
            $url = 'http://data.rbg.vic.gov.au/dev/keybase-ws/ws/key_delete';
            $post = array();
            $post['keybase_user_id'] = $this->session->userdata('id');
            $result = curl_delete($url, $key, $post, TRUE);
            redirect('projects/show/' . $projectID);
        }
        else {
            $this->data['cbox'] = $cbox;
            $this->data['key'] = $this->keymodel->getKey($key);
        }
        $this->load->view('keys/delete', $this->data);
    }
    
    private function _detectDelimiter($keyid, $tempfilename, $delimiter=FALSE) {
        $this->data['keyid'] = $keyid;
        $this->data['tempfilename'] = $tempfilename;
        $infile = fopen('uploads/' . $tempfilename, 'r');
        $linearray = array();
        while (!feof($infile)) {
            $linearray[] = fgets($infile);
        }
        
        if (!$delimiter) {
            $n = count($linearray);
            $i = 0;
            $numcols = array();
            while ($i < 10 && $i < $n) {
                $row = str_getcsv($linearray[$i], "\t");
                $numcols[] = count($row);
                $i++;
            }
            $sum = array_sum($numcols);
            $count = count($numcols);
            $delimiter = ($sum/$count > 2) ? 'tab' : 'comma';
        }
        
        $this->data['delimiter'] = $delimiter;
        $delimiter = ($delimiter == 'tab') ? "\t" : ",";
        
        $input_key = array();
        foreach ($linearray as $line) {
            if ($line) {
                $input_key[] = str_getcsv($line, $delimiter);
            }
        }
        
        return $input_key;
    }
    
    private function _checkForErrors($keyid, $tempfilename, $delimiter) {
        $this->load->library('ErrorCheckService');
        $result = $this->errorcheckservice->checkForErrors($keyid, $tempfilename, $delimiter);
        if ($result->errors || $result->warnings) {
            $this->data['tempfilename'] = $tempfilename;
            $this->data['keyid'] = $keyid;
            $this->data['delimiter'] = $delimiter;
            $this->data['error_key'] = $this->errorcheckservice->errorKeyHtml($result->leads, $result->errors, $result->warnings);
            $this->data['errors'] = $result->errors;
            $this->data['warnings'] = $result->warnings;
            return TRUE;
        }
        else {
            return FALSE;
        }
    }
    
}

/* End of file keys.php */
/* Location: ./controllers/keys.php */
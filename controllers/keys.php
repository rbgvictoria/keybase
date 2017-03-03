<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once 'keybase.php';

class Keys extends KeyBase {
    var $data;

    public function  __construct() {
        parent::__construct();
        $this->load->library('KeyService');
        $this->load->library('ProjectService');
        $this->load->library('SourceService');
        $this->load->library('UserService');
        $this->load->library('Citation');
        $this->output->enable_profiler(false);
    }
    
    public function index() {
        redirect(base_url());
    }
    
    public function show($id) {
        if (!$id) {
            redirect(site_url());
        }
        $meta = $this->keyservice->getKeyMetadata($id);
        $source = NULL;
        if ($meta->source_id) {
            $source = $this->sourceservice->getSource($meta->source_id);
        }
        $meta->source = $source;
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
        $this->data['js'][] = site_url() . autoVersion('js/jquery.keybase.source.js');
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
            $data = array('key_metadata' => json_encode($this->input->post()));
            $result = $this->keyservice->editKey($key, $data);
            redirect('keys/show/' . $result);
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
        $data = array();
        $data['key_metadata'] = json_encode($this->input->post('key_metadata'));
        $data['file_content'] = '@uploads/' . $this->input->post('tempfilename') . ';type=text/csv';
        $result = $this->keyservice->editKey($key, $data);
        unlink('uploads/' . $this->input->post('tempfilename'));
        redirect('keys/show/' . $result);
    }
    
    public function create($projectid=FALSE) {
        $this->data['js'][] = site_url() . autoVersion('js/jquery.keybase.source.js');
        $meta = new stdClass();
        $meta->project = new stdClass();
        $meta->project->project_id = ($projectid) ? $projectid : $this->input->post('project_id');
        
        $url = $this->ws_url() . 'ws/project_meta_get/' . $meta->project->project_id;
        $projectdata = $this->projectservice->getProjectMetadata($projectid);
        $meta->project->project_name = $projectdata->project_name;

        $this->data['key'] = $meta;
        $this->data['referer'] = ($this->input->post('referer')) ? $this->input->post('referer') : $_SERVER['HTTP_REFERER'];
        
        if (!isset($this->session->userdata['id']))
            redirect(site_url());
        
        if ($this->input->post('submit')) {
            if (!($this->input->post('key_title')  && $this->input->post('taxonomic_scope') 
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
            redirect($this->input->post('referer'));
        }

        $this->load->view('keys/edit', $this->data);
    }
    
    public function delete($key, $cbox=FALSE) {
        if (!$this->session->userdata('id')) exit;
        $url = $this->ws_url() . 'ws/key_meta_get/' .$key;
        $meta = $this->keyservice->getKeyMetadata($key);
        $this->data['cbox'] = $cbox;
        $this->data['key'] = $meta;
        if ($this->input->post('ok')) {
            $result = $this->keyservice->deleteKey($key);
            redirect('projects/show/' . $meta->project->project_id);
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
    
    public function export($keyid) {
        $this->load->library('ExportService');
        $format = 'json';
        if ($this->input->get('format')) {
            $format = $this->input->get('format');
        }
        
        switch ($format) {
            case 'json':
                $this->load->helper('json');
                $key = $this->exportservice->export($keyid, 'json');
                echo json_output(json_decode($key));
                break;

            case 'sdd':
                $key = $this->exportservice->export($keyid, 'sdd');
                header('Content-type: text/xml');
                echo $key;
                break;

            case 'lpxk':
                $key = $this->exportservice->export($keyid, 'lpxk');
                header('Content-type: text/xml');
                echo $key;
                break;
            
            case 'csv':
                $filename = 'keybase_export_' . $keyid . '_' . time() . '.csv';
                $key = $this->exportservice->export($keyid, 'csv');
                header('Content-type: text/csv');
                header('Content-disposition: attachment;filename=' . $filename);
                echo $key;
                break;
           
            case 'txt':
                $filename = 'keybase_export_' . $keyid . '_' . time() . '.txt';
                $key = $this->exportservice->export($keyid, 'txt');
                header('Content-type: text/plain');
                header('Content-disposition: attachment;filename=' . $filename);
                echo $key;
                break;
           
            default:
                break;
        }
        
    }
    
}

/* End of file keys.php */
/* Location: ./controllers/keys.php */
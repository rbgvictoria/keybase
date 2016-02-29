<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once 'keybase.php';

class Keys extends KeyBase {
    var $data;

    function  __construct() {
        parent::__construct();
        $this->load->model('keymodel');
    }
    
    function index() {
        redirect(base_url());
    }
    
    function show($key) {
        $this->load->model('playermodel');
        $this->data['js'][] = base_url() . 'js/jquery.keybase.keymenu.js?v=1.0';
        if (!$key) 
            redirect('key');
        $this->data['keyformat'] = 'about';
        $this->data['keyid'] = $key;
        $this->data['keyname'] = $this->playermodel->getKeyName($key);
        $this->data['citation'] = $this->keymodel->getCitation($key);
        $this->data['breadcrumbs'] = $this->playermodel->getBreadCrumbs($key);
        $this->data['infilter'] = $this->playermodel->keyInFilter($key);
        $this->data['project'] = $this->keymodel->getProjectDetails($key);
        $this->data['users'] = $this->keymodel->getProjectUsers($this->data['project']['ProjectsID']);
        $this->data['key'] = $this->keymodel->getKey($key);
        $this->data['taxa'] = $this->keymodel->getItems($key);
        if (isset($this->session->userdata['id'])) {
            $this->data['changes'] = $this->keymodel->getChanges($key);
        }
        $this->load->view('keys/show', $this->data);
    }
    
    public function search() {
        if (!$this->input->post('searchbox'))
            redirect($_SERVER['HTTP_REFERER']);
        
        $searchstring = $this->input->post('searchbox');
        $this->data['search_string'] = $searchstring;
        $this->data['search_result'] = $this->keymodel->getSearchResult($searchstring);
        
        $this->load->view('keys/search', $this->data);
    }

    public function edit($key=FALSE) {
        $this->data['js'][] = base_url() . 'js/jquery.keybase.editkey.js?v=1.0';
        $this->data['js'][] = base_url() . 'js/jquery.keybase.keymenu.js?v=1.0';
        
        if (!$key)
            $key = $this->input->post('keyid');
        if (!$key) 
            redirect('key');
        if (!isset($this->session->userdata['id']))
            redirect('keys/show/' . $key . '?tab=3');
        
        $projectid = $this->keymodel->getProjectID($key);
        $this->data['projectid'] = $projectid;
        $this->data['keyid'] = $key;
        
        if ($this->input->post('submit')) {
            $this->keymodel->editKeyMetadata($this->input->post(), $this->session->userdata['id']);
            if ($_FILES['delimitedtext']['tmp_name']) {
                $filename = $_FILES['delimitedtext']['tmp_name'];
                if ($filename) {
                    $this->load->model('lpxktokeybasemodel', 'lpxk');
                    $delimiter = $this->input->post('delimiter');
                    $tempfile = file_get_contents($_FILES['delimitedtext']['tmp_name']);
                    $tempfilename = uniqid();
                    file_put_contents('uploads/' . $tempfilename, $tempfile);
                    $this->data['input_key'] = $this->_detectDelimiter($key, $tempfilename);
                }
            }
            else redirect('keys/show/' . $key);
        }
        
        if ($this->input->post('submit2')) {
            $this->data['projectid'] = $this->input->post('projectid');
            $errors = $this->_checkForErrors($this->input->post('keyid'), 
                    $this->input->post('tempfilename'), $this->input->post('delimiter'));
            if ($errors) {
                $cbox = FALSE;
            }
            else {
                $this->load->model('lpxktokeybasemodel', 'lpxk');
                $this->lpxk->LpxkToKeybase($key, 'uploads/' . $this->input->post('tempfilename'), 
                        'delimitedtext', FALSE, $this->input->post('delimiter'), 
                        $this->session->userdata['id']);
                unlink('uploads/' . $this->input->post('tempfilename'));
                redirect('keys/show/' . $key);
            }
        }
        
        if ($this->input->post('submit3')) {
                $this->load->model('lpxktokeybasemodel', 'lpxk');
                $this->lpxk->LpxkToKeybase($key, 'uploads/' . $this->input->post('tempfilename'), 
                        'delimitedtext', FALSE, $this->input->post('delimiter'), 
                        $this->session->userdata['id']);
                unlink('uploads/' . $this->input->post('tempfilename'));
                redirect('keys/show/' . $key);
        }
        
        if ($this->input->post('cancel')) {
            if ($this->input->post('tempfilename') && file_exists('uploads/' . $this->input->post('tempfilename')))
                unlink ('uploads/' . $this->input->post('tempfilename'));
            redirect($this->input->post('referer'));
        }
        
        $this->data['key'] = $this->keymodel->getKey($key);
        $this->data['referer'] = ($this->input->post('referer')) ? $this->input->post('referer') : $_SERVER['HTTP_REFERER'];
        $this->load->view('keys/edit', $this->data);
    }
    
    public function create($projectid=FALSE) {
        $this->data['projectid'] = ($projectid) ? $projectid : $this->input->post('projectid');
        
        $projectdata = $this->keymodel->getProjectData($this->data['projectid']);
        $this->data['projectname'] = $projectdata['Name'];
        
        $this->data['js'][] = base_url() . 'js/jquery.keybase.editkey.js?v=1.0';
        if (!isset($this->session->userdata['id']))
            redirect('key');
        
        if ($this->input->post('submit')) {
            if (!($this->input->post('name')  && $this->input->post('taxonomicscope') 
                    && $this->input->post('geographicscope'))) {
                $this->data['message'][] = 'Please enter all required metadata (name, taxonomic scope, geographic scope).';
            }
            if (!($_FILES['loadfile']['tmp_name'] || $_FILES['delimitedtext']['tmp_name'] 
                    || $this->input->post('loadurl'))) {
                $this->data['message'][] = 'Please select a key file to upload.';
                echo 'Please select a key file to upload.';
            }
            if (isset($this->data['message'])) {
                $this->load->view('editkeyview', $this->data);
                return;
            }
            
            if ($this->input->post('keyid'))
                $keyid = $this->input->post('keyid');
            else
                $keyid = $this->keymodel->editKeyMetadata($this->input->post());

            if ($_FILES['loadfile']['tmp_name']) {
                $filename = $_FILES['loadfile']['tmp_name'];
            }
            elseif ($this->input->post('loadurl')){
                $filename = $this->input->post('loadurl');
            }
            elseif ($_FILES['delimitedtext']['tmp_name']) {
                $filename = $_FILES['delimitedtext']['tmp_name'];
            }
            if ($filename) {
                $this->load->model('lpxktokeybasemodel', 'lpxk');
                $delimiter = $this->input->post('delimiter');
                if ($this->input->post('loadurl') || $_FILES['loadfile']['tmp_name']) {
                    if ($this->input->post('loadurl') && $this->input->post('loadimages')) {
                        $this->lpxk->LpxkToKeyBase($keyid, $filename, 'lpxk', TRUE, FALSE, $this->session->userdata['id']);
                    }
                    else {
                        $this->lpxk->LpxkToKeyBase($keyid, $filename, 'lpxk', FALSE, FALSE, $this->session->userdata['id']);
                    }
                    redirect('keys/show/' . $keyid);
                }
                elseif ($_FILES['delimitedtext']['tmp_name']) {
                    $tempfile = file_get_contents($_FILES['delimitedtext']['tmp_name']);
                    $tempfilename = uniqid();
                    file_put_contents('uploads/' . $tempfilename, $tempfile);
                    $this->data['input_key'] = $this->_detectDelimiter($keyid, $tempfilename);
                    $this->data['keyid'] = $keyid;
                }
            }
        }
        
        if ($this->input->post('submit2')) {
            $errors = $this->_checkForErrors($this->input->post('keyid'), 
                    $this->input->post('tempfilename'), $this->input->post('delimiter'));
            if ($errors) {
                $cbox = FALSE;
            }
            else {
                $this->load->model('lpxktokeybasemodel', 'lpxk');
                $this->lpxk->LpxkToKeybase($this->input->post('keyid'), 'uploads/' . $this->input->post('tempfilename'), 
                        'delimitedtext', FALSE, $this->input->post('delimiter'), 
                        $this->session->userdata['id']);
                unlink('uploads/' . $this->input->post('tempfilename'));
                redirect('keys/show/' . $this->input->post('keyid'));
            }
        }
        
        if ($this->input->post('submit3')) {
            $keyid = $this->input->post('keyid');
            $this->load->model('lpxktokeybasemodel', 'lpxk');
            $this->lpxk->LpxkToKeybase($keyid, 'uploads/' . $this->input->post('tempfilename'), 
                    'delimitedtext', FALSE, $this->input->post('delimiter'), 
                    $this->session->userdata['id']);
            unlink('uploads/' . $this->input->post('tempfilename'));
            redirect('keys/show/' . $keyid);
        }
        
        if ($this->input->post('cancel')) {
            if ($this->input->post('tempfilename') && file_exists('uploads/' . $this->input->post('tempfilename')))
                unlink ('uploads/' . $this->input->post('tempfilename'));
            if ($this->input->post('keyid'))
                $this->keymodel->deleteKey($this->input->post('keyid'), $this->session->userdata['id']);
            redirect($this->input->post('referer'));
        }

        $this->data['referer'] = ($this->input->post('referer')) ? $this->input->post('referer') : $_SERVER['HTTP_REFERER'];
        $this->load->view('keys/edit', $this->data);
    }
    
    public function delete($key, $cbox=FALSE) {
        if (!isset($this->session->userdata['id'])) exit;
        
        if ($this->input->post('ok')) {
            $projectID = $this->keymodel->getProjectID($key);
            $this->keymodel->deleteKey($key, $this->session->userdata['id']);
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
        $this->data['tempfilename'] = $tempfilename;
        $this->data['keyid'] = $keyid;
        $this->data['delimiter'] = $delimiter;
        
        $delimiter = ($delimiter == 'tab') ? "\t" : ',';
        
        $infile = fopen('uploads/' . $tempfilename, 'r');
        $inkey = array();
        $this->fromnodes = array();
        $this->tonodes = array();
        while (!feof($infile)) {
            $row = fgetcsv($infile, 0, $delimiter);
            if ($row) {
                foreach ($row as $index => $value)
                    $row[$index] = trim($value);
                
                $inkey[] = $row;
                $this->fromnodes[] = $row[0];
                $this->tonodes[] = isset($row[2]) ? $row[2] : FALSE;
            }
        }
        
        $unique_nodes = array_unique($this->fromnodes);
        $unique_node_keys = array_keys($unique_nodes);
        
        $path = array();
        $this->numpaths = 0;
        $this->endnodes = array();
        $this->loops = array();
        
        $this->_traverseKey($path, $unique_nodes[0]);
        
        $errors = array();
        $warnings = array();
        $htmltable = array();
        $htmltable[] = '<table class="table table-bordered table-condensed">';
        foreach($inkey as $k => $row) {
            $numcols = count($row);
            $fromnode = array_search(array_search($row[0], $unique_nodes), $unique_node_keys);
            if (isset($row[2])) {
                $key = array_search($row[2], $unique_nodes);
                if ($key !== FALSE)
                    $tonode = array_search($key, $unique_node_keys);
                else $tonode = FALSE;
            }
            
            $htmltablerow = array();
            if ($numcols < 3) {
                $htmltablerow[] = '<tr class="too-few-columns">';
                $errors['too-few-columns'][] = $row;
            }
            else
                $htmltablerow[] = '<tr>';
            
            $key = array_search($row[0], $this->tonodes);
            
            if (count(array_keys($this->fromnodes, $row[0])) < 2) {
                $htmltablerow[] = '<td class="singleton-leads">' . $row[0] . '</td>';
                $errors['singleton-leads'][] = $row;
            }
            elseif (count(array_keys($this->fromnodes, $row[0])) > 2) {
                $htmltablerow[] = '<td class="polytomies">' . $row[0] . '</td>';
                $warnings['polytomies'][] = $row;
            }
            elseif ($key === FALSE && $fromnode != 0) {
                $htmltablerow[] = '<td class="orphan-couplets">' . $row[0] . '</td>';
                $errors['orphan-couplets'][] = $row;
            }
            else {
                $htmltablerow[] = '<td>' . $row[0] . '</td>';
            }
            
            if (isset($row[1]))
                $htmltablerow[] = '<td>' . $row[1] . '</td>';
            else
                $htmltablerow[] = '<td>&nbsp;</td>';
            
            if (isset($row[2])) {
                if ($tonode) {
                    if (isset($this->loops[$k])) {
                        $htmltablerow[] = '<td class="loops">' . $row[2] . '</td>';
                        $errors['loops'][] = $row;
                    }
                    elseif (count(array_keys($this->tonodes, $row[2])) > 1) {
                        $htmltablerow[] = '<td class="reticulations">' . $row[2] . '</td>';
                        $warnings['reticulations'][] = $row;
                    }
                    else
                        $htmltablerow[] = '<td>' . $row[2] . '</td>';
                }
                else {
                    if (is_numeric($row[2])) {
                        $htmltablerow[] = '<td class="dead-ends">' . $row[2] . '</td>';
                        $errors['dead-ends'][] = $row;
                    }
                    elseif (!(preg_match('/^[A-Z]{1,1}[a-z]+ {1,1}/', str_replace('×', '', $row[2])) || preg_match('/^[A-Z]{1,1}[a-z]+$/', str_replace('×', '', $row[2])))) {
                        $htmltablerow[] = '<td class="possible-dead-ends">' . $row[2] . '</td>';
                        $warnings['possible-dead-ends'][] = $row;
                    }
                    elseif (isset($this->endnodes[$k])) {
                        $htmltablerow[] = '<td class="endnode">' . $row[2] . '</td>';
                    }
                    else {
                        $htmltablerow[] = '<td class="will-not-key-out">' . $row[2] . '</td>';
                        $warnings['will-not-key-out'][] = $row;
                    }
                }
            }
            else
                $htmltablerow[] = '<td>&nbsp;</td>';
            
            $htmltablerow[] = '</tr>';
            $htmltable[] = implode('', $htmltablerow);
        }
        $htmltable[] = '</table>';
        $this->data['error_key'] = implode('', $htmltable);
        $this->data['errors'] = $errors;
        $this->data['warnings'] = $warnings;
        return ($errors || $warnings) ? TRUE : FALSE;
    }
    
    private function _traverseKey($path, $node) {
        $path[] = $node;
        $this->numpaths++;
        
        foreach (array_keys($this->fromnodes, $node) as $lead) {
            $goto = $this->tonodes[$lead];
            if ($goto) {
                if (in_array($goto, $this->fromnodes)) {
                    if (in_array($goto, $path)) {
                        $endpath = $path;
                        $endpath[] = $goto;
                        //echo implode('->', $endpath) . '<br/>';
                        $this->numpaths++;
                        $this->loops[$lead] = $goto;
                    }
                    else {
                        //echo implode('->', $path) . '<br/>';
                        $this->_traverseKey($path, $goto);
                    }
                }
                else {
                    $endpath = $path;
                    $endpath[] = $goto;
                    //echo implode('->', $endpath) . '<br/>';
                    $this->numpaths++;
                    $this->endnodes[$lead] = $goto;
                }
            }
        }
    }
    
    
}

/* End of file keys.php */
/* Location: ./controllers/keys.php */
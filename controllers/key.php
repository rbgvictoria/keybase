<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Key extends CI_Controller {

    var $data;

    function  __construct() {
        parent::__construct();
        $this->load->database();
        $this->load->library('session');
        $this->load->helper('url');
        $this->load->helper('form');
        $this->output->enable_profiler(FALSE);
        $this->load->model('keymodel');
        
        // Allow for custom style sheets and javascript
        $this->data['css'] = array();
        $this->data['js'] = array();
        $this->data['iehack'] = FALSE;
    }

    public function index($edit=FALSE) {
        $this->data['ProjectStats'] = $this->keymodel->getProjectStats();
        $keys = array();
        $taxa = array();
        foreach ($this->data['ProjectStats'] as $project) {
            $keys[] = $project['NumKeys'];
            $taxa[] = $project['NumTaxa'];
        }
        $this->data['NumKeys'] = array_sum($keys);
        $this->data['NumTaxa'] = array_sum($taxa);
        
        $this->data['staticcontent'] = $this->keymodel->getStaticContent('home');
        
        if ($edit == '_edit') {
            $this->data['css'][] = base_url() . 'css/ckeditor_styles.css';
            $this->data['js'][] = 'http://www.rbg.vic.gov.au/dbpages/lib/ckeditor/ckeditor.js';
            $this->data['js'][] = base_url() . 'js/ckeditor_customconfig.js';
            
            if ($this->input->post('submit')) {
                $this->keymodel->updateStaticContent($this->input->post());
                redirect(base_url());
            }
            $this->load->view('editview', $this->data);
        }
        else
            $this->load->view('homeview', $this->data);
    }
    
    public function uid($uid) {
        $keysid = $this->keymodel->getKeyByUID($uid);
        if ($keysid)
            redirect('key/nothophoenix/' . $keysid);
        else
            redirect(site_url());
    }
    
    public function search() {
        if (!$this->input->post('searchbox'))
            redirect($_SERVER['HTTP_REFERER']);
        
        $searchstring = $this->input->post('searchbox');
        $this->data['search_string'] = $searchstring;
        $this->data['search_result'] = $this->keymodel->getSearchResult($searchstring);
        
        
        $this->load->view('searchresultview', $this->data);
    }
    
    public function keys ($itemid=false) {
        $this->data['js'][] = base_url() . 'js/jquery.comparekeys.js';
        if ($itemid) {
            $this->data['ItemsID'] = $itemid;
            $this->data['ItemName'] = $this->keymodel->getItemName($itemid);
            $this->data['keys'] = $this->keymodel->getKeys($itemid);
            if (count($this->data['keys']) == 1) {
                redirect('key/nothophoenix/' . $this->data['keys'][0]['id']);
            }
        }    
        else {
            $this->data['ItemsID'] = FALSE;
            $this->data['ItemName'] = FALSE;
            $this->data['keys'] = $this->keymodel->getKeys();
        }
        $this->load->view('key_view', $this->data);
        
    }
    
    public function mykeys() {
        if (isset($this->session->userdata['id']))
            $this->data['keys'] = $this->keymodel->getMyKeys($this->session->userdata['id']);
        $this->load->view('mykeysview', $this->data);
    }
    
    public function projects() {
        $this->data['projects'] = $this->keymodel->getProjects();
        $this->load->view('projectsview', $this->data);
    }
    
    public function myprojects() {
        if (!$this->session->userdata['id'])
            redirect('key/projects');
        $this->data['projects'] = $this->keymodel->getMyProjects($this->session->userdata['id']);
        $this->load->view('myprojectsview', $this->data);
    }
    
    public function keydetail($key=FALSE) {
        $this->load->model('playermodel');
        $this->data['js'][] = base_url() . 'js/jquery.keybase.keymenu.js';
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
        $this->load->view('keydetail_view', $this->data);
    }
    
    public function project($project) {
        $this->load->model('projectmodel');
        $this->data['js'][] = base_url() . 'js/dynatree/jquery.dynatree.js';
        $this->data['js'][] = base_url() . 'js/jquery.keybase.project.js';
        $this->data['css'][] = base_url() . 'css/dynatree/skin/ui.dynatree.css';
        $this->data['js'][] = 'http://www.rbg.vic.gov.au/dbpages/lib/ckeditor/ckeditor.js';
        $this->data['js'][] = base_url() . 'js/ckeditor_customconfig.js';
        $this->data['js'][] = base_url() . 'js/medialize-jQuery-contextMenu/jquery.contextMenu.js';
        $this->data['css'][] = base_url() . 'js/medialize-jQuery-contextMenu/jquery.contextMenu.css';
        
        $this->data['infilter'] = $this->keymodel->projectInFilter($project);
        if (isset($this->session->userdata['GlobalFilter'])) {
            $this->data['filterkeys'] = $this->projectmodel->getFilterKeys($project);
            $taxa = $this->keymodel->getTaxa($project, $this->data['filterkeys']);
        }
        else {
            $taxa = $this->keymodel->getTaxa($project);
        }
        
        $this->data['projectid'] = $project;
        $this->data['project'] = $this->keymodel->getProjectData($project);
        
        $this->data['keys_hierarchy'] = $this->keymodel->getProjectKeysLinked($project);
        $this->data['keys_orphaned'] = $this->keymodel->getProjectKeysOrphaned($project);
        
        //$this->data['keys'] = $this->keymodel->getProjectKeys($project);
        $this->data['users'] = $this->keymodel->getProjectUsers($project);
        $this->load->view('projectview', $this->data);
    }
    
    public function update_hierarchy($projectid) {
        $this->load->model('projectmodel');

        if (isset($this->session->userdata['id']) && $this->projectmodel->IsProjectUser($projectid, $this->session->userdata['id']))
            $this->hierarchy ($projectid);
        redirect('key/project/' . $projectid);
        
    }
    
    public function addprojectuser($project) {
        if (!isset($this->session->userdata['id']))
            redirect('key/project/' . $project);
        $this->data['projectid'] = $project;
        $this->data['users'] = $this->keymodel->getUsers($project);
        
        if ($this->input->post('submit')) {
            if ($this->input->post('userid')) {
                $this->keymodel->addProjectUser($this->input->post());
                redirect('key/project/' . $project);
            }
            else
                $this->data['messages'][] = 'Please select a user.';
        }
        
        $this->load->view('addprojectuserview', $this->data);
    }

    public function nothophoenix($key, $node=null, $highestnode=null) {
        $this->output->enable_profiler(FALSE);
        $this->load->model('nothophoenixmodel', 'phoenix');
        $this->data['js'][] = base_url() . 'js/jquery.keypanel.js';
        $this->data['js'][] = base_url() . 'js/jquery.keybase.keymenu.js';
        $this->data['iehack'] = TRUE;
        
        $this->data['keyformat'] = 'player';
        $this->data['keyid'] = $key;
        $this->data['keyname'] = $this->phoenix->getKeyName($key);
        $this->data['project'] = $this->keymodel->getProjectDetails($key);
        $this->data['citation'] = $this->keymodel->getCitation($key);
        $this->data['breadcrumbs'] = $this->phoenix->getBreadCrumbs($key);
        
        $project = $this->phoenix->getProjectID($key);
        $this->data['infilter'] = $this->phoenix->keyInFilter($key);
        $this->phoenix->GlobalFilter($project, $key);
        $node = $this->phoenix->getNode($key, $node);
        $currentnode = $this->phoenix->getCurrentNode($node);
        
        $this->data['currentnode'] = $node;
        $this->data['parent'] = ($node) ? $this->phoenix->getParent($currentnode) : false;
        //$this->data['higherkey'] = $this->phoenix->getHigherKey();
        $this->data['higherkey'] = FALSE;

        // current node
        $this->data['node'] = $this->phoenix->getNextCouplet($node);

        // path
        if ($node)
            $this->data['path'] = $this->phoenix->getPath($key, $currentnode);

        
        $remaining = $this->phoenix->auxRemainingEntities($key, $currentnode);

        // included taxa
        $this->data['remaining'] = $this->phoenix->getRemainingEntities($key, $remaining);

        // discarded taxa
        $this->data['discarded'] = $this->phoenix->getDiscardedEntities($key, $remaining);

        $this->load->view('nothophoenix_view', $this->data);
    }
    
    public function bracketedkey($key) {
        $this->data['js'][] = base_url() . 'js/jquery.keybase.keymenu.js';
        $this->load->model('htmlkeymodel');
        //$this->data['js'][] = base_url() . 'js/jquery.bracketedkey.js';
        $this->data['keyformat'] = 'bracketed';
        $this->data['keyid'] = $key;
        $project = $this->htmlkeymodel->getProjectID($key);
        $this->data['infilter'] = $this->htmlkeymodel->keyInFilter($key);
        $this->htmlkeymodel->GlobalFilter($project, $key);
        $this->data['keyname'] = $this->htmlkeymodel->getKeyName($key);
        $this->data['project'] = $this->keymodel->getProjectDetails($key);
        $this->data['key'] = $this->htmlkeymodel->createBracketedKey($key, $this->data['project']['ProjectsID']);
        $this->data['citation'] = $this->keymodel->getCitation($key);
        $this->data['breadcrumbs'] = $this->htmlkeymodel->getBreadCrumbs($key);
        $this->load->view('bracketedkey_view', $this->data);
    }
    
    public function indentedkey($key) {
        $this->data['js'][] = base_url() . 'js/jquery.keybase.keymenu.js';
        $this->data['js'][] = base_url() . 'js/jquery.indentedkey.js';
        $this->data['js'][] = base_url() . 'js/jquery.indentedkey.js';
        $this->load->model('htmlkeymodel');
        $this->data['keyformat'] = 'indented';
        $this->data['keyid'] = $key;
        $project = $this->htmlkeymodel->getProjectID($key);
        $this->data['infilter'] = $this->htmlkeymodel->keyInFilter($key);
        $this->htmlkeymodel->GlobalFilter($project, $key);
        $this->data['keyname'] = $this->htmlkeymodel->getKeyName($key);
        $this->data['project'] = $this->keymodel->getProjectDetails($key);
        $this->data['key'] = $this->htmlkeymodel->createIndentedKey($key, $this->data['project']['ProjectsID'], 15);
        $this->data['citation'] = $this->keymodel->getCitation($key);
        $this->data['breadcrumbs'] = $this->htmlkeymodel->getBreadCrumbs($key);
        $this->load->view('indentedkey_view', $this->data);
    }
    
    public function editkey($key=FALSE, $cbox=FALSE) {
        $this->data['js'][] = base_url() . 'js/jquery.keybase.editkey.js';
        $this->data['js'][] = base_url() . 'js/jquery.keybase.keymenu.js';
        if (!$key) 
            redirect('key');
        if (!isset($this->session->userdata['id']))
            redirect('key/keydetail/' . $key);
        $this->data['keyid'] = $key;
        $this->data['key'] = $this->keymodel->getKey($key);
        
        if ($this->input->post('cancel')) {
            redirect($this->input->post('referer'));
        }
        
        if ($this->input->post('submit')) {
            //$this->keymodel->editKeyMetadata($this->input->post(), $this->session->userdata['id']);
            if ($_FILES['loadfile']['tmp_name']  ||
                    $this->input->post('loadurl') ||
                    $_FILES['delimitedtext']['tmp_name']) {
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
                            $this->lpxk->LpxkToKeyBase($key, $filename, 'lpxk', TRUE, FALSE, $this->session->userdata['id']);
                        }
                        else {
                            $this->lpxk->LpxkToKeyBase($key, $filename, 'lpxk', FALSE, FALSE, $this->session->userdata['id']);
                        }
                    }
                    elseif ($_FILES['delimitedtext']['tmp_name']) {
                        /*if ($delimiter && $this->input->post('name')) {
                            $this->lpxk->LpxkToKeybase($key, $filename, 'delimitedtext', FALSE, $delimiter, $this->session->userdata['id']);
                        }*/
                        $tempfile = file_get_contents($_FILES['delimitedtext']['tmp_name']);
                        $tempfilename = uniqid();
                        file_put_contents('uploads/' . $tempfilename, $tempfile);
                        $this->data['input_key'] = $this->detectDelimiter($key, $tempfilename);
                        $cbox = FALSE;
                    }
                }
                /*if (($filename || $this->input->post('taxonomicscope') != $this->input->post('taxonomicscope_old')) && !$this->input->post('skip_hierarchy')) {
                    $projectid = $this->keymodel->getProjectID($key);
                    $this->hierarchy($projectid);
                }*/
            }
            //redirect($this->input->post('referer'));
        }
        
        if ($this->input->post('submit2')) {
            $errors = $this->checkForErrors($this->input->post('keyid'), 
                    $this->input->post('tempfilename'), $this->input->post('delimiter'));
            if ($errors) {
                $this->data['errors'] = $errors;
                $cbox = FALSE;
            }
            else {
                $this->load->model('lpxktokeybasemodel', 'lpxk');
                $this->lpxk->LpxkToKeybase($key, 'uploads/' . $this->input->post('tempfilename'), 
                        'delimitedtext', FALSE, $this->input->post('delimiter'), 
                        $this->session->userdata['id']);
                unlink('uploads/' . $this->input->post('tempfilename'));
                redirect('key/nothophoenix/' . $key);
            }
        }
        
        if ($this->input->post('submit3')) {
                $this->load->model('lpxktokeybasemodel', 'lpxk');
                $this->lpxk->LpxkToKeybase($key, 'uploads/' . $this->input->post('tempfilename'), 
                        'delimitedtext', FALSE, $this->input->post('delimiter'), 
                        $this->session->userdata['id']);
                unlink('uploads/' . $this->input->post('tempfilename'));
                redirect('key/nothophoenix/' . $key);
        }
        
        if ($this->input->post('cancel')) {
            if (file_exists('uploads/' . $this->input->post('tempfilename')))
                unlink ('uploads/' . $this->input->post('tempfilename'));
            redirect($this->input->post('referer'));
        }
        
        $this->data['referer'] = $_SERVER['HTTP_REFERER'];
        $this->data['cbox'] = $cbox;
        $this->load->view('editkeyview', $this->data);
    }
    
    private function detectDelimiter($keyid, $tempfilename, $delimiter=FALSE) {
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
    
    private function checkForErrors($keyid, $tempfilename, $delimiter) {
        $this->data['tempfilename'] = $tempfilename;
        $this->data['keyid'] = $keyid;
        $this->data['delimiter'] = $delimiter;
        
        $delimiter = ($delimiter == 'tab') ? "\t" : ',';
        
        $infile = fopen('uploads/' . $tempfilename, 'r');
        $inkey = array();
        $fromnodes = array();
        $tonodes = array();
        while (!feof($infile)) {
            $row = fgetcsv($infile, 0, $delimiter);
            if ($row) {
                $inkey[] = $row;
                $fromnodes[] = $row[0];
                $tonodes[] = isset($row[2]) ? $row[2] : FALSE;
            }
        }
        
        $unique_nodes = array_unique($fromnodes);
        $unique_node_keys = array_keys($unique_nodes);
        
        $errors = array();
        $htmltable = array();
        $htmltable[] = '<table>';
        foreach($inkey as $row) {
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
            elseif ($tonode && $tonode == $fromnode) {
                $htmltablerow[] = '<tr class="definite-loop">';
                $errors['definite-loop'][] = $row;
            }
            elseif ($tonode && $tonode < $fromnode) {
                $htmltablerow[] = '<tr class="possible-loop">';
                $errors['possible-loop'][] = $row;
            }
            else
                $htmltablerow[] = '<tr>';
            
            $key = array_search($row[0], $tonodes);
            if ($key !== FALSE || $fromnode == 0) {
                $htmltablerow[] = '<td>' . $row[0] . '</td>';
            }
            else {
                $htmltablerow[] = '<td class="orphan">' . $row[0] . '</td>';
                $errors['orphan'][] = $row;
            }
            
            if (isset($row[1]))
                $htmltablerow[] = '<td>' . $row[1] . '</td>';
            else
                $htmltablerow[] = '<td>&nbsp;</td>';
            
            if (isset($row[2])) {
                if ($tonode) {
                    if (count(array_keys($tonodes, $row[2])) > 1) {
                        $htmltablerow[] = '<td class="reticulation">' . $row[2] . '</td>';
                        $errors['reticulation'][] = $row;
                    }
                    else
                        $htmltablerow[] = '<td>' . $row[2] . '</td>';
                }
                else 
                    $htmltablerow[] = '<td class="endnode">' . $row[2] . '</td>';
            }
            else
                $htmltablerow[] = '<td>&nbsp;</td>';
            
            $htmltablerow[] = '</tr>';
            $htmltable[] = implode('', $htmltablerow);
        }
        $htmltable[] = '</table>';
        $this->data['error_key'] = implode('', $htmltable);
        return $errors;
    }

    public function addKey($projectid=FALSE, $cbox=FALSE) {
        $this->data['projectid'] = $projectid;
        $this->data['cbox'] = $cbox;
        $this->data['js'][] = base_url() . 'js/jquery.keybase.editkey.js';
        if (!isset($this->session->userdata['id']))
            redirect('key');
        
        if ($this->input->post('cancel')) {
            redirect($this->input->post('referer'));
        }
        
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
            
            $key = $this->keymodel->editKeyMetadata($this->input->post());

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
                        $this->lpxk->LpxkToKeyBase($key, $filename, 'lpxk', TRUE, FALSE, $this->session->userdata['id']);
                    }
                    else {
                        $this->lpxk->LpxkToKeyBase($key, $filename, 'lpxk', FALSE, FALSE, $this->session->userdata['id']);
                    }
                }
                elseif ($_FILES['delimitedtext']['tmp_name']) {
                    if ($delimiter) {
                        $this->lpxk->LpxkToKeybase($key, $filename, 'delimitedtext', FALSE, $delimiter, $this->session->userdata['id']);
                    }
                }
                $this->hierarchy($projectid);
            }

            redirect('key/nothophoenix/' . $key);
        }
        
        $this->data['referer'] = $_SERVER['HTTP_REFERER'];
        $this->load->view('editkeyview', $this->data);
    }
    
    public function getinputkey($keyid, $tempfilename, $delimiter=FALSE) {
        $input_key = $this->detectDelimiter($keyid, $tempfilename, $delimiter);
        
        $maxcols = 0;
        foreach ($input_key as $row) {
            if (count($row) > $maxcols)
                $maxcols = count($row);
        }
        
        $table = array();
        $table[] = '<table class="detect-delimiter" width="100%">';
        foreach ($input_key as $row) {
            $table[] = '<tr>';
            foreach ($row as $cell) {
                $table[] = '<td>' . $cell . '</td>';
            }
            for ($i = count($row); $i < $maxcols; $i++) {
                $table[] = '<td>&nbsp;</td>';
            }
            $table[] = '</tr>';
        }
        $table[] = '</table>';
                
        echo implode('', $table);
    }
    
    public function hierarchy($projectid) {
        $this->load->model('keyhierarchymodel');
        $this->data['hierarchy'] = $this->keyhierarchymodel->getHierarchy($projectid);
    }
    
    public function addproject() {
        $this->data['js'][] = 'http://www.rbg.vic.gov.au/dbpages/lib/ckeditor/ckeditor.js';
        $this->data['js'][] = base_url() . 'js/jquery.keybase.editproject.js';
        if (!isset($this->session->userdata['id']))
            redirect('key');
        
        if ($this->input->post('submit')) {
            $projectid = $this->keymodel->addProject($this->input->post());
            redirect('key/project/' . $projectid);
        }
        
        $this->data['cbox'] = FALSE;
        $this->load->view('editprojectview', $this->data);
    }
    
    public function editproject($project=false, $cbox=false) {
        $this->data['js'][] = 'http://www.rbg.vic.gov.au/dbpages/lib/ckeditor/ckeditor.js';
        $this->data['js'][] = base_url() . 'js/jquery.keybase.editproject.js';
        $this->output->enable_profiler(false);
        if (!$project) {
            if (!isset($this->session->userdata['id']))
                redirect('key/projects');
            else
                redirect('key/myprojects');
        }
        if (!isset($this->session->userdata['id']))
            redirect("key/project/$project");
        
        if ($this->input->post('submit')) {
            $this->keymodel->editProject($this->input->post());
            redirect("key/project/$project");
        }
        
        if ($this->input->post('cancel')) {
            redirect("key/project/$project?tab=0");
        }
        
        $this->data['cbox'] = $cbox;
        
        $this->data['project'] = $this->keymodel->getProjectData($project);
        $this->load->view('editprojectview', $this->data);
    }
    
    public function export($format, $keyid) {
        $this->output->enable_profiler(FALSE);
        $this->load->model('exportmodel');
        $key = $this->exportmodel->export($keyid);
        //print_r($key);
        
        $this->load->library('export');
        
        if ($format == 'lpxk') {
            $lpxk = $this->export->exportToLpxk($key);
            header('Content-type: text/xml');
            echo $lpxk;
        }
        elseif ($format == 'csv') {
            $filename = $this->export->exportToCsv($key);
            $csv = file_get_contents('temp_out/' . $filename);
            header('Content-type: text/csv');
            header('Content-disposition: attachment;filename=' . $filename);
            echo $csv;
        }
        elseif ($format == 'txt') {
            $filename = $this->export->exportToCsv($key, 'tab');
            $csv = file_get_contents('temp_out/' . $filename);
            header('Content-type: text/plain');
            header('Content-disposition: attachment;filename=' . $filename);
            echo $csv;
        }
        elseif ($format == 'sdd') {
            $sdd = $this->export->exportToSdd($key);
            header('Content-type: text/xml');
            echo $sdd;
        }
    }
    
    public function compare($itemid) {
        $comp = $this->keymodel->compareKeys($itemid);
        $ret = array();
        $numkeys = count($comp[0]['keys']);
        
        $ret[] = '<table>';
        
        $row = array();
        $row[] = '<tr>';
        $row[] = '<th>Taxon name</th>';
        for ($i = 0; $i < $numkeys; $i++) {
            $colno = $i + 1;
            $row[] = '<th>' . $colno . '</th>';
        }
        $row[] = '</tr>';
        $ret[] = implode('', $row);
        
        foreach ($comp as $item) {
            $row = array();
            $row[] = '<tr>';
            $row[] = '<td>' . $item['Name'] . '</td>';
            foreach ($item['keys'] as $inkey) {
                if ($inkey)
                    $row[] = '<td>1</td>';
                else
                    $row[] = '<td>&nbsp;</td>';
            }
            $row[] = '</tr>';
            $ret[] = implode('', $row);
        }
        
        $ret[] = '</table>';
        echo implode("\n", $ret);
    }
    
    public function bulkupload() {
        if (!isset($this->session->userdata['id']))
            redirect('key');
        
        $this->data['projects'] = $this->keymodel->getMyProjects($this->session->userdata['id']);
        
        if ($this->input->post('submit')) {
            $this->load->model('lpxktokeybasemodel', 'lpxk');
            $path = 'temp_in/' . basename($_FILES['upload']['name'], '.zip');
            if (isset($_FILES['upload']) && $_FILES['upload']['tmp_name'])
                move_uploaded_file($_FILES['upload']['tmp_name'], $path . '.zip');
            
            $zip = new ZipArchive();
            $zip->open($path . '.zip');
            if (!file_exists($path))
                mkdir($path);
            $zip->extractTo($path);
            
            if (file_exists($path . '/sources.csv')) {
                $sources = array();
                $sourceids = array();
                $sourcesfile = fopen($path . '/sources.csv', 'r');
                
                $firstline = fgetcsv($sourcesfile);
                array_shift($firstline);
                
                while (!feof($sourcesfile)) {
                    $line = fgetcsv($sourcesfile);
                    if (!empty($line[0])) {
                        $source = array();
                        $id = array_shift($line);
                        foreach ($line as $index=>$value) {
                            $source[$firstline[$index]] = ($value) ? $value : NULL;
                        }
                        $sourceids[$id] = $this->keymodel->insertSource($source);
                    }
                }
                
            }
            
            $keyfile = fopen($path . '/keys.csv', 'r');
            $leadsfile = fopen($path . '/leads.csv', 'r');
            
            $keys = array();
            $ids = array();
            $firstline = TRUE;
            while (!feof($keyfile)) {
                $line = fgetcsv($keyfile);
                if ($firstline) {
                    $firstline = FALSE;
                    continue;
                }
                if ($line[1]) {
                    $keys[] = array(
                        'id' => $line[0],
                        'name' => $line[1],
                        'taxonomicscope' => $line[2],
                        'geographicscope' => $line[3],
                        'sourceid' => (!empty($line[4]) && isset($sourceids)) ? $sourceids[$line[4]] : NULL
                    );
                    $ids[] = $line[0];
                }
            }
            fclose($keyfile);
            
            $keyids = array();
            $leads = array();
            $firstline = TRUE;
            while (!feof($leadsfile)) {
                $line = fgetcsv($leadsfile);
                if ($firstline) {
                    $firstline = FALSE;
                    continue;
                }
                $keyids[] = $line[0];
                $leads[] = array(
                    'fromnode' => $line[1],
                    'leadtext' => $line[2],
                    'tonode' => $line[3],
                );
            }
            fclose($leadsfile);
            
            $keyidsfile = fopen($path . '/keyids.csv', 'w');
            
            foreach ($ids as $index=>$id) {
                
                $keymetadata = array(
                    'name' => $keys[$index]['name'],
                    'description' => NULL,
                    'taxonomicscope' => $keys[$index]['taxonomicscope'],
                    'geographicscope' => $keys[$index]['geographicscope'],
                    'sourceid' => $keys[$index]['sourceid'],
                    'projectid' => $this->input->post('project'),
                    'createdbyid' => $this->session->userdata['id'],
                    'authors' => FALSE,
                    'title' => FALSE,
                );
                $handle = fopen($path . '/key_' . $id . '.csv', 'w');
                
                $thiskeyids = array_keys($keyids, $id);
                
                foreach ($thiskeyids as $k) {
                    $row = array(
                        $leads[$k]['fromnode'],
                        $leads[$k]['leadtext'],
                        $leads[$k]['tonode'],
                    );
                    fputcsv($handle, $row);
                }
                fclose($handle);
                
                $keysid = $this->keymodel->editKeyMetadata($keymetadata);
                fputcsv($keyidsfile, array($id, $keysid));
                //$this->lpxk->LpxkToKeybase($keysid, $path . '/key_' . $id . '.csv', 'delimitedtext', FALSE, 'comma', 1);
            }
            
        redirect('key/project/' . $this->input->post('project'));
        }
        
        $this->load->view('bulkuploadview', $this->data);
    }
    
    public function getlsids() {
        set_time_limit(0);
        $taxa = $this->keymodel->getTaxaWithoutLSID();
        foreach ($taxa as $taxon) {
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, 'http://biodiversity.org.au/taxon/' . urlencode($taxon['Name']). '.xml');
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            $result = curl_exec($curl);
            
            file_put_contents('temp_in/curl/' . str_replace(' ', '_', $taxon['Name'] . '.xml'), $result);
            
            $doc = new DOMDocument('1.0', 'UTF-8');
            $doc->loadXML($result);
            $nodeList = $doc->getElementsByTagName('TaxonConcept');
            if ($nodeList->length) {
                $item = $nodeList->item(0);
                $lsid = $item->getAttribute('ibis:lsid');
                $this->keymodel->updateLSID($taxon['ItemsID'], $lsid);
            }
            
        }
    }
    
    public function deletekey($key, $cbox=FALSE) {
        if (!isset($this->session->userdata['id'])) exit;
        
        if ($this->input->post('ok')) {
            $this->keymodel->deleteKey($key, $this->session->userdata['id']);
            if (!$this->input->post('skip_hierarchy'))
                $this->hierarchy($this->input->post('projectid'));
            redirect('key/project/' . $this->input->post('projectid'));
        }
        else {
            $this->data['cbox'] = $cbox;
            $this->data['key'] = $this->keymodel->getKey($key);
        }
        $this->load->view('deletekeyview', $this->data);
    }
    
    public function deleteprojectuser($projectuserid) {
        if (isset($this->session->userdata['id'])) {
            $this->keymodel->deleteProjectUser($projectuserid, $this->session->userdata['id']);
        }
        if (isset($_SERVER['HTTP_REFERER']) && substr($_SERVER['HTTP_REFERER'], 0, strlen(base_url())) == base_url())
            redirect($_SERVER['HTTP_REFERER']);        
        else
            redirect('key');
        
    }
    
    public function filter() {
        $this->load->model('filtermodel');
        $this->data['js'][] = base_url() . 'js/dynatree/jquery.dynatree.js';
        $this->data['js'][] = base_url() . 'js/jquery.keybase.globalfilter.js';
        $this->data['css'][] = base_url() . 'css/dynatree/skin/ui.dynatree.css';
        
        $default = array('filterid');
        $uri = $this->uri->uri_to_assoc(3, $default);
        $this->data['filterid'] = $uri['filterid'];
        
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
        
        $this->load->view('filterview', $this->data);
    }
    
    public function importglobalfilter() {
        $this->output->enable_profiler(false);
        $this->load->model('filtermodel');
        if ($this->input->post('submit')) {
            if ($_FILES['file']['tmp_name']) {
                $filterid = $this->filtermodel->importGlobalFilter($_FILES['file']['tmp_name']);
                if ($filterid) {
                    redirect('key/filter/filterid/' . $filterid);
                }
                else {
                    redirect('key/filter');
                }
            }
            else {
                redirect('key/filter');
            }
        }
        
        $this->load->view('importglobalfilterview', $this->data);
    }
    
    private function update_filter() {
        if (!$this->input->post('taxa')) return FALSE;
        
        $taxa = preg_split("/[\r|\n]+/", trim($this->input->post('taxa')));
        foreach ($taxa as $key=>$value)
            $taxa[$key] = trim($value);

        $projects = $this->input->post('projects');
        if (!$projects[0]) $projects = FALSE;

        $this->filtermodel->findInKeyBase($taxa, $projects);

        $filterid = $this->filtermodel->getKeys($projects, $this->input->post('filter'), $this->input->post('filtername'));

        $this->data['itemsfound'] = $this->filtermodel->itemsFound();
        $this->data['itemsnotfound'] = $this->filtermodel->itemsNotFound();
        
        redirect('key/filter/' . $filterid);
    }
    
    public function filterkey($key) {
        $this->output->enable_profiler(FALSE);
        $this->load->model('filtermodel');
        //$this->data['js'][] = base_url() . 'js/jquery.keybase.localfilter.js';
        $this->data['key'] = $key;
        
        if (isset($this->session->userdata['LocalFilter']) && $this->session->userdata['LocalFilterKey']==$key && $this->session->userdata['LocalFilterOn']) {
            $filter = $this->filtermodel->getLocalFilterItems();
        }
        elseif (isset($this->session->userdata['GlobalFilterOn']) && $this->session->userdata['GlobalFilterOn']) {
            $filter = $this->filtermodel->retrieveFilterForKey($key);
        }
        else
            $filter = FALSE;
        
        $this->data['initems'] = $this->filtermodel->getInItems($key, $filter);
        if ($filter) {
            $this->data['outitems'] = $this->filtermodel->getOutItems($key, $filter);
        }
        $this->load->view('filterkeyview', $this->data);
    }
    
    public function localfilter($key) {
        $this->load->model('filtermodel');
        if ($this->input->post('filteritems')) {
            $this->filtermodel->setLocalFilter($key, $this->input->post('filteritems'));
        }
        else {
            $this->filtermodel->unsetLocalFilter();
        }
        redirect ($this->input->post('referer'));
        
    }
    
    public function st() {
        $uri = str_replace('key/st/', '', $this->uri->uri_string());
        $cleanuri = str_replace('/_edit', '', $uri);
        //$cleanuri = (substr($cleanuri, strlen($cleanuri)-1, 1) == '/') ? substr($cleanuri, 0, strlen($cleanuri)-1) : $cleanuri;
        
        if ($uri == 'key/st')
            $this->data['pages'] = $this->keymodel->getStaticPages();
        
        $this->data['staticcontent'] = $this->keymodel->getStaticContent($cleanuri);
        if (strpos($uri, '/_edit')) {
            if (isset($this->session->userdata['id']) && in_array($this->session->userdata('id'), array(1, 2))) {
                $this->data['js'][] = 'http://www.rbg.vic.gov.au/dbpages/lib/ckeditor/ckeditor.js';
                $this->data['js'][] = base_url() . 'js/ckeditor_customconfig.js';
                //$this->data['js'][] = base_url() . 'js/jquery.keybase.loadimage.js';

                if ($this->input->post('submit')) {
                    $this->keymodel->updateStaticContent($this->input->post());
                    redirect('/key/st/' . $cleanuri);
                }
                $this->load->view('editview', $this->data);
            }
        }
        if ($cleanuri == 'citation')
            $this->data['staticcontent']['PageContent'] = str_replace ('&lt;today&gt;', date('d-m-Y'), $this->data['staticcontent']['PageContent']);

        $this->load->view('staticview', $this->data);
    }
    
    public function createstaticpage() {
        if (!(isset($this->session->userdata['id']) && in_array($this->session->userdata('id'), array(1, 2)))) exit;
        if ($this->input->post('submit')) {
            $this->keymodel->createNewStaticPage($this->input->post());
            redirect('key/st/' . $this->input->post('uri') . '/_edit');
        }
        
        $this->load->view('createpage', $this->data);
    }
    
    public function toggle_globalfilter($toggle) {
        if ($toggle == 'off') {
            $this->session->unset_userdata('GlobalFilterOn');
            $this->session->set_userdata('GlobalFilterOn', 0);
        }
        else {
            $this->session->unset_userdata('GlobalFilterOn');
            $this->session->set_userdata('GlobalFilterOn', 1);
        }
        redirect($_SERVER['HTTP_REFERER']);
    }
    
    public function toggle_localfilter($toggle) {
        if ($toggle == 'off') {
            $this->session->unset_userdata('LocalFilterOn');
            $this->session->set_userdata('LocalFilterOn', 0);
        }
        else {
            $this->session->unset_userdata('LocalFilterOn');
            $this->session->set_userdata('LocalFilterOn', 1);
        }
        redirect($_SERVER['HTTP_REFERER']);
    }
    
    public function remove_globalfilter() {
        $unset = array(
            'GlobalFilter' => '',
            'GlobalFilterOn' => '',
        );
        $this->session->unset_userdata($unset);
        redirect($_SERVER['HTTP_REFERER']);
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
        $this->load->view('loadimageview', $this->data);
    }
    
}

/* End of file key.php */
/* Location: ./controllers/key.php */
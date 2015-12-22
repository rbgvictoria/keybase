<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class WS extends CI_Controller {
    var $data;

    public function  __construct() {
        parent::__construct();
        $this->load->database();
        $this->load->library('session');
        $this->load->helper('url');
        $this->load->helper('form');
        $this->output->enable_profiler(false);
        $this->load->model('keymodel');
        $this->load->model('webservicesmodel', 'ws');
        $this->data = array();
    }
    
    public function index() {
        $this->load->view('ws_view', $this->data);
    }
    
    public function projects() {
        $data = $this->ws->getProjectStats($this->input->get('project'));
        
        $prep = array();
        foreach ($data as $row) {
            $proj = array();
            $proj['project_id'] = $row['ProjectsID'];
            $proj['project_name'] = $row['ProjectName'];
            $proj['number_of_items'] = $row['NumTaxa'];
            $proj['number_of_keys'] = $row['NumKeys'];
            $proj['taxonomic_scope'] = (object) array(
                'id' => $row['TaxonomicScopeID'],
                'name' => $row['TaxonomicScope']
            );
            $proj['geographic_scope'] = $row['GeographicScope'];
            $proj['project_icon'] = $row['ProjectIcon'];
            $proj['first_key'] = (object) array(
                'id' => $row['FirstKeyID'],
                'name' => $row['FirstKeyName']
            );
            
            if ($this->input->get('project')) {
                if ($this->input->get('items') !== 'false') {
                    $proj['items'] = $this->projectItems($this->input->get('project'));
                }
                if ($this->input->get('keys') !== 'false') {
                    $proj['keys'] = $this->projectKeys($this->input->get('project'));
                }
            }
            
            $prep[] = (object) $proj;
        }
        
        $json = json_encode($prep);
        header('Content-type: application/json');
        if (isset($_GET['callback']) && $_GET['callback'])
            echo $_GET['callback'] . '(' . $json . ')';
        else
            echo $json;
    }
    
    private function projectItems($project) {
        $data = $this->ws->getProjectItems($project);
        $items = array();
        foreach ($data as $row) {
            $item = array();
            $item['id'] = $row->item_id;
            $item['name'] = $row->item_name;
            $items[] = (object) $item;
        }
        return $items;
    }
    
    private function projectKeys($project) {
        $this->load->model('projectmodel');
        $filter = false;
        /*if (isset($this->session->userdata['GlobalFilterOn']) && $this->session->userdata['GlobalFilterOn']) {
            $filter = $this->projectmodel->getFilterKeys($project);
        }*/
        /*elseif ($filterid) {
            $filter = $this->projectmodel->getFilterKeys($project, $filterid);
        }*/
        
        $data = $this->ws->getProjectKeys($project, $filter);
        $keys = array();
        foreach ($data as $row) {
            $key = array();
            $key['id'] = $row->KeysID;
            $key['parent_id'] = $row->ParentKeyID;
            $key['name'] = $row->Name;
            $key['taxonomic_scope'] = (object) array(
                'id' => $row->TaxonomicScopeID,
                'name' => $row->TaxonomicScope
            );
            $keys[] = (object) $key;
        }
        return $keys;
    }
    
    public function items() {
        if (empty($_GET['project']) && empty($_GET['key'])) exit;
        $data = $this->ws->ws_getItems($_GET);
        
        $type = (!empty($_GET['key'])) ? 'k' . $_GET['key'] : 'p' . $_GET['project'];
        $filename = "keybase_items_{$type}_" . date('Ymd_His');
        
        if ($data) {
            if (isset($_GET['format']) && $_GET['format'] == 'csv') {
                $csv = $this->arrayToCsv($data);
                header("Content-Disposition: attachment; filename=\"$filename.csv\"");
                header('Content-type: text/csv');
                echo implode("\n", $csv);
            }
            else {
                $out = array();
                if (!empty($_GET['key'])) {
                    $keydetails = $this->keymodel->getKey($_GET['key']);
                    $out['KeysID'] = $keydetails['KeysID'];
                    $out['KeyName'] = $keydetails['Name'];
                    $out['ProjectsID'] = $keydetails['ProjectsID'];
                    $out['ProjectName'] = $keydetails['ProjectName'];
                }
                elseif (!empty($_GET['project'])) {
                    $project = $this->keymodel->getProjectData($_GET['project']);
                    $out['ProjectsID'] = $project['ProjectsID'];
                    $out['ProjectName'] = $project['Name'];
                }
                
                if (!empty($_GET['pageSize']) && is_numeric($_GET['pageSize'])) {
                    $out['pageSize'] = (int) $_GET['pageSize'];
                    
                    if (!empty($_GET['page']) && is_numeric($_GET['page']) && $_GET['page'] > 1)
                        $out['page'] = (int) $_GET['page'];
                    else
                        $out['page'] = 1;
                }
                
                $out['numberOfItems'] = count($data);
                
                $out['TimestampDownloaded'] = date('Y-m-d H:i:s');
                $out['Items'] = $data;
                
                $json = json_encode((object) $out);
                header("Content-Disposition: inline; filename=\"$filename.json\"");
                header('Content-type: application/json');
                echo $json;
            }
        }
    }

    public function keys () {
        if (empty($_GET['project']) && empty($_GET['tscope'])) 
            exit;
        $data = $this->ws->ws_getKeys($_GET);
        
        $type = (!empty($_GET['tscope'])) ? $_GET['tscope'] : 'p' . $_GET['project'];
        $filename = "keybase_keys_{$type}_" . date('Ymd_His');
        
        if ($data) {
            if (isset($_GET['format']) && $_GET['format'] == 'csv') {
                $csv = $this->arrayToCsv($data);
                if (!isset($_GET['output']) || $_GET['output'] == 'attachment') {
                    header("Content-Disposition: attachment; filename=\"$filename.csv\"");
                    header('Content-type: text/csv');
                }
                echo implode("\n", $csv);
            }
            else {
                $out = array();
                if (!empty($_GET['project'])) {
                    $project = $this->keymodel->getProjectData($_GET['project']);
                    $out['ProjectsID'] = $project['ProjectsID'];
                    $out['ProjectName'] = $project['Name'];
                }
                if (!empty($_GET['tscope'])) {
                    $itemid = $this->keymodel->getItemID($_GET['tscope']);
                    $item = $this->keymodel->getItemInfo($itemid);
                    $out['ItemsID'] = $item->ItemsID;
                    $out['ItemName'] = $item->ItemName;
                    $out['ItemLSID'] = $item->ItemLSID;
                    $out['Scope'] = 'Key(s) to members of item';
                }
                
                if (!empty($_GET['pageSize']) && is_numeric($_GET['pageSize'])) {
                    $out['pageSize'] = (int) $_GET['pageSize'];
                    
                    if (!empty($_GET['page']) && is_numeric($_GET['page']) && $_GET['page'] > 1)
                        $out['page'] = (int) $_GET['page'];
                    else
                        $out['page'] = 1;
                }
                
                $out['numberOfKeys'] = count($data);
                
                $out['TimestampDownloaded'] = date('Y-m-d H:i:s');
                $out['Items'] = $data;
                
                $json = json_encode((object) $out);
                header("Content-Disposition: inline; filename=\"$filename.json\"");
                header('Content-type: application/json');
                echo $json;
            }
         
        }
    }
    
    public function filter() {
        if (empty($_GET['name']) && empty($_GET['id'])) exit;
        
        if (!empty($_GET['id']))
            $data = $this->ws->retrieveFilter('id', $_GET['id']);
        elseif (!empty($_GET['name']))
            $data = $this->ws->retrieveFilter('name', $_GET['name']);
        
        if (isset($_GET['format']) && $_GET['format'] == 'xml') {
            $doc = new SimpleXMLElement('<?xml version="1.0"?><Filter></Filter>');
            $this->array_to_xml($data, $doc);
            header('Content-type: text/xml');
            echo $doc->asXML();
        }
        else {
            $json = json_encode($data);
            header('Content-type: application/json');
            echo $json;
        }
    }
    
    public function keyJSON() {
        if (empty($_GET['key_id'])) {
            exit;
        }
        
        $data = $this->ws->getKey($_GET['key_id']);
        $data['project'] = (object) $this->ws->getProjectDetails($_GET['key_id']);
        $data['project']->project_icon = site_url() . 'images/projecticons/' . $data['project']->project_icon;
        $data['source'] = (object) $this->ws->getSource($_GET['key_id']);
        $data['source']->is_modified = ($data['source']->is_modified) ? true : false;
        $data['items'] = $this->ws->getKeyItems($_GET['key_id']);
        $data['first_step'] = $this->ws->getRootNode($_GET['key_id']);
        $data['leads'] = $this->ws->getLeads($_GET['key_id']);
        
        $json = json_encode($data);
        header('Access-Control-Allow-Origin: *');  
        header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type, Content-Range, Content-Disposition, Content-Description');
        header('Content-type: application/json');
        if (isset($_GET['callback']) && $_GET['callback'])
            echo $_GET['callback'] . '(' . $json . ')';
        else
            echo $json;
    }
    
    public function globalFilter() {
        if (empty($_GET['filter_id'])) {
            exit;
        }
        
        $data = $this->ws->globalFilter($_GET['filter_id']);
        $json = json_encode($data);
        header('Content-type: application/json');
        echo $json;
        
    }
    
    public function filterProjects() {
        if (isset($_GET['project_id'])) {
            $data = $this->ws->getFilterProjects($_GET['project_id']);
        }
        else {
            $data = $this->ws->getFilterProjects();
        }
        $json = json_encode($data);
        header('Content-type: application/json');
        echo $json;
    }

    
    private function arrayToCsv($data) {
        $csv = array();
        $csv[] = $this->arrayToCsvRow(array_keys((array) $data[0]), ',');
        foreach ($data as $row) {
            $csv[] = $this->arrayToCsvRow(array_values((array) $row), ',');
        }
        return $csv;
    }
    
    private function arrayToCsvRow( array &$fields, $delimiter = ';', $enclosure = '"', $encloseAll = false, $nullToMysqlNull = false ) {
        $delimiter_esc = preg_quote($delimiter, '/');
        $enclosure_esc = preg_quote($enclosure, '/');

        $output = array();
        foreach ( $fields as $field ) {
            if ($field === null && $nullToMysqlNull) {
                $output[] = 'NULL';
                continue;
            }

            // Enclose fields containing $delimiter, $enclosure or whitespace
            if ( $encloseAll || preg_match( "/(?:${delimiter_esc}|${enclosure_esc}|\s)/", $field ) ) {
                $output[] = $enclosure . str_replace($enclosure, $enclosure . $enclosure, $field) . $enclosure;
            }
            else {
                $output[] = $field;
            }
        }

        return implode( $delimiter, $output );
    }
    
    private function array_to_xml($array, $xml) {
        foreach($array as $key => $value) {
            if(is_array($value)) {
                if(!is_numeric($key)){
                    $subnode = $xml->addChild("$key");
                    $this->array_to_xml($value, $subnode);
                }
                else{
                    $this->array_to_xml($value, $xml);
                }
            }
            else {
                $xml->addChild("$key","$value");
            }
        }
    }
    
    
}

/* End of file ws.php */
/* Location: ./controllers/ws.php */
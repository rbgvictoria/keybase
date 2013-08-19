<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class WebServices extends CI_Controller {

    public function  __construct() {
        parent::__construct();
        $this->load->database();
        $this->load->library('session');
        $this->load->helper('url');
        $this->output->enable_profiler(FALSE);
        $this->load->model('keymodel');
        $this->load->model('webservicesmodel');
    }
    
    function getFilter() {
        if (empty($_GET['name']) && empty($_GET['id'])) exit;
        
        if (!empty($_GET['id']))
            $data = $this->webservicesmodel->retrieveFilter('id', $_GET['id']);
        elseif (!empty($_GET['name']))
            $data = $this->webservicesmodel->retrieveFilter('name', $_GET['name']);
        
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
    
    public function project_items() {
        if (empty($_GET['project']) && empty($_GET['key'])) exit;
        $data = $this->webservicesmodel->ws_getItems($_GET);
        
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
        $data = $this->webservicesmodel->ws_getKeys($_GET);
        
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

/* End of file webservices.php */
/* Location: ./controllers/webservices.php */